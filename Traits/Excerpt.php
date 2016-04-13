<?php namespace Expresser\PostType\Traits;

trait Excerpt {

  protected $excerptLength = 55;
  protected $excerptMore = null;
  protected $suppressExcerptFilters = false;

  public function getExcerptAttribute($value) {

    $value = apply_filters('the_excerpt', $value);

    if (empty($value)) {

      $value = $this->suppressContentFilters(true)->content;
      $value = strip_shortcodes($value);
      $value = apply_filters('the_content', $value);
      $value = str_replace(']]>', ']]&gt;', $value);

      if (!$this->suppressExcerptFilters) {

        $value = wp_trim_words($value, apply_filters('excerpt_length', $this->excerptLength), apply_filters('excerpt_more', $this->excerptMore));
      }
    }

    $this->suppressExcerptFilters = false;

    return $value;
  }

  public function getExcerptLength() {

    return $this->excerptLength;
  }

  public function getExcerptMore() {

    return $this->excerptMore;
  }

  public function setExcerptLength($excerptLength) {

    $this->excerptLength = $excerptLength;

    return $this;
  }

  public function setExcerptMore($excerptMore) {

    $this->excerptMore = $excerptMore;

    return $this;
  }

  public function suppressExcerptFilters($suppressExcerptFilters) {

    $this->suppressExcerptFilters = $suppressExcerptFilters;

    return $this;
  }
}
