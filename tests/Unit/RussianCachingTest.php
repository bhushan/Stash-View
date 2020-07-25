<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Enlight\StashView\RussianCaching;

class RussianCachingTest extends TestCase
{
    /**
     * Test If It Caches The Given Key.
     *
     * @return void
     */
    public function testIfItCachesTheGivenKey(): void
    {
        $post = $this->createPost();

        $cache = new RussianCaching(new Repository(new ArrayStore()));

        $cache->put($post, '<div>view fragment</div>');

        $this->assertTrue($cache->has($post->getCacheKey()));

        $this->assertTrue($cache->has($post));
    }
}
