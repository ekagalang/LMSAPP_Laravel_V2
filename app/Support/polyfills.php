<?php

// Global polyfills for missing mbstring functions in constrained environments
if (!function_exists('mb_split')) {
    function mb_split($pattern, $string, $limit = -1)
    {
        // Fallback using preg_split; assumes $pattern is a regex without delimiters
        $delimited = '/' . $pattern . '/u';
        return preg_split($delimited, $string, $limit === -1 ? 0 : $limit);
    }
}

