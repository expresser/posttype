<?php

namespace Expresser\PostType\Traits;

use Expresser\Taxonomy\Tag;

trait Tags
{
    public function getTagsAttribute()
    {
        return Tag::query()->post($this->ID)->get();
    }

    public function hasTags(array $tags = [])
    {
        return $this->hasTag($tags);
    }

    public function hasTag($tag)
    {
        return has_tag($tag, $this->ID);
    }

    public function replaceTags(array $tags)
    {
        return $this->replaceTerms($tags, 'post_tag');
    }
}
