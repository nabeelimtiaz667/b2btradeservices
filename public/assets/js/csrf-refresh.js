/**
 * CI4's CSRF protection rotates the token on every successful verification
 * (Config\Security::$regenerate = true) -- a fresh hash is generated and
 * re-cookied after each request that passes the check, whether the
 * controller then succeeds or returns a business-logic error. A page that
 * embeds one token at load and never updates it breaks the moment a visitor
 * submits a second AJAX request without a full reload (a retry after a
 * validation error, or repeated inline edits like the admin leads table's
 * per-row stage/notes) -- the second request's stale token throws a raw
 * CSRF exception instead of a normal response.
 *
 * `app/Filters/CsrfTokenHeader.php` echoes the current, already-rotated
 * hash back on every single response as an X-CSRF-TOKEN header (readable by
 * JS on a same-origin request without any CORS exposure needed). This picks
 * that header off a fetch() Response and writes it into every
 * `csrf_test_name` hidden field on the page, so the next submission --
 * same form or a different one -- carries a token the server still
 * recognizes.
 *
 * Usage: fetch(...).then(bumpCsrfToken).then(r => r.json())...
 * Safe to call on every response, success or failure.
 *
 * Also caches the latest value on window.__csrfTokenValue, for the pages
 * with no hidden csrf_test_name field to update -- a handful of admin
 * dashboard screens (leads.php, popup-leads.php) build their POST body by
 * hand instead of submitting a <form>. Those pages seed this from
 * `<?= csrf_hash() ?>` inline at load, then read window.__csrfTokenValue
 * when building each request instead of embedding a fixed value.
 */
function bumpCsrfToken(response) {
    var token = response.headers.get('X-CSRF-TOKEN');
    if (token) {
        window.__csrfTokenValue = token;
        document.querySelectorAll('input[name="csrf_test_name"]').forEach(function (el) {
            el.value = token;
        });
    }
    return response;
}
