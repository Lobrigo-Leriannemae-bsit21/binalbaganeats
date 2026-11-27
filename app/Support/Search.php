<?php

namespace App\Support;

final class Search
{
    /** Accepts only plain strings, trims, caps the length, and returns null when empty. */
    public static function clean(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = mb_substr(trim($value), 0, 100);

        return $value === '' ? null : $value;
    }

    /** Builds a LIKE pattern, escaping % and _ so users cannot inject wildcards. */
    public static function like(string $term): string
    {
        return '%'.addcslashes($term, '%_\\').'%';
    }
}