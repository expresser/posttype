<?php namespace Expresser\PostType\Traits;

use Expresser\Support\Filter;

trait Permalink {
  use CachePermalink;

  public function permalink() {

    $permalink = get_permalink($this->ID);

    if (Filter::isUrl($permalink)) return $this->permalink = $permalink;
  }

  public function url() {

    return $this->permalink;
  }
}
