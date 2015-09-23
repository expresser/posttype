<?php namespace Expresser\Post;

use Closure;
use InvalidArgumentException;
use WP_Query;

class Query {

  protected $query;

  protected $params = array();

  protected $metas = array();

  protected $taxonomies = array();

  public function __construct(WP_Query $query) {

    $this->query = $query;
  }

  public function get(array $params = array()) {

    $params = array_merge_recursive($this->params, $params);

    return $this->query->query($params);
  }

  public function author($id) {

    if (is_int($id)) {

      $this->params['author'] = $id;
    }
    else if (is_string($id)) {

      $this->params['author_name'] = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function authors(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->params['author__in'] = $ids; break;

      case 'NOT IN':

        $this->params['author__not_in'] = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function category($id) {

    if (is_int($id)) {

      $this->params['cat'] = $id;
    }
    else if (is_string($id)) {

      $this->params['category_name'] = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function categories(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->params['category__in'] = $ids; break;

      case 'NOT IN':

        $this->params['category__not_in'] = $ids; break;

      case 'AND':

        $this->params['category__and'] = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function tag($id) {

    if (is_int($id)) {

      $this->params['tag_id'] = $id;
    }
    else if (is_string($id)) {

      $this->params['tag'] = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function tags(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->params['tag__in'] = $ids; break;

      case 'NOT IN':

        $this->params['tag__not_in'] = $ids; break;

      case 'AND':

        $this->params['tag__and'] = $ids; break;

      case 'SLUG IN':

        $this->params['tag_slug__in'] = $ids; break;

      case 'SLUG AND':

        $this->params['tag_slug__and'] = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function taxonomy($taxonomy, $terms, $field = 'term_id', $include_children = true, $operator = 'IN') {

    $this->taxonomies[] = compact('taxonomy', 'field', 'terms', 'include_children', 'operator');

    $this->params['tax_query'] = $this->taxonomies;

    return $this;
  }

  public function taxonomies(Closure $callback, $relation = 'AND') {

    $this->taxonomies['relation'] = $relation;

    return call_user_func($callback, $this);
  }

  public function search($keyword) {

    $this->params['s'] = $keyword;

    return $this;
  }

  public function post($id) {

    if (is_int($id)) {

      $this->params['p'] = $id;
    }
    else if (is_string($id)) {

      $this->params['name'] = $id;
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

        $this->params['post__in'] = $ids; break;

      case 'NOT IN':

        $this->params['post__not_in'] = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function page($id) {

    if (is_int($id)) {

      $this->params['page_id'] = $id;
    }
    else if (is_string($id)) {

      $this->params['pagename'] = $id;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function parent($id) {

    $this->params['post_parent'] = $id;

    return $this;
  }

  public function parents(array $ids, $operator = 'IN') {

    switch ($operator) {

      case 'IN':

        $this->params['post_parent__in'] = $ids; break;

      case 'NOT IN':

        $this->params['post_parent__not_in'] = $ids; break;

      default:

        throw new InvalidArgumentException;
    }

    return $this;
  }

  public function password($password = null) {

    if (is_null($password) || is_bool($password)) {

      $this->params['has_password'] = $password;
    }
    else if (is_string($password)) {

      $this->params['post_password'] = $password;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function type($type) {

    $this->params['post_type'] = $type;

    return $this;
  }

  public function status($status) {

    $this->params['post_status'] = $status;

    return $this;
  }

  public function paginate($postsPerPage, $offset = 0, $paged = 1, $ignoreStickyPosts = false) {

    if (is_int($postsPerPage) && $postsPerPage >= 0) {

      $this->params['nopaging'] = false;

      $this->params['posts_per_page'] = $postsPerPage;

      if ($offset > 0) {

        $this->params['offset'] = $offset + ($paged - 1) * $postsPerPage;
      }
      else {

        $this->params['offset'] = $offset;
        $this->params['paged'] = $paged;
      }
    }
    else if ($postsPerPage === -1 || $postsPerPage === false) {

      $this->params['nopaging'] = true;
    }
    else {

      throw new InvalidArgumentException;
    }

    $this->params['ignore_sticky_posts'] = $ignoreStickyPosts;

    return $this;
  }














  public function orderBy($orderby = 'date', $order = 'DESC') {

    $this->params['orderby'] = $orderby;
    $this->params['order'] = $order;

    return $this;
  }

  public static function make() {

    return new static(new WP_Query);
  }
}
