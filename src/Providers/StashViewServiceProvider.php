<?php

declare(strict_types=1);

namespace Enlight\StashView\Providers;

use Enlight\StashView\CacheDirective;
use Enlight\StashView\RussianCaching;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class StashViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Blade::directive('cache', function ($expression) {
            return "<?php if(! app('Enlight\StashView\CacheDirective')->setUp({$expression})) : ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php endif; echo app('Enlight\StashView\CacheDirective')->tearDown(); ?>";
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->singleton(CacheDirective::class, function () {
            new CacheDirective(
                new RussianCaching()
            );
        });
    }
}
