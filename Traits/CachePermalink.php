<?php

namespace Expresser\PostType\Traits;

use Closure;

trait CachePermalink
{
    protected static $permalinkCache = [];

    protected static function getPermalink($url, Closure $callback = null)
    {
        $key = md5($url);

        $cachedUrl = static::getCachedPermalink($key);

        if (is_null($cachedUrl)) {
            $url = is_callable($callback) ? $callback($url) : $url;

            static::setCachedPermalink($key, $url);
        } else {
            $url = $cachedUrl;
        }

        return $url;
    }

    protected static function getCachedPermalink($key)
    {
        if (array_key_exists($key, static::$permalinkCache)) {
            return static::$permalinkCache[$key];
        }
    }

    protected static function setCachedPermalink($key, $url)
    {
        static::$permalinkCache[$key] = $url;
    }
}
