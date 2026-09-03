<?php

if (! function_exists('highlight_keyword')) {
    /**
     * Escape $text and wrap every case-insensitive occurrence of $keyword in
     * <mark>, for search-results highlighting.
     *
     * Returns already-escaped, ready-to-echo HTML -- callers must NOT also
     * esc() $text (that would double-encode) and must NOT esc() the return
     * value (that would encode the <mark> tags themselves). Matching is done
     * on the escaped text against an escaped keyword, so both go through the
     * same encoding and stay aligned (e.g. a keyword containing "&" still
     * matches "&amp;" in the escaped text).
     */
    function highlight_keyword(?string $text, ?string $keyword): string
    {
        $safe = esc((string) $text);
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return $safe;
        }

        $safeKeyword = esc($keyword);

        return preg_replace('/' . preg_quote($safeKeyword, '/') . '/iu', '<mark>$0</mark>', $safe) ?? $safe;
    }
}

if (! function_exists('count_keyword_occurrences')) {
    /**
     * Case-insensitive substring occurrence count of $keyword in $text.
     *
     * Operates on raw (unescaped) text -- for counting matches inside a
     * field that isn't rendered onto the page at all (or only a truncated
     * slice of it is), so highlight_keyword() has nothing to mark up and a
     * "matched N times in this record" note is shown instead.
     */
    function count_keyword_occurrences(?string $text, ?string $keyword): int
    {
        $text = (string) $text;
        $keyword = trim((string) $keyword);

        if ($text === '' || $keyword === '') {
            return 0;
        }

        return substr_count(mb_strtolower($text), mb_strtolower($keyword));
    }
}
