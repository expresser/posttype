<?php namespace Expresser\PostType;

use Expresser\Support\Filter;

class Attachment extends Native {

  public function caption() {

    if (!empty($this->excerpt)) return $this->excerpt;
  }

  public function description() {

    if (!empty($this->content)) return $this->content;
  }

  public function newQuery() {

    return parent::newQuery()->status($this->post_status);
  }

  public function postStatus() {

    return array('draft', 'inherit');
  }

  public function postType() {

    return 'attachment';
  }

  public function thumbnailUrl() {

    $src = wp_get_attachment_image_src($this->ID, 'thumbnail', true);

    if (is_array($src)) return $this->thumbnail_url = $src[0];
  }

  public function url() {

    $url = wp_get_attachment_url($this->ID);

    if (Filter::isUrl($url)) return $this->url = $url;
  }
}