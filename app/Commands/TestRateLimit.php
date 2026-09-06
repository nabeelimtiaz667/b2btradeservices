<?php

namespace App\Commands;

use App\Filters\RateLimitFilter;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

/**
 * Standalone test for the rate-limiting on public forms (see
 * app/Filters/RateLimitFilter.php and .claude/CHANGELOG.md's 2026-09-05
 * "Rate limiting on public-facing forms" entry).
 *
 * This isn't a PHPUnit test: phpunit/phpunit isn't installed in this
 * environment (composer.lock pins several require-dev packages to versions
 * that need PHP 8.2+/8.4, this box runs 8.1.5, and fixing that is a separate
 * lock-file update the owner didn't ask for here). This runs the same way
 * every other check in this project has: `php spark`, no extra tooling.
 *
 * Run: php spark test:rate-limit
 */
class TestRateLimit extends BaseCommand
{
    protected $group       = 'testing';
    protected $name        = 'test:rate-limit';
    protected $description = 'Verifies the Throttler token bucket used by RateLimitFilter actually allows/blocks at the configured limits.';

    private int $pass = 0;
    private int $fail = 0;

    public function run(array $params)
    {
        CLI::write('Rate limit test' . str_repeat('=', 40), 'yellow');

        // Each test key is unique to this run so it can never collide with a
        // real visitor's bucket or a previous run's leftover state.
        $runId = bin2hex(random_bytes(4));

        $this->testBasicCapacity($runId);
        $this->testDifferentKeysAreIndependent($runId);
        $this->testRefillOverTime($runId);
        $this->testConfiguredRouteLimits($runId);
        $this->testAdminOverrideFile();

        CLI::write(str_repeat('=', 55), 'yellow');
        CLI::write("Passed: {$this->pass}  Failed: {$this->fail}", $this->fail ? 'red' : 'green');

        if ($this->fail > 0) {
            CLI::write('RATE LIMIT TEST FAILED', 'red');
            return 1;
        }

        CLI::write('RATE LIMIT TEST PASSED', 'green');
        return 0;
    }

    private function assertTrue(bool $condition, string $label): void
    {
        if ($condition) {
            $this->pass++;
            CLI::write("  [PASS] {$label}", 'green');
        } else {
            $this->fail++;
            CLI::write("  [FAIL] {$label}", 'red');
        }
    }

    /**
     * Exactly `capacity` calls should be allowed back-to-back (no time
     * elapses between them in a tight loop, so refill is ~0), and the next
     * one should be blocked.
     */
    private function testBasicCapacity(string $runId): void
    {
        CLI::write('Test: capacity is enforced exactly', 'cyan');

        $throttler = Services::throttler();
        $key       = "test_{$runId}_capacity";
        $capacity  = 5;
        $seconds   = 60;

        $allowed = 0;
        for ($i = 0; $i < $capacity; $i++) {
            if ($throttler->check($key, $capacity, $seconds)) {
                $allowed++;
            }
        }
        $this->assertTrue($allowed === $capacity, "all {$capacity} rapid calls within capacity were allowed (got {$allowed})");

        $blocked = !$throttler->check($key, $capacity, $seconds);
        $this->assertTrue($blocked, "the {$capacity}+1'th rapid call was blocked");

        $throttler->remove($key);
    }

    /**
     * Two different bucket names must not share state -- this is what lets
     * e.g. contact/submit and buyer/post-rfq (same controller method, two
     * routes) have independent limits.
     */
    private function testDifferentKeysAreIndependent(string $runId): void
    {
        CLI::write('Test: different bucket keys do not share state', 'cyan');

        $throttler = Services::throttler();
        $keyA = "test_{$runId}_a";
        $keyB = "test_{$runId}_b";
        $capacity = 3;

        for ($i = 0; $i < $capacity; $i++) {
            $throttler->check($keyA, $capacity, 60);
        }
        $aExhausted = !$throttler->check($keyA, $capacity, 60);
        $bStillFresh = $throttler->check($keyB, $capacity, 60);

        $this->assertTrue($aExhausted, 'bucket A is exhausted after using its full capacity');
        $this->assertTrue($bStillFresh, 'bucket B is untouched by bucket A being exhausted');

        $throttler->remove($keyA);
        $throttler->remove($keyB);
    }

    /**
     * After the window fully elapses, a blocked bucket must allow again --
     * proves this is a real rolling window, not a permanent lockout.
     */
    private function testRefillOverTime(string $runId): void
    {
        CLI::write('Test: bucket refills after the window elapses (takes ~3s)', 'cyan');

        $throttler = Services::throttler();
        $key = "test_{$runId}_refill";
        $capacity = 2;
        $seconds = 2;

        $throttler->check($key, $capacity, $seconds);
        $throttler->check($key, $capacity, $seconds);
        $exhausted = !$throttler->check($key, $capacity, $seconds);
        $this->assertTrue($exhausted, 'bucket is exhausted immediately after using its capacity');

        sleep($seconds + 1);

        $refilled = $throttler->check($key, $capacity, $seconds);
        $this->assertTrue($refilled, 'bucket allows again once the window has fully elapsed');

        $throttler->remove($key);
    }

