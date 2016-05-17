<?php namespace Expresser\PostType;

use Expresser\Contracts\Image\Renderable;

class Image extends Attachment implements Renderable {

  protected $classNames;

  public function alt() {

    $alt = get_post_meta($this->ID, '_wp_attachment_image_alt', true);

    if (!empty($alt)) return $this->alt = $alt;
  }

  public function getClassNames($classNames) {

    $classNames =  implode(' ', [$this->classNames, $classNames]);

    return $classNames;
  }

  public function getSizeOrDefaultUrl($size) {

    $url = $this->getSizeUrl($size);

    if (is_null($url)) $url = $this->url;

    return $url;
  }

  public function getSizeUrl($size) {

    if (array_key_exists($size, $this->sizes)) return $this->sizes[$size]['url'];
  }

  public function height() {

    return $this->meta['height'];
  }

  public function isDimension($width, $height) {

    return $this->width == $width && $this->height == $height;
  }

  public function isLandscape() {

    return $this->width >= $this->height;
  }

  public function isPortrait() {

    return $this->height > $this->width;
  }

  public function meta() {

    $meta = wp_get_attachment_metadata($this->ID, true);

    if (is_array($meta)) {

      if (!isset($meta['sizes'])) $meta['sizes'] = [];

      foreach ($meta['sizes'] as $name => &$size) {

        $size = array_merge($size, array_combine(
          ['url', 'width', 'height', 'resized'],
          wp_get_attachment_image_src($this->ID, $name)
        ));
      }

      return $this->meta = $meta;
    }
  }

  public function mimeType() {

    return 'image';
  }

  public function newQuery() {

    return parent::newQuery()->mimeType($this->mime_type);
  }

  public function render($classNames = null, $alt = null, $title = null) {

    $this->classNames = $classNames;

    add_filter('get_image_tag_class', [$this, 'getClassNames'], 10);

    $img = self::removeHeightAndWidthAttributes(get_image_tag($this->ID, $alt, $title, null, null));

    remove_filter('get_image_tag_class', [$this, 'getClassNames'], 10);

    return $img;
  }

  public function sizes() {

    return $this->meta['sizes'];
  }

  public function thumbnailUrl() {

    return $this->getSizeUrl('thumbnail');
  }

  public function width() {

    return $this->meta['width'];
  }

  public static function renderImageOrDefault(Renderable $image = null, $defaultSrc, $classNames = null) {

    if (is_null($image)) {

      return apply_filters('get_image_tag', "<img class='$classNames' src='$defaultSrc'>", 0, null, null, null, null);
    }

    return $image->render($classNames);
  }

  protected static function removeHeightAndWidthAttributes($img) {

    $img = preg_replace('/height="\d+"/', '', $img);
    $img = preg_replace('/width="\d+"/', '', $img);

    return $img;
  }
}
