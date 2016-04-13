<?php namespace Expresser\PostType\Traits;

use Expresser\Taxonomy\Category;

trait Categories {

  public function categories() {

    return $this->categories = Category::wherePost($this->ID)->get();
  }

  public function hasCategories(array $categories = []) {

    return $this->hasCategory($categories);
  }

  public function hasCategory($category) {

    return has_category($category, $this->ID);
  }

  public function inCategory($category) {

    return in_category($category, $this->ID);
  }
}
