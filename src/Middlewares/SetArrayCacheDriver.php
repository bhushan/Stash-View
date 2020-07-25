<?php

declare(strict_types=1);

namespace Enlight\StashView\Middlewares;

use Closure;
use Illuminate\Http\Request;

class SetArrayCacheDriver
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app()->environment() === 'local') {
            config(['cache.default' => 'array']);
        }

        return $next($request);
    }
}
