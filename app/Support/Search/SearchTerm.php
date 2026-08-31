<?php

namespace App\Support\Search;

use Illuminate\Support\Str;

class SearchTerm
{
    public const MAX_LENGTH = 100;

    public static function normalize(?string $term): ?string
    {
        if ($term === null) {
            return null;
        }

        $term = trim($term);

        if ($term === '') {
            return null;
        }

        return Str::limit($term, self::MAX_LENGTH, '');
    }

    public static function likePattern(?string $term): ?string
    {
        $term = self::normalize($term);

        if ($term === null) {
            return null;
        }

        return '%'.addcslashes($term, '%_\\').'%';
    }
}
