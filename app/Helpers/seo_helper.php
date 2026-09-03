<?php

if (! function_exists('canonical_self_url')) {
    /**
     * Canonical URL for the current request, including its query string.
     *
     * current_url() alone strips the query string, which is correct for
     * static pages (protects against a tracking-param URL like ?utm_source=x
     * being treated as separate content) but wrong for search/filter pages
     * where the query *is* the content -- canonicalizing /buyer/search?q=rice
     * to the bare /buyer/search would point at a different, near-empty page
     * instead of the page actually being viewed.
     */
    function canonical_self_url(): string
    {
        $query = service('request')->getUri()->getQuery();

        return current_url() . ($query !== '' ? '?' . $query : '');
    }
}

if (! function_exists('search_slug_encode')) {
    /**
     * Turn a raw search keyword into a URL-safe path segment.
     *
     * Deliberately the same transform as inquiry/product/category slugs
     * (url_title(strtolower(...), '-', true)) so the whole site slugifies
     * consistently. This is lossy -- punctuation is stripped -- which is why
     * search_slug_decode() below is a *reversal of intent*, not of bytes: a
     * search for "50%" becomes /search/50, and reading that segment back
     * only ever recovers "50". That is an accepted trade-off for a clean,
     * shareable URL, matching the trade-off already made for inquiry titles.
     */
    function search_slug_encode(string $keyword): string
    {
        helper('url');

        return url_title(strtolower(trim($keyword)), '-', true);
    }
}

if (! function_exists('search_slug_decode')) {
    /**
     * Turn a URL keyword segment back into a string usable in a LIKE query.
     * Hyphens become spaces; this is the inverse of search_slug_encode() only
     * in intent, not bytes -- see that function's docblock.
     */
    function search_slug_decode(string $segment): string
    {
        return trim(str_replace('-', ' ', $segment));
    }
}

if (! function_exists('parse_search_path')) {
    /**
     * Parse a search route's trailing path into a keyword plus labeled
     * filters, e.g. "steel-plates/country/us/date/2026-01-01" becomes
     * ['keyword' => 'steel-plates', 'filters' => ['country' => 'us', 'date' =>
     * '2026-01-01']].
     *
     * The first segment is treated as the keyword UNLESS it is itself a known
     * filter key, which lets a filter-only URL like "country/us" (no keyword)
     * parse correctly with no separate route needed. The one accepted
     * ambiguity: a keyword that is itself exactly "country", "category",
     * "date", "membership" or "type" cannot be distinguished from that filter
     * key. Narrow, deliberate trade-off for a route scheme with no fixed
     * segment count.
     *
     * $knownKeys must list every filter this route's controller understands;
     * an unrecognised key/value pair is silently dropped rather than passed
     * through, so a malformed URL degrades to "ignore the junk" rather than a
     * database error.
     */
    function parse_search_path(?string $path, array $knownKeys): array
    {
        $segments = array_values(array_filter(
            explode('/', trim((string) $path, '/')),
            static fn ($s) => $s !== ''
        ));

        if ($segments === []) {
            return ['keyword' => null, 'filters' => []];
        }

        $keyword = null;
        $i       = 0;

        if (! in_array($segments[0], $knownKeys, true)) {
            $keyword = search_slug_decode($segments[0]);
            $i       = 1;
        }

        $filters = [];
        $count   = count($segments);

        for (; $i + 1 < $count; $i += 2) {
            if (in_array($segments[$i], $knownKeys, true)) {
                $filters[$segments[$i]] = $segments[$i + 1];
            }
        }

        return ['keyword' => $keyword, 'filters' => $filters];
    }
}

if (! function_exists('build_search_path')) {
    /**
     * Inverse of parse_search_path(): build the clean path segment string
     * from a keyword and an ordered [key => value] filter list. Empty/null
     * filter values are omitted, so a partially-filled filter set still
     * produces a clean URL rather than an empty "country/" segment pair.
     */
    function build_search_path(?string $keyword, array $filters): string
    {
        $parts = [];

        if (! empty($keyword)) {
            $parts[] = search_slug_encode($keyword);
        }

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $parts[] = $key;
            $parts[] = $value;
        }

        return implode('/', $parts);
    }
}

if (! function_exists('build_search_pager')) {
    /**
     * Clean-URL pagination data for the search()-family pages, using the
     * same segment convention as parse_search_path()/build_search_path()
     * above (.../search/{keyword}/category/{slug}/page/{n}) instead of a
     * query string -- so a pagination link never trips the legacy
     * ?q=...&category=... redirect branch in each search() method, and
     * paginated results stay canonical/crawlable path URLs like every other
     * clean URL on the site.
     *
     * Page 1 omits the 'page' segment entirely rather than emitting an
     * explicit .../page/1, so the first page only ever has one URL form.
     *
     * $filters must already be in the fixed, controller-declared order
     * (e.g. ['category' => ...] for products) -- build_search_path() just
     * emits key/value pairs in iteration order, it doesn't know a canonical
     * order, so the caller is responsible for it.
     */
    function build_search_pager(string $basePath, ?string $keyword, array $filters, int $currentPage, int $totalPages, int $surround = 2): array
    {
        unset($filters['page']);

        $urlFor = static function (int $page) use ($basePath, $keyword, $filters): string {
            $pageFilters = $filters;
            $pageFilters['page'] = $page > 1 ? (string) $page : null;
            $clean = build_search_path($keyword, $pageFilters);

            return base_url($basePath . ($clean !== '' ? '/' . $clean : ''));
        };

        $links = [];
        $start = max(1, $currentPage - $surround);
        $end   = min($totalPages, $currentPage + $surround);
        for ($p = $start; $p <= $end; $p++) {
            $links[] = ['page' => $p, 'url' => $urlFor($p), 'active' => $p === $currentPage];
        }

        return [
            'currentPage' => $currentPage,
            'totalPages'  => $totalPages,
            'first'       => $currentPage > 1 ? $urlFor(1) : null,
            'previous'    => $currentPage > 1 ? $urlFor($currentPage - 1) : null,
            'next'        => $currentPage < $totalPages ? $urlFor($currentPage + 1) : null,
            'last'        => $currentPage < $totalPages ? $urlFor($totalPages) : null,
            'links'       => $links,
        ];
    }
}

if (! function_exists('truncate_for_meta')) {
    /**
     * Normalize free-form text into a meta-description-safe string: collapse
     * whitespace/newlines to single spaces and truncate at a word boundary
     * near $maxLength, never mid-word.
     *
     * Same logic as inquiry_meta_description()'s truncation step, pulled out
     * here so other content types (products, etc.) don't duplicate it.
     */
    function truncate_for_meta(string $text, int $maxLength = 160): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));

        if ($text === '' || strlen($text) <= $maxLength) {
            return $text;
        }

        $truncated = substr($text, 0, $maxLength);
        $cut = strrpos($truncated, ' ');

        if ($cut !== false && $cut > (int) ($maxLength * 0.5)) {
            $truncated = substr($truncated, 0, $cut);
        }

        return rtrim($truncated, " .,;:-") . '...';
    }
}
