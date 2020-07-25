<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Enlight\StashView\CacheDirective;
use Enlight\StashView\RussianCaching;

class CacheDirectiveTest extends TestCase
{
    /**
     * Test If It Sets Up The Opening Cache Directive.
     *
     * @return void
     */
    public function testIfItSetsUpTheOpeningCacheDirective(): void
    {
        $directive = $this->createNewCacheDirective();

        $isCached = $directive->setUp($this->createPost());

        $this->assertFalse($isCached);

        echo '<div>fragment</div>';

        $cachedFragment = $directive->tearDown();

        $this->assertEquals('<div>fragment</div>', $cachedFragment);
    }

    private function createNewCacheDirective(): CacheDirective
    {
        $cache = new Repository(
            new ArrayStore()
        );

        $doll = new RussianCaching($cache);

        return new CacheDirective($doll);
    }
}
