<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/**
 * Per-IP rate limiting on public-facing form submissions, using CI4's
 * built-in token-bucket Throttler (file-cache backed, same as everything
 * else in this app -- no Redis/Memcached dependency).
 *
 * Route filter argument is 'name,capacity,seconds', e.g. 'ratelimit:login,5,60'
 * = 5 submissions per 60 seconds per IP. Each name gets its own bucket, so
 * unrelated forms never share a counter even from the same visitor.
 *
 * The capacity is only a default: the "Site Security" admin page
 * (dashboard/security) lets an admin override "N" per form, without a
 * deploy. Overrides live in app/Data/rate_limits.php -- a plain PHP array,
 * same pattern as app/Data/countries.php -- rather than the database:
 * this filter runs on every hit to a public form, including the ones a
 * brute-force attempt makes on purpose to get blocked, so the lookup needs
 * to be a `require` of an opcache-able file, not a query that would hit the
 * DB on every single attempt (defeating the point of rate limiting DB-heavy
 * forms like login). An admin changes this rarely, so a file fits -- see
 * DECISIONS.md #15's reasoning for countries.php, which is the same trade.
 * The window (seconds) is not admin-editable -- see that page.
 */
class RateLimitFilter implements FilterInterface
{
    public const DATA_FILE = APPPATH . 'Data/rate_limits.php';

    /**
     * Single source of truth for every rate-limited form: name (matches the
     * route filter's first argument and the rate_limits.php key), label
     * (shown on the Site Security page), window in seconds (fixed, not
     * admin-editable) and the default capacity (used until an admin
     * overrides it, and what Routes.php's filter argument should always
     * match -- see TestRateLimit's drift check).
     *
     * @return array<string, array{label: string, window: int, default: int}>
     */
    public static function forms(): array
    {
        return [
            'login'           => ['label' => 'Login',                        'window' => 60,  'default' => 8],
            'register'        => ['label' => 'Register',                     'window' => 300, 'default' => 5],
            'forgot_password' => ['label' => 'Forgot Password',              'window' => 300, 'default' => 3],
            'reset_password'  => ['label' => 'Reset Password',               'window' => 300, 'default' => 5],
            'contact'         => ['label' => 'Contact Us',                   'window' => 300, 'default' => 5],
            'contact_ajax'    => ['label' => 'Contact Us (popup forms)',     'window' => 300, 'default' => 5],
            'post_rfq'        => ['label' => 'Post RFQ',                     'window' => 300, 'default' => 5],
            'lead_capture'    => ['label' => 'Lead Capture Popup',           'window' => 300, 'default' => 8],
            'lead_complete'   => ['label' => 'Complete Signup (lead link)',  'window' => 300, 'default' => 5],
        ];
    }

    /**
     * Admin-set overrides, name => capacity. Deliberately NOT cached in a
     * static/local variable across requests: under PHP-FPM a worker process
     * serves many requests over its lifetime, and a process-lifetime cache
     * would mean an admin's save on the Site Security page silently doesn't
     * take effect until that worker happens to recycle. A plain `require`
     * of a small array file is already cheap -- opcache keeps the compiled
     * bytecode and revalidates it against the file's mtime by default, so
     * this picks up a save on the very next request without needing its
     * own cache layer.
     *
     * @return array<string, int>
     */
    public static function overrides(): array
    {
        return is_file(self::DATA_FILE) ? (require self::DATA_FILE) : [];
    }

    /**
     * Atomically writes the admin-set overrides -- same temp-file-plus-
     * rename approach as CountrySyncer::writeAtomic(), so a reader never
     * sees a half-written file.
     *
     * @param array<string, int> $overrides
     */
    public static function saveOverrides(array $overrides): void
    {
        $export = var_export($overrides, true);
        $php = "<?php\n\n"
            . "// Written by the \"Site Security\" admin page (Dashboard::updateSiteSecurity())\n"
            . "// on " . date('Y-m-d H:i:s') . " -- do not edit by hand, use the page instead.\n"
            . "// name => capacity (\"N\"). See RateLimitFilter::forms() for defaults and windows.\n"
            . "return " . $export . ";\n";

        $tmpPath = self::DATA_FILE . '.tmp';
        file_put_contents($tmpPath, $php, LOCK_EX);
        rename($tmpPath, self::DATA_FILE); // atomic on both POSIX and Windows NTFS
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $arguments = $arguments ?? [];
        $name     = $arguments[0] ?? 'default';
        $capacity = (int) ($arguments[1] ?? 10);
        $seconds  = (int) ($arguments[2] ?? 60);

        $overrides = self::overrides();
        if (isset($overrides[$name]) && (int) $overrides[$name] > 0) {
            $capacity = (int) $overrides[$name];
        }

        $throttler = Services::throttler();
        // Cache keys can't contain {}()/\@: -- an IPv6 address (e.g. "::1" on
        // local dev) does, so hash it rather than trying to strip characters.
        $key = 'form_' . $name . '_' . md5($request->getIPAddress());

        if ($throttler->check($key, $capacity, $seconds) === false) {
            $message = 'Too many attempts. Please wait a minute and try again.';

            if ($request->isAJAX()) {
                return Services::response()
                    ->setStatusCode(429)
                    ->setJSON(['status' => 'error', 'message' => $message]);
            }

            return redirect()->back()->withInput()->with('error', $message);
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
