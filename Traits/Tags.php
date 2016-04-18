<?php namespace Expresser\PostType\Traits;

use Expresser\Taxonomy\Tag;

trait Tags {

  public function hasTags() {

    return $this->tags->count() > 0;
  }

  public function replaceTags(array $tags) {

    return $this->replaceTerms($tags, 'post_tag');
  }

  public function tags() {

    return $this->tags = Tag::wherePost($this->ID)->get();
  }
}
