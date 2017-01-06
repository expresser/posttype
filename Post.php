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

    public function postType()
    {
        return 'post';
    }
}
