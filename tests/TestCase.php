<?php

declare(strict_types=1);

namespace Tests;

use Tests\Models\Post;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Load migrations from directory.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Set up database connection for tests.
     *
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Get package providers.
     *
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return ['Enlight\StashView\Providers\StashViewServiceProvider'];
    }

    /**
     * Create Post.
     *
     * @return Model
     */
    protected function createPost(): Model
    {
        $post = new Post();
        $post->title = 'dummy title';
        $post->save();

        return $post;
    }
}
