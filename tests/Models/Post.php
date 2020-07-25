<?php

declare(strict_types=1);

namespace Tests\Models;

use Enlight\StashView\Traits\Cacheable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Cacheable;
}
