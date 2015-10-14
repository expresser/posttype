<?php namespace Expresser\Post;

use Closure;
use InvalidArgumentException;
use WP_Query;

class Query {

  protected $query;

  protected $metas = array();

  protected $sorts = array();

  protected $taxonomies = array();

  public function __construct(WP_Query $query) {

    $this->query = $query;
  }

  public function __get($name) {

    return $this->getParameter($name);
  }

  public function __isset($name) {

    return is_null($this->getParameter($name)) === false;
  }

  public function __set($name, $value) {

    $this->setParameter($name, $value);
  }

  public function get() {

    return $this->query->get_posts();
  }

  public function getParameter($name) {

    return $this->getParameterValue($name);
  }

  public function getParameterValue($name) {

    $value = $this->query->get($name);

    if (!empty($value)) return $value;
  }

  public function setParameter($name, $value) {

    $this->query->set($name, $value);
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

  public function taxonomy($taxonomy, $terms, $field = 'term_id', $include_children = true, $operator = 'IN') {

    $this->taxonomies[] = compact('taxonomy', 'field', 'terms', 'include_children', 'operator');

    $this->tax_query = $this->taxonomies;

    return $this;
  }

  public function taxonomies(Closure $callback, $relation = 'AND') {

    call_user_func($callback, $this);

    if (count($this->taxonomies) > 1) {

      $this->taxonomies = array_merge(array('relation' => $relation), $this->taxonomies);
    }

    $this->tax_query = $this->taxonomies;

    return $this;
  }

  public function taxonomiesSub(Closure $callback, $relation = 'AND') {

    $query = self::make();

    $query->taxonomies($callback, $relation);

    $this->taxonomies = array_merge($this->taxonomies, array($query->tax_query));

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

    $ids = count($ids) > 0 ? $ids : array(mt_getrandmax());

    switch ($operator) {

      case 'IN':

        $this->post__in = $ids; break;

      case 'NOT IN':

        $this->post__not_in = $ids; break;

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

  public function password($password = null) {

    if (is_null($password) || is_bool($password)) {

      $this->has_password = $password;
    }
    else if (is_string($password)) {

      $this->post_password = $password;
    }
    else {

      throw new InvalidArgumentException;
    }

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

  public function paginate($postsPerPage, $offset = 0, $paged = 1, $isArchivePage = false) {

    if (is_int($postsPerPage) && $postsPerPage >= 0) {

      $this->nopaging = false;

      if ($isArchivePage) {

        $this->posts_per_archive_page = $postsPerPage;
      }
      else {

        $this->posts_per_page = $postsPerPage;
      }

      if ($offset > 0) {

        $this->offset = ($offset + ($paged - 1) * $postsPerPage);
      }
      else {

        $this->offset = $offset;
        $this->paged = $paged;
      }
    }
    else if ($postsPerPage === -1 || $postsPerPage === false) {

      $this->nopaging = true;
    }
    else {

      throw new InvalidArgumentException;
    }

    return $this;
  }

  public function pageNumber($number) {

    if (is_int($number)) {

      $this->page = $number;
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

  public function sort($orderby = 'date', $order = 'DESC') {

    $sort = $this->orderby;

    $sort[$orderby] = $order;

    $this->orderby = $sort;

    return $this;
  }

  public function raw($key, $value) {

    $this->$key = $value;

    return $this;
  }

  public static function make() {

    return new static(new WP_Query);
  }
}
