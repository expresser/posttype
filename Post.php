<?php namespace Expresser\PostType;

class Post extends Native {
  use \Expresser\PostType\Traits\Content,
      \Expresser\PostType\Traits\Excerpt,
      \Expresser\PostType\Traits\Permalink;

  public function postType() {

    return 'post';
  }
}
