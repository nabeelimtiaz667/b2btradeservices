<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Echoes the current CSRF hash back as a response header on every request.
 *
 * CI4's CSRF protection rotates the token after each successful
 * verification (Config\Security::$regenerate = true), so any AJAX flow
 * that can submit more than once without a full page reload needs a way to
 * learn the new value -- the CSRF cookie itself is httponly, unreadable
 * from JS. See public/assets/js/csrf-refresh.js's bumpCsrfToken(), which
 * reads this header and updates the page's hidden csrf_test_name fields.
 *
 * Applied globally (cheap -- one header read, no DB/session work) rather
 * than only on known AJAX routes, so it never needs updating when a new
 * AJAX endpoint is added.
 */
class CsrfTokenHeader implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (function_exists('csrf_hash')) {
            $response->setHeader(csrf_header(), csrf_hash());
        }

        return $response;
    }
}
