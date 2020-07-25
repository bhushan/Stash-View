<?php

declare(strict_types=1);

namespace Enlight\StashView;

use Illuminate\Database\Eloquent\Model;

class CacheDirective
{
    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @var RussianCaching
     */
    private $cache;

    /**
     * CacheDirective constructor.
     *
     * @param RussianCaching $cache
     */
    public function __construct(RussianCaching $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Set up output buffering and store cache key.
     *
     * @param Model $model
     * @return bool
     */
    public function setUp(Model $model): bool
    {
        ob_start();

        $this->keys[] = $key = $model->getCacheKey();

        return $this->cache->has($key);
    }

    /**
     * Get current buffer and store it into cache.
     *
     * @return string
     */
    public function tearDown(): string
    {
        return $this->cache->put(
            array_pop($this->keys),
            ob_get_clean()
        );
    }
}
