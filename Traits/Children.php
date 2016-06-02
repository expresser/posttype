<?php namespace Expresser\PostType\Traits;

trait Children {

  public function children() {

    return $this->children = self::query()->parent($this->ID)->orderBy('menu_order', 'ASC')->get();
  }

  public function hasChildren() {

    return $this->children->count() > 0;
  }
}
