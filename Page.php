<?php

namespace Expresser\PostType;

use Expresser\PostType\Traits\Content;
use Expresser\PostType\Traits\Permalink;

class Page extends Native
{
    use Content,
        Permalink;

    public function postType()
    {
        return 'page';
    }
}
