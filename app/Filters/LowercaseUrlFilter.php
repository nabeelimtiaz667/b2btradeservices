<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Canonicalizes any request path containing uppercase letters to its all-
 * lowercase form via a 301 redirect, so a dynamic URL segment (country
 * code, search keyword, slug) typed or linked in mixed case -- e.g.
 * /supplier-country/AD -- doesn't serve as a separate duplicate-content URL
 * from its lowercase canonical form (/supplier-country/ad). Static route
 * words are already case-sensitive in CI4's router (a wrong-case one 404s
 * before any filter runs), so this only ever fires for a route that
 * actually resolved.
 *
 * GET-only: redirecting a POST/PUT would silently drop the request body,
 * and 301 changes the method to GET on older HTTP/1.0 clients.
 *
 * Scoped to public-facing routes via this filter's 'except' list in
 * Config/Filters.php -- dashboard/admin/leads are behind login, not
 * indexed, and leads/detail/{uid} intentionally uses uppercase UIDs
 * (S-002028 etc.), so those are deliberately left untouched.
 */
class LowercaseUrlFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if ($request->getMethod() !== 'GET') {
            return;
        }

        $uri       = $request->getUri();
        $routePath = $uri->getRoutePath();

        if ($routePath === strtolower($routePath)) {
            return;
        }

        // $uri is a SiteURI (CI4 4.4+), whose setPath() is overridden to
        // treat its argument as a *route* path (no base-path/subfolder
        // prefix) and re-derive the full path from that -- it is NOT a
        // plain "replace the path string" setter. Feeding it getPath()'s
        // result (which already includes the base path) double-prefixed
        // the redirect target; getRoutePath() is the correct round-trip
        // input. Casting the clone to string via __toString() then
        // reassembles scheme/authority/full-path/query correctly on its
        // own -- no manual query-string handling needed.
        $target = (clone $uri)->setPath(strtolower($routePath));

        return redirect()->to((string) $target, 301);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
