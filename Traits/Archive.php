<?php namespace Expresser\PostType\Traits;

use Expresser\Support\Filter;

trait Archive {

  public static function getArchiveUrl() {

    $url = get_post_type_archive_link((new static)->type);

    if (Filter::isUrl($url)) return $url;
  }
}
