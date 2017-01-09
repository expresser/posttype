<?php

namespace Expresser\PostType\Traits;

trait Children
{
    public function getChildrenAttribute()
    {
        return self::query()->parent($this->ID)->orderBy('menu_order', 'ASC')->get();
    }

    public function hasChildren()
    {
        return $this->children->count() > 0;
    }
}
