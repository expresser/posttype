<?php namespace Expresser\PostType;

use Exception;

use WP_Post;
use WP_Query;

abstract class Base extends \Expresser\Support\Model {

  protected $post;

  public function __construct(WP_Post $post = null) {

    $this->post = $post ?: new WP_Post((object)array(
      'post_status' => $this->post_status,
      'post_type' => $this->post_type,
    ));

    parent::__construct($this->post->to_array());
  }

  public function addMeta($key, $value, $unique = false) {

    return add_post_meta($this->ID, $key, $value, $unique);
  }

  public function delete() {

    wp_trash_post($this->ID);
  }

  public function deleteMeta($key, $value = '') {

    return delete_post_meta($this->ID, $key, $value);
  }

  public function getAuthorAttribute($value) {

    if (is_numeric($value)) $value = (int)$value;

    return $value;
  }

  public function getAttributeFromArray($key) {

    $value = parent::getAttributeFromArray($key);

    if (is_null($value)) $value = parent::getAttributeFromArray('post_' . $key);

    return $value;
  }

  public function getCommentCountAttribute($value) {

    if (is_numeric($value)) $value = (int)$value;

    return $value;
  }

  public function getMeta($key, $single = false) {

    return get_post_meta($this->ID, $key, $single);
  }

  public function hasNextPost() {

    return !is_null($this->next_post);
  }

  public function hasPreviousPost() {

    return !is_null($this->previous_post);
  }

  public function insert() {

    return wp_insert_post($this->getDirty());
  }

  public function newQuery() {

    return (new Query(new WP_Query))->setModel($this)->whereType($this->post_type)->limit(false);
  }

  public function nextPost() {

    return $this->next_post = $this->getNextPost();
  }

  public function previousPost() {

    return $this->previous_post = $this->getPreviousPost();
  }

  public function replaceTerms(array $terms, $taxonomy) {

    return $this->saveTerms($terms, $taxonomy);
  }

  public function saveTerms(array $terms, $taxonomy, $append = false) {

    return wp_set_object_terms($this->ID, $terms, $taxonomy, $append);
  }

  public function update() {

    wp_update_post(array_merge(
      $this->getDirty(),
      array('ID' => $this->ID)
    ));
  }

  public function updateMeta($key, $value, $previousValue = '') {

    return update_post_meta($this->ID, $key, $value, $previousValue);
  }

  protected function getAdjacentPost($inSameTerm = false, array $excludedTerms = [], $previous = true, $taxonomy = 'category') {

    global $post;

    $globalPost = $post;

    $post = get_post($this->ID);

    $adjacentPost = get_adjacent_post($inSameTerm, $excludedTerms, $previous, $taxonomy);

    $post = $globalPost;

    if ($adjacentPost instanceof WP_Post) return self::make($adjacentPost);
  }

  protected function getNextPost($inSameTerm = false, array $excludedTerms = [], $taxonomy = 'category') {

    return $this->getAdjacentPost($inSameTerm, $excludedTerms, false, $taxonomy);
  }

  protected function getPreviousPost($inSameTerm = false, array $excludedTerms = [], $taxonomy = 'category') {

    return $this->getAdjacentPost($inSameTerm, $excludedTerms, true, $taxonomy);
  }

  public static function register() {

    static::registerPostType();

    parent::register();
  }

  protected static function registerPostType() {

    throw new Exception('A post type must override registerPostType.');
  }

  public abstract function postType();
}
