<?php

declare(strict_types=1);

namespace Enlight\StashView\Traits;

trait Cacheable
{
    /**
     * Get cache key for the model according to its primary key.
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        return sprintf(
            '%s/%s-%s',
            get_class($this),
            $this->getRouteKey(),
            $this->updated_at->timestamp
        );
    }
}
