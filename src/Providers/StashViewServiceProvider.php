<?php

declare(strict_types=1);

namespace Enlight\StashView\Providers;

use Enlight\StashView\CacheDirective;
use Enlight\StashView\RussianCaching;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Cache\Repository;
use Enlight\StashView\Middlewares\SetArrayCacheDriver;

class StashViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(CacheDirective::class, function () {
            return new CacheDirective(
                new RussianCaching(
                    app(Repository::class)
                )
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @param Kernel $kernel
     * @return void
     */
    public function boot(Kernel $kernel): void
    {
        $kernel->pushMiddleware(SetArrayCacheDriver::class);

        Blade::directive('cache', function ($expression) {
            return "<?php if(! app('Enlight\StashView\CacheDirective')->setUp({$expression})) : ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php endif; echo app('Enlight\StashView\CacheDirective')->tearDown(); ?>";
        });
    }
}
