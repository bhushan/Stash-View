<?php

declare(strict_types=1);

namespace Enlight\StashView;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Cache\Repository as Cache;

class RussianCaching
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * RussianCaching constructor.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Put fragment into the cache forever.
     *
     * @param $key
     * @param $fragment
     * @return string
     */
    public function put($key, $fragment): string
    {
        $key = $this->normalizeCacheKey($key);

        return $this->cache
            ->tags('views')
            ->rememberForever($key, function () use ($fragment) {
                return $fragment;
            });
    }

    /**
     * Check if cache already has key.
     *
     * @param $key
     * @return bool
     */
    public function has($key): bool
    {
        $key = $this->normalizeCacheKey($key);

        return $this->cache
            ->tags('views')
            ->has($key);
    }

    /**
     * Normalizes cache key for being used.
     *
     * @param Model|string $key
     * @return string
     */
    protected function normalizeCacheKey($key): string
    {
        if ($key instanceof Model) {
            $key = $key->getCacheKey();
        }

        return $key;
    }
}
