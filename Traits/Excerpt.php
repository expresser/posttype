<?php namespace Expresser\PostType\Traits;

trait Excerpt {

  protected $postExcerptLength = 55;
  protected $postExcerptMore = null;
  protected $suppressPostExcerptFilters = false;

  public function getPostExcerptAttribute($value) {

    $value = apply_filters('the_excerpt', $value);

    if (empty($value)) {

      $value = $this->suppressPostContentFilters(true)->post_content;
      $value = strip_shortcodes($value);
      $value = apply_filters('the_content', $value);
      $value = str_replace(']]>', ']]&gt;', $value);

      if (!$this->suppressPostExcerptFilters) {

        $value = wp_trim_words($value, apply_filters('excerpt_length', $this->postExcerptLength), apply_filters('excerpt_more', $this->postExcerptMore));
      }
    }

    $this->suppressPostExcerptFilters = false;

    return $value;
  }

  public function getPostExcerptLength() {

    return $this->postExcerptLength;
  }

  public function getPostExcerptMore() {

    return $this->postExcerptMore;
  }

  public function setPostExcerptLength($postExcerptLength) {

    $this->postExcerptLength = $postExcerptLength;

    return $this;
  }

  public function setPostExcerptMore($postExcerptMore) {

    $this->postExcerptMore = $postExcerptMore;

    return $this;
  }

  public function suppressPostExcerptFilters($suppressPostExcerptFilters) {

    $this->suppressPostExcerptFilters = $suppressPostExcerptFilters;

    return $this;
  }
}
