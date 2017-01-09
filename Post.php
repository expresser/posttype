<?php

namespace Expresser\PostType;

use Expresser\PostType\Traits\Content;
use Expresser\PostType\Traits\Excerpt;
use Expresser\PostType\Traits\Permalink;

class Post extends Native
{
    use Content,
        Excerpt,
        Permalink;

    public $post_type = 'post';

    public function getCacheableAccessors()
    {
        return array_merge(parent::getCacheableAccessors(), [
            'permalink',
        ]);
    }
}
