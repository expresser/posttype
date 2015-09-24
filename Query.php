<?php namespace Expresser\Post;

use Closure;
use Exception;
use InvalidArgumentException;
use WP_Query;

class Query {

  public $metas = array();

  protected $query;

  private $arrays = array('meta_query', 'orderby', 'tax_query');

  public function __construct(WP_Query $query) {

    $this->query = $query;
  }

  public function __call($method, $parameters) {

    $callback = array($this->query, $method);

    if (method_exists($this->query, $method) && is_callable($callback)) {

      $result = call_user_func_array($callback, $parameters);

      if (strtolower($method) === 'get') {

        $parameter = array_shift($parameters);

        if (in_array($parameter, $this->arrays)) {

          if (empty($result)) {

            $result = array();
          }
        }
      }

      return $result;
    }

    $className = get_class($this);

    throw new Exception("Call to undefined method {$className}::{$method}()");
  }

  public function fetch() {

    return $this->get_posts();
  }

  public function author($id) {

    if (is_int($id)) {

      $this->set('author', $id);
    }
    else if (is_string($id)) {

      $this->set('author_name', $id);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function authors(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->set('author__in', $ids); break;

      case 'NOT IN':

        $this->set('author__not_in', $ids); break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function category($id) {

    if (is_int($id)) {

      $this->set('cat', $id);
    }
    else if (is_string($id)) {

      $this->set('category_name', $id);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function categories(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->set('category__in', $ids); break;

      case 'NOT IN':

        $this->set('category__not_in', $ids); break;

      case 'AND':

        $this->set('category__and', $ids); break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function tag($id) {

    if (is_int($id)) {

      $this->set('tag_id', $id);
    }
    else if (is_string($id)) {

      $this->set('tag', $id);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function tags(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->set('tag__in', $ids); break;

      case 'NOT IN':

        $this->set('tag__not_in', $ids); break;

      case 'AND':

        $this->set('tag__and', $ids); break;

      case 'SLUG IN':

        $this->set('tag_slug__in', $ids); break;

      case 'SLUG AND':

        $this->set('tag_slug__and', $ids); break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function taxonomy($taxonomy, $terms, $field = 'term_id', $include_children = true, $operator = 'IN') {

    $taxonomies = $this->get('tax_query');

    $taxonomies[] = compact('taxonomy', 'field', 'terms', 'include_children', 'operator');

    $this->set('tax_query', $taxonomies);

    return $this;
  }

  public function taxonomies(Closure $callback, $relation = 'AND') {

    call_user_func($callback, $this);

    $taxonomies = $this->get('tax_query');

    if (count($taxonomies) > 1) {

      $taxonomies = array_merge(array('relation' => $relation), $taxonomies);
    }

    $this->set('tax_query', $taxonomies);

    return $this;
  }

  public function taxonomiesSub(Closure $callback, $relation = 'AND') {

    $query = self::make();

    call_user_func($callback, $query);

    $taxonomies = $query->get('tax_query');

    if (count($taxonomies) > 1) {

      $taxonomies = array_merge(array('relation' => $relation), $taxonomies);
    }

    $taxonomies = array_merge($this->get('tax_query'), array($taxonomies));

    $this->set('tax_query', $taxonomies);

    return $this;
  }

  public function search($keyword) {

    $this->set('s', $keyword);

    return $this;
  }

  public function post($id) {

    if (is_int($id)) {

      $this->set('p', $id);
    }
    else if (is_string($id)) {

      $this->set('name', $id);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function posts(array $ids, $operator = 'IN') {

    $ids = count($ids) > 0 ? $ids : array(mt_getrandmax());

    switch ($operator) {

      case 'IN':

        $this->set('post__in', $ids); break;

      case 'NOT IN':

        $this->set('post__not_in', $ids); break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function page($id) {

    if (is_int($id)) {

      $this->set('page_id', $id);
    }
    else if (is_string($id)) {

      $this->set('pagename', $id);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function parent($id) {

    $this->set('post_parent', $id);

    return $this;
  }

  public function parents(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->set('post_parent__in', $ids); break;

      case 'NOT IN':

        $this->set('post_parent__not_in', $ids); break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function password($password = null) {

    if (is_null($password) || is_bool($password)) {

      $this->set('has_password', $password);
    }
    else if (is_string($password)) {

      $this->set('post_password', $password);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function type($type) {

    $this->set('post_type', $type);

    return $this;
  }

  public function status($status) {

    $this->set('post_status', $status);

    return $this;
  }

  public function paginate($postsPerPage, $offset = 0, $paged = 1, $isArchivePage = false) {

    if (is_int($postsPerPage) && $postsPerPage >= 0) {

      $this->set('nopaging', false);

      $this->set($isArchivePage ? 'posts_per_archive_page' : 'posts_per_page', $postsPerPage);

      if ($offset > 0) {

        $this->set('offset', $offset + ($paged - 1) * $postsPerPage);
      }
      else {

        $this->set('offset', $offset);
        $this->set('paged', $paged);
      }
    }
    else if ($postsPerPage === -1 || $postsPerPage === false) {

      $this->set('nopaging', true);
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function pageNumber($number) {

    if (is_int($number)) {

      $this->set('page') = $number;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function ignoreStickyPosts() {

    $this->set('ignore_sticky_posts', true);

    return $this;
  }

  public function sort($orderby = 'date', $order = 'DESC') {

    $sort = $this->get('orderby');

    $sort[$orderby] = $order;

    $this->set('orderby', $sort);

    return $this;
  }

  public function raw($key, $value) {

    $this->set($key, $value);

    return $this;
  }

  public static function make() {

    return new static(new WP_Query);
  }
}
