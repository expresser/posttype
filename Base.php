<?php namespace Expresser\PostType;

use Exception;

use WP_Post;
use WP_Query;

abstract class Base extends \Expresser\Support\Model {

  protected $fieldPrefix = 'post_';

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

    return (new Query(new WP_Query))->setModel($this)->type($this->post_type)->paginate(false);
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

  public static function registerHooks($class) {

    remove_action('delete_post', [__CLASS__, '_deletePost']);
    remove_action('save_post', [__CLASS__, '_savePost'], 10, 2);
    remove_action('trash_post', [__CLASS__, '_trashPost']);

    remove_filter('get_post_metadata', [__CLASS__, '_getMetaData'], 10, 4);

    add_action('delete_post', [__CLASS__, '_deletePost']);
    add_action('init', [$class, 'registerPostType']);
    add_action('save_post', [__CLASS__, '_savePost'], 10, 2);
    add_action('trash_post', [__CLASS__, '_trashPost']);

    add_filter('get_post_metadata', [__CLASS__, '_getMetaData'], 10, 4);
  }

  public static function registerPostType() {

    throw new Exception('A post type must override registerPostType.');
  }

  public static function _deletePost($id) {

    do_action(implode('_', ['exp/delete', get_post_type($id)]), $id);
  }

  public static function _getMetaData($value, $id, $key = '', $single = false) {

    do_action(implode('_', ['exp/get', get_post_type($id), 'metadata']), $value, $id, $key, $single);
  }

  public static function _savePost($id, WP_Post $post) {

    do_action(implode('_', ['exp/save', $post->post_type]), $id, $post);
  }

  public static function _trashPost($id) {

    do_action(implode('_', ['exp/trash', get_post_type($id)]), $id);
  }

  public abstract function postType();
}
