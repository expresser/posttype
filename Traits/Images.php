<?php namespace Expresser\PostType\Traits;

use Expresser\PostType\Image;

trait Images {

  public function hasImages() {

    return $this->images->count() > 0;
  }

  public function images() {

    $images = Image::query()->parent($this->ID)->get();

    if ($this->hasFeaturedImage()) {

      $images->push($this->featured_image);
    }

    return $this->images = $images;
  }
}
