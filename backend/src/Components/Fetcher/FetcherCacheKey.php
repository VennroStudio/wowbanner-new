<?php

declare(strict_types=1);

namespace App\Components\Fetcher;

final readonly class FetcherCacheKey
{
    /**
     * @param list<int|string> $parts
     */
    public static function tag(string $prefix, array $parts): string
    {
        if ($parts === []) {
            return $prefix;
        }

        return $prefix . '.' . implode('.', $parts);
    }

    public static function key(string $tag, string $modelClass): string
    {
        return $tag . '.' . self::shortModelName($modelClass);
    }

    private static function shortModelName(string $modelClass): string
    {
        $position = strrpos($modelClass, '\\');

        return $position === false ? $modelClass : substr($modelClass, $position + 1);
    }
}
