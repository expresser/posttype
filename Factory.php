<?php namespace Expresser\Type;

use InvalidArgumentException;

use WP_Post;

class Factory {

  protected static $classCache = [];

  public static function resolve(WP_Post $post) {

    $class = static::normalizeClassName($post->post_type);

    return new $class($post);
  }

  protected static function normalizeClassName($key) {

    if (isset(static::$classCache[$key])) {

			return static::$classCache[$key];
		}

    $classname = static::convertToNamespace($key);

    if (!class_exists($classname)) {

      $classname = static::convertToAlias($key);

      if (!class_exists($classname)) {

        $classname = __NAMESPACE__ . '\\' . $classname;

        if (!class_exists($classname)) {

          throw new InvalidArgumentException('Class type not found.');
        }
      }
    }

    return static::$classCache[$key] = $classname;
  }

  protected static function convertToWords($value) {

    return ucwords(str_replace(array('-', '_'), ' ', $value));
  }

  protected static function convertToAlias($value) {

    return str_replace(' ', '', static::convertToWords($value));
  }

  protected static function convertToNamespace($value) {

    return str_replace(' ', '\\', static::convertToWords($value));
  }
}
