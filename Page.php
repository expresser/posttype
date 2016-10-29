<?php

namespace Expresser\PostType;

class Page extends Native
{
    use \Expresser\PostType\Traits\Content,
      \Expresser\PostType\Traits\Permalink;

    public function postType()
    {
        return 'page';
    }
}
