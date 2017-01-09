<?php

namespace Expresser\PostType\Traits;

use Expresser\Taxonomy\Tag;

trait Tags
{
    public function getTagsAttribute()
    {
        return Tag::query()->post($this->ID)->get();
    }

    public function hasTags()
    {
        return $this->tags->count() > 0;
    }

    public function replaceTags(array $tags)
    {
        return $this->replaceTerms($tags, 'post_tag');
    }
}
