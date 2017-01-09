<?php

namespace Expresser\PostType\Traits;

use Expresser\PostType\Image;

trait Images
{
    public function getImagesAttribute()
    {
        $images = Image::query()->parent($this->ID)->get();

        if ($this->hasFeaturedImage()) {
            $images->push($this->featured_image);
        }

        return $images;
    }

    public function hasImages()
    {
        return $this->images->count() > 0;
    }
}
