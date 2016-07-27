<?php namespace Expresser\PostType;

use Closure;
use InvalidArgumentException;

use WP_Query;

class Query extends \Expresser\Support\Query {

  protected $metas = [];

  protected $taxonomies = [];

  private $statuses = ['publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'];

  public function __construct(WP_Query $query) {

    parent::__construct($query);
  }

  public function find($id) {

    return $this->post($id)->status($this->statuses)->first();
  }

  public function findAll(array $ids) {

    return $this->posts($ids)->status($this->statuses)->get();
  }

  public function findByName($name) {

    return $this->post($name)->status($this->statuses)->first();
  }

  public function findBySlug($name) {

    return $this->page($name)->status($this->statuses)->first();
  }

  public function first() {

    return $this->limit(1)->get()->first();
  }

  public function limit($limit) {

    $this->paginate($limit);

    return $this;
  }

  public function get() {

    $posts = $this->query->get_posts();

    return $this->getModels($posts);
  }

  public function author($id) {

    if (is_int($id)) {

      $this->author = $id;
    }
    else if (is_string($id)) {

      $this->author_name = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function authors(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->author__in = $ids; break;

      case 'NOT IN':

        $this->author__not_in = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function category($id) {

    if (is_int($id)) {

      $this->cat = $id;
    }
    else if (is_string($id)) {

      $this->category_name = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function categories(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->category__in = $ids; break;

      case 'NOT IN':

        $this->category__not_in = $ids; break;

      case 'AND':

        $this->category__and = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function tag($id) {

    if (is_int($id)) {

      $this->tag_id = $id;
    }
    else if (is_string($id)) {

      $this->tag = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function tags(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->tag__in = $ids; break;

      case 'NOT IN':

        $this->tag__not_in = $ids; break;

      case 'AND':

        $this->tag__and = $ids; break;

      case 'SLUG IN':

        $this->tag_slug__in = $ids; break;

      case 'SLUG AND':

        $this->tag_slug__and = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function taxonomy($taxonomy, $terms, $field = 'term_id', $operator = 'IN', $includeChildren = true) {

    $this->taxonomies[] = compact('taxonomy', 'field', 'terms', 'includeChildren', 'operator');

    $this->tax_query = $this->taxonomies;

    return $this;
  }

  public function taxonomies(Closure $callback, $relation = 'AND') {

    call_user_func($callback, $this);

    if (count($this->taxonomies) > 1) {

      $this->taxonomies = array_merge(['relation' => $relation], $this->taxonomies);
    }

    $this->tax_query = $this->taxonomies;

    return $this;
  }

  public function taxonomiesSub(Closure $callback, $relation = 'AND') {

    $query = (new static(new WP_Query))->setModel($this->model);

    $query->taxonomies($callback, $relation);

    $this->taxonomies = array_merge($this->taxonomies, [$query->tax_query]);

    $this->tax_query = $this->taxonomies;

    return $this;
  }

  public function search($keyword) {

    $this->s = $keyword;

    return $this;
  }

  public function post($id) {

    if (is_int($id)) {

      $this->p = $id;
    }
    else if (is_string($id)) {

      $this->name = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function posts(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $ids = count($ids) > 0 ? $ids : [PHP_INT_MAX];

        $this->post__in = $ids; break;

      case 'NOT IN':

        $this->post__not_in = $ids; break;

      case 'NAME IN':

        $this->post_name__in = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function page($id) {

    if (is_int($id)) {

      $this->page_id = $id;
    }
    else if (is_string($id)) {

      $this->pagename = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function parent($id) {

    $this->post_parent = $id;

    return $this;
  }

  public function parents(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->post_parent__in = $ids; break;

      case 'NOT IN':

        $this->post_parent__not_in = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function hasPassword($hasPassword = null) {

    $this->has_password = $hasPassword;

    return $this;
  }

  public function password($password) {

    $this->post_password = $password;

    return $this;
  }

  public function type($type) {

    $this->post_type = $type;

    return $this;
  }

  public function status($status) {

    $this->post_status = $status;

    return $this;
  }

  public function paginate($count, $page = 1, $offset = 0, $type = null) {

    if (is_int($count) && $count >= 0) {

      $this->nopaging = false;

      if (str_is($type, 'ARCHIVE')) {

        $this->posts_per_archive_page = $count;
      }
      else {

        $this->posts_per_page = $count;
      }

      if ($offset > 0) {

        $this->offset = ($offset + ($page - 1) * $count);
      }
      else {

        $this->offset = $offset;

        if (str_is($type, 'STATIC FRONT PAGE')) {

          $this->page = $page;
        }
        else {

          $this->paged = $page;
        }
      }
    }
    else if ($count === -1 || $count === false) {

      $this->nopaging = true;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function ignoreStickyPosts() {

    $this->ignore_sticky_posts = true;

    return $this;
  }

  public function order($order = 'DESC') {

    $this->order = $order;

    return $this;
  }

  // TODO: Multi-dimensional orderBy
  public function orderBy($orderBy = 'date', $order = 'DESC') {

    $this->orderby = $orderBy;
    $this->order = $order;

    return $this;
  }

  // TODO: Date Query implementation
  public function date() {}

  public function metaCompare($compare) {

    $this->meta_compare = $compare;

    return $this;
  }

  public function metaKey($key) {

    $this->meta_key = $key;

    return $this;
  }

  public function metaType($type) {

    $this->meta_type = $type;

    return $this;
  }

  public function metaValue($value) {

    $this->meta_value = $value;

    return $this;
  }

  public function meta($key, $value, $compare = '=', $type = 'CHAR') {

    $meta = compact('key', 'value', 'compare', 'type');

    if (in_array($compare, ['EXISTS', 'NOT EXISTS'])) {

      unset($meta['value']);
    }

    $this->metas[] = $meta;

    $this->meta_query = $this->metas;

    return $this;
  }

  public function metas(Closure $callback, $relation = 'AND') {

    call_user_func($callback, $this);

    if (count($this->metas) > 1) {

      $this->metas = array_merge(['relation' => $relation], $this->metas);
    }

    $this->meta_query = $this->metas;

    return $this;
  }

  public function metasSub(Closure $callback, $relation = 'AND') {

    $query = (new static(new WP_Query))->setModel($this->model);

    $query->metas($callback, $relation);

    $this->metas = array_merge($this->metas, [$query->meta_query]);

    $this->meta_query = $this->metas;

    return $this;
  }

  public function permission($perm) {

    $this->perm = $perm;

    return $this;
  }

  public function mimeType($mimeType) {

    $this->post_mime_type = $mimeType;

    return $this;
  }

  public function cacheResults($enable = true) {

    $this->cache_results = $enable;

    return $this;
  }

  public function updatePostMetaCache($enable = true) {

    $this->update_post_meta_cache = $enable;

    return $this;
  }

  public function updatePostTermCache($enable = true) {

    $this->update_post_term_cache = $enable;

    return $this;
  }

  public function suppressFilters() {

    $this->suppress_filters = true;

    return $this;
  }
}
