# Stash View

Stash view is a composer package for `Laravel` which caches views using Russian Doll Caching methodology.

> What is Russian Doll Caching ?
> It is really famous caching stratergy to cache your views into small chunks. It is quite famous in Rails community. If you are interested to know more checkout this [link](https://guides.rubyonrails.org/caching_with_rails.html).

In a nutshell, It caches your views into chunks.
This article will give you more clear [idea](https://signalvnoise.com/posts/3112-how-basecamp-next-got-to-be-so-damn-fast-without-using-much-client-side-ui).

![Russian Doll Caching PNG](http://s3.amazonaws.com/37assets/svn/777-russian-doll-caching-1.png)

## Now enough about idea let's talk about how to use it

> You can also checkout example code from [here](https://github.com/bhushan/Stash-View-Example)

## Installation

### Step 1: Composer

Firstly require this package using following command.

```bash
composer require enlight/stash-view
```

### Step 2: Service Provider (Optional)

 This package supports auto discovery but if you are using `Laravel 5.4` or below you need to add `ServiceProvider` into `providers` array.

For your Laravel app, open `config/app.php` and, within the `providers` array, append:

```php
Enlight\StashView\Providers\StashViewServiceProvider::class
```

This will bootstrap the package into Laravel.

### Step 3: Cache Driver

For this package to function properly, you must use a Laravel cache driver that supports tagging (like `Cache::tags('foo')`). Drivers such as `Memcached` and `Redis` support this feature.

Check your `.env` file, and ensure that your `CACHE_DRIVER` choice accomodates this requirement:

```php
CACHE_DRIVER=redis
```

> NOTE: If your application is set to `local` environment then by default this package will use `array` caching driver to speed up development process so that you don't need to clear cache again and again while developing.

Have a look at [Laravel's cache configuration documentation](https://laravel.com/docs/7.x/cache#configuration), if you need any help.

## Usage

### The Basics

With the package now installed, you may use the provided `@cache` Blade directive anywhere in your views, like so:

```html
@cache('my-cache-key')
    <div>
        <h1>Hello World</h1>
    </div>
@endcache
```

By surrounding this block of HTML with the `@cache` and `@endcache` directives, we're asking the package to cache the given HTML. Now this example is trivial, however, you can imagine a more complex view that includes various nested caches, as well as lazy-loaded relationship calls that trigger additional database queries. After the initial page load that caches the HTML fragment, each subsequent refresh will instead pull from the cache. As such, those additional database queries will never be executed. Really cool side effect of this package is it reduces you sql queries and solves n+1 problem out of the box.

Please keep in mind that, in production, this will cache the HTML fragment "forever". For local development, on the other hand, we are using `array` cache driver which stores the cache in memory and flush out when work done. That way, you may update your views and templates however you wish, without needing to worry about clearing the cache manually.

Now because your production server will cache the fragments forever, you'll want to add a step to your deployment process that clears the relevant cache.

```php
Cache::tags('views')->flush();
```

### Caching Models

While you're free to hard-code any string for the cache key, the true power of Russian-Doll caching comes into play when we use a timestamp-based approach.

Consider the following fragment:

```html
@cache($post)
    <article>
        <h2>{{ $post->title }}></h2>
        <p>Written By: {{ $post->author->username }}</p>

        <div class="body">{{ $post->body }}</div>
    </article>
@endcache
```

In this example, we're passing the `$post` object, itself, to the `@cache` directive - rather than a string. The package will then look for a `getCacheKey()` method on the model. We've already done that work for you; just have your Eloquent model use the `Enlight\StashView\Traits\Cacheable` trait, like so:

```php
use Enlight\StashView\Traits\Cacheable;

class Post extends Eloquent
{
    use Cacheable;
}
```

Alternatively, you may use this trait on a parent class that each of your Eloquent models extend.

That should do it! Now, the cache key for this fragment will include the object's `Primary Key` ie `id` in most cases and `updated_at` timestamp: `App\Post/1-98765432101`.

> The key is that, because we factor the `updated_at` timestamp into the cache key, whenever you update the given post, the cache key will change. This will then, in effect, bust the cache!

#### Touching

In order for this technique to work properly, it's vital that we have some mechanism to alert parent relationships (and subsequently bust parent caches) each time a model is updated. Here's a basic workflow:

1. Model is updated in the database.
2. Its `updated_at` timestamp is refreshed, triggering a new cache key for the instance.
3. The model "touches" (or pings) its parent.
4. The parent's `updated_at` timestamp, too, is updated, which busts its associated cache.
5. Only the affected fragments re-render. All other cached items remain untouched.

Luckily, Laravel offers this "touch" functionality out of the box. Consider a `Note` object that needs to alert its parent `Card` relationship each time an update occurs.

```php
<?php

namespace App;

use Enlight\StashView\Traits\Cacheable;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use Cacheable;

    protected $touches = ['card'];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
```

Notice the `$touches = ['card']` portion. This instructs Laravel to ping the `card` relationship's timestamps each time the note is updated.

Now, everything is in place. You might render your view, like so:

> resources/views/cards/_card.blade.php

```html
@cache($card)
    <article class="Card">
        <h2>{{ $card->title }}</h2>

        <ul>
            @foreach ($card->notes as $note)
                @include ('cards/_note')
            @endforeach
        </ul>
    </article>
@endcache
```

> resources/views/cards/_note.blade.php

```html
@cache($note)
    <li>{{ $note->body }}</li>
@endcache
```

Notice the Russian-Doll style cascading for our caches; that's the key. If any note is updated, its individual cache will clear - long with its parent - but any siblings will remain untouched.

### Caching Collections

> Its not yet supported but this is already in todo list. Will appreciate any good PR for this. :)

## FAQ

**1. Is there any way to override the cache key for a model instance?**

Yes. Let's say you have:

```html
@cache($post)
    <div>view here</div>
@endcache
```

Behind the scenes, we'll look for a `getCacheKey` method on the model. Now, as mentioned above, you can use the `Enlight\StashView\Traits\Cacheable` trait to instantly import this functionality. Alternatively, you may pass your own cache key to the `@cache` directive, like this:

```html
@cache('post-pagination')
    <div>view here</div>
@endcache
```

This instructs the package to use `post-pagination` for the cache instead. This can be useful for pagination and other related tasks.

That's it. Thank You :)

Happy Coding.
