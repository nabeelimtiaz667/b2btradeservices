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
