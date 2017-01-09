<?php

namespace Expresser\PostType;

use Expresser\PostType\Traits\Content;
use Expresser\PostType\Traits\Permalink;

class Page extends Native
{
    use Content,
        Permalink;

    public $post_type = 'page';

    public function getCacheableAccessors()
    {
        return array_merge(parent::getCacheableAccessors(), [
            'permalink',
        ]);
    }
}
