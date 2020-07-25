<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;

class CacheableTest extends TestCase
{
    /**
     * Test Verifies An Eloquent Model Gets Unique Key.
     *
     * @return void
     */
    public function testVerifiesAnEloquentModelGetsUniqueKey(): void
    {
        $model = $this->createPost();

        $this->assertEquals(
            'Tests\Models\Post/1-' . $model->updated_at->timestamp,
            $model->getCacheKey()
        );
    }
}
