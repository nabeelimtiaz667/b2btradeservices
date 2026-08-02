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

if (! function_exists('inquiry_meta_description')) {
    /**
     * SEO meta description for an inquiry detail page.
     *
     * Prefers the buyer's own description text: it's free-form and reads
     * naturally, unlike a templated sentence built from fields. Descriptions run
     * up to ~4500 chars and 98 of 470 rows contain embedded newlines, so this
     * normalizes whitespace and truncates at a word boundary rather than
     * dumping the raw column into the tag.
     *
     * Falls back to a templated sentence from title/quantity/unit/product_name
     * for the 3 rows with no description at all, so the tag is never empty.
     */
    function inquiry_meta_description(array $inquiry, int $maxLength = 160): string
    {
        $text = trim((string) ($inquiry['description'] ?? ''));

        if ($text === '') {
            $parts = [$inquiry['title'] ?? 'Buyer inquiry'];

            $qty = trim(($inquiry['quantity'] ?? '') . ' ' . ($inquiry['unit'] ?? ''));
            if ($qty !== '') {
                $parts[] = 'Quantity: ' . $qty . '.';
            }

            if (! empty($inquiry['product_name'])) {
                $parts[] = 'Product: ' . $inquiry['product_name'] . '.';
            }

            $parts[] = 'View this buyer inquiry on B2B Trade Services.';
            $text    = implode(' ', $parts);
        }

        // Collapse newlines/tabs/repeated spaces from free-form text into one
        // line, since a meta tag attribute can't carry the original formatting.
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        if (strlen($text) <= $maxLength) {
            return $text;
        }

        $truncated = substr($text, 0, $maxLength);
        $cut       = strrpos($truncated, ' ');

        if ($cut !== false && $cut > (int) ($maxLength * 0.5)) {
            $truncated = substr($truncated, 0, $cut); // avoid ending mid-word
        }

        return rtrim($truncated, " .,;:-") . '...';
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
