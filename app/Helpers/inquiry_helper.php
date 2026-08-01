<?php

if (! function_exists('inquiry_slugify')) {
    /**
     * Build the slug base for an inquiry title.
     *
     * Intentionally identical to the legacy inline expression that the views
     * used to render, url_title(strtolower($title), '-', true), so that no
     * already-indexed URL changes when slugs move into the database. This was
     * verified byte-for-byte against all 470 rows that existed at the time; a
     * naive preg_replace slugifier differs on 15 of them (for example
     * "men's" -> "mens" vs "men-s", "60%protein" -> "60proteindried" vs
     * "60-protein-dried").
     *
     * url_title() already transliterates, so non-ASCII titles need no extra
     * handling. The length cap is the only addition.
     *
     * Returns '' when nothing usable can be derived; callers are expected to
     * fall back to another field.
     */
    function inquiry_slugify(?string $title, int $maxLength = 200): string
    {
        $title = trim((string) $title);

        if ($title === '') {
            return '';
        }

        helper('url');
        $slug = url_title(strtolower($title), '-', true);

        if (strlen($slug) > $maxLength) {
            $slug = substr($slug, 0, $maxLength);
            $cut  = strrpos($slug, '-');

            if ($cut !== false && $cut > 60) {
                $slug = substr($slug, 0, $cut); // avoid ending mid-word
            }
        }

        return trim($slug, '-');
    }
}

if (! function_exists('inquiry_url')) {
    /**
     * Canonical public URL for an inquiry row.
     *
     * Falls back to the numeric form when the slug is missing (a row predating
     * the backfill, or an interrupted backfill). That numeric form is a real
     * route which 301s to the canonical URL, so nothing 404s and no view needs
     * a conditional of its own.
     */
    function inquiry_url(array $inquiry): string
    {
        if (! empty($inquiry['slug'])) {
            return base_url('buyer-inquiry/' . $inquiry['slug']);
        }

        return base_url('buyer-inquiry/' . ($inquiry['id'] ?? ''));
    }
}
