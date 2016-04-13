<?php namespace Expresser\PostType\Traits;

trait Content {

  protected $suppressContentFilters = false;

  public function getContentAttribute($value) {

    if (post_password_required($this->ID)) $value = get_the_password_form($this->ID);

    if (!$this->suppressContentFilters) {

      $value = apply_filters('the_content', $value);
    }

    $this->suppressContentFilters = false;

    if (!empty($value)) return $value;
  }

  public function hasContent() {

    return !empty($this->content);
  }

  public function suppressContentFilters($suppressContentFilters) {

    $this->suppressContentFilters = $suppressContentFilters;

    return $this;
  }
}