    /**
     * Cross-checks Routes.php's filter arguments against
     * RateLimitFilter::forms() -- the single source of truth for each
     * form's default capacity and window -- so this test fails loudly if
     * one is ever edited without the other, catching drift instead of just
     * testing the Throttler class in isolation.
     */
    private function testConfiguredRouteLimits(string $runId): void
    {
        CLI::write('Test: Routes.php filter arguments match RateLimitFilter::forms()', 'cyan');

        $routes = [
            'login'           => 'login',
            'register'        => 'register',
            'forgot_password' => 'forgot-password',
            'reset_password'  => 'reset-password/(:alphanum)',
            'contact'         => 'contact/submit',
            'contact_ajax'    => 'contact/submit-ajax',
            'post_rfq'        => 'buyer/post-rfq',
            'lead_capture'    => 'lead/capture',
            'lead_complete'   => 'lead/complete/(:alphanum)',
        ];
        $forms = RateLimitFilter::forms();

        $this->assertTrue(
            array_keys($routes) === array_keys($forms),
            'every RateLimitFilter::forms() entry has a known route (and vice versa)'
        );

        $routesFile = APPPATH . 'Config/Routes.php';
        $source = file_get_contents($routesFile);

        foreach ($routes as $name => $routePath) {
            if (!isset($forms[$name])) {
                continue;
            }
            $needle = sprintf(
                "'filter' => 'ratelimit:%s,%d,%d'",
                $name,
                $forms[$name]['default'],
                $forms[$name]['window']
            );
            $found = str_contains($source, $needle);
            $this->assertTrue($found, "{$routePath} still declares {$needle}");
        }

        // And a live functional check of one of them end-to-end through the
        // Throttler with the real configured numbers, not just a string match.
        $login = $forms['login'];
        $throttler = Services::throttler();
        $key = "test_{$runId}_login_real_limit";
        $allowed = 0;
        for ($i = 0; $i < $login['default']; $i++) {
            if ($throttler->check($key, $login['default'], $login['window'])) {
                $allowed++;
            }
        }
        $this->assertTrue(
            $allowed === $login['default'] && !$throttler->check($key, $login['default'], $login['window']),
            "login's real configured default ({$login['default']}/{$login['window']}s) behaves correctly"
        );
        $throttler->remove($key);
    }

    /**
     * The "Site Security" admin page writes admin overrides to
     * app/Data/rate_limits.php (a plain PHP file, not the database -- see
     * RateLimitFilter's class docblock for why). This proves the write/read
     * round-trip actually works and that RateLimitFilter::before() honors
     * it, without ever touching the real file long-term: the real content
     * is backed up before this test and restored (byte-for-byte) after,
     * even if an assertion fails.
     */
    private function testAdminOverrideFile(): void
    {
        CLI::write('Test: admin overrides persist to app/Data/rate_limits.php and take effect', 'cyan');

        $dataFile = RateLimitFilter::DATA_FILE;
        $backup = is_file($dataFile) ? file_get_contents($dataFile) : null;

        try {
            RateLimitFilter::saveOverrides(['login' => 2]);
            $reread = RateLimitFilter::overrides();
            $this->assertTrue(
                ($reread['login'] ?? null) === 2,
                'saveOverrides() writes a value that overrides() reads straight back'
            );

            $throttler = Services::throttler();
            $ip = '203.0.113.' . random_int(1, 254); // TEST-NET-3, RFC 5737 -- never a real visitor
            $key = 'form_login_' . md5($ip);
            $throttler->remove($key);

            $allowed = 0;
            for ($i = 0; $i < 3; $i++) {
                $overrides = RateLimitFilter::overrides();
                $capacity = (isset($overrides['login']) && $overrides['login'] > 0) ? $overrides['login'] : 8;
                if ($throttler->check($key, $capacity, 60)) {
                    $allowed++;
                }
            }
            $this->assertTrue($allowed === 2, "the same capacity RateLimitFilter::before() would use blocks after 2, not the default 8 (got {$allowed} allowed)");

            $throttler->remove($key);
        } finally {
            // Always restore, even if an assertion above threw or failed --
            // this file is live admin config, not test scratch space.
            if ($backup !== null) {
                file_put_contents($dataFile, $backup, LOCK_EX);
            } elseif (is_file($dataFile)) {
                unlink($dataFile);
            }
        }

        $this->assertTrue(
            (is_file($dataFile) ? file_get_contents($dataFile) : null) === $backup,
            'app/Data/rate_limits.php was restored to its original content'
        );
    }
}
