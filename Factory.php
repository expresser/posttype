<?php

namespace Expresser\PostType;

use InvalidArgumentException;
use WP_Post;

class Factory
{
    protected static $classCache = [];

    public static function resolve(WP_Post $post)
    {
        $postType = $post->post_type;

        $classname = isset(static::$classCache[$postType]) ? static::$classCache[$postType] : null;

        if (is_null($classname)) {
            $classname = static::normalizeClassName($postType);

            static::$classCache[$postType] = $classname;
        }

        return new $classname($post);
    }

    protected static function normalizeClassName($key)
    {
        $classname = static::convertToNamespace($key);

        if (!class_exists($classname)) {
            $classname = static::convertToAlias($key);

            if (!class_exists($classname)) {
                $classname = __NAMESPACE__.'\\'.$classname;

                if (!class_exists($classname)) {
                    throw new InvalidArgumentException('Class type not found.');
                }
            }
        }

        return $classname;
    }

    protected static function convertToWords($value)
    {
        return ucwords(str_replace(['-', '_'], ' ', $value));
    }

    protected static function convertToAlias($value)
    {
        return str_replace(' ', '', static::convertToWords($value));
    }

    protected static function convertToNamespace($value)
    {
        return str_replace(' ', '\\', static::convertToWords($value));
    }
}
