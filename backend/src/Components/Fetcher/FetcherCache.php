<?php

declare(strict_types=1);

namespace App\Components\Fetcher;

use App\Components\Cacher\Cacher;

final readonly class FetcherCache
{
    private const string TAG_PREFIX = 'fetcher_tag.';

    public function __construct(
        private Cacher $cacher,
    ) {}

    public function get(string $key): mixed
    {
        return $this->cacher->get($key);
    }

    /**
     * @param list<string> $tags
     */
    public function set(string $key, mixed $value, int $ttl, array $tags): bool
    {
        $stored = $this->cacher->set($key, $value, $ttl);

        if (!$stored) {
            return false;
        }

        foreach ($tags as $tag) {
            $tagKey = $this->tagKey($tag);

            $this->cacher->sAdd($tagKey, $key);
            $this->cacher->expire($tagKey, $ttl);
        }

        return true;
    }

    public function invalidateTag(string $tag): void
    {
        $tagKey = $this->tagKey($tag);

        foreach ($this->cacher->sMembers($tagKey) as $key) {
            $this->cacher->delete($key);
        }

        $this->cacher->delete($tagKey);
    }

    private function tagKey(string $tag): string
    {
        return self::TAG_PREFIX . $tag;
    }
}
