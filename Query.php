<?php

namespace Expresser\PostType;

use Closure;
use Expresser\Support\Query as BaseQuery;
use InvalidArgumentException;
use WP_Query;

class Query extends BaseQuery
{
    private $statuses = ['publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'];

    public function __construct(WP_Query $query)
    {
        parent::__construct($query);
    }

    public function execute()
    {
        $posts = $this->query->get_posts();

        return $posts;
    }

    public function author($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('author', $id);
        } elseif (is_string($id)) {
            $this->setQueryVar('author_name', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function authors(array $ids, $operator = 'IN')
    {
        switch ($operator) {
            case 'IN':
                $this->setQueryVar('author__in', $ids);
                break;
            case 'NOT IN':
                $this->setQueryVar('author__not_in', $ids);
                break;
            default:
                throw new InvalidArgumentException();
        }

        return $this;
    }

    public function category($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('cat', $id);
        } elseif (is_string($id)) {
            $this->setQueryVar('category_name', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function categories(array $ids, $operator = 'IN')
    {
        switch ($operator) {
            case 'IN':
                $this->setQueryVar('category__in', $ids);
                break;
            case 'NOT IN':
                $this->setQueryVar('category__not_in', $ids);
                break;
            case 'AND':
                $this->setQueryVar('category__and', $ids);
                break;
            default:
                throw new InvalidArgumentException();
        }

        return $this;
    }

    public function tag($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('tag_id', $id);
        } elseif (is_string($id)) {
            $this->setQueryVar('tag', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function tags(array $ids, $operator = 'IN')
    {
        switch ($operator) {
            case 'IN':
                $this->setQueryVar('tag__in', $ids);
                break;
            case 'NOT IN':
                $this->setQueryVar('tag__not_in', $ids);
                break;
            case 'AND':
                $this->setQueryVar('tag__and', $ids);
                break;
            case 'SLUG IN':
                $this->setQueryVar('tag_slug__in', $ids);
                break;
            case 'SLUG AND':
                $this->setQueryVar('tag_slug__and', $ids);
                break;
            default:
                throw new InvalidArgumentException();
        }

        return $this;
    }

    public function taxonomy($taxonomy, $terms, $field = 'term_id', $operator = 'IN', $include_children = true)
    {
        $tax_query = compact('taxonomy', 'field', 'terms', 'include_children', 'operator');

        $tax_query = array_merge($this->getQueryVar('tax_query'), [$tax_query]);

        $this->setQueryVar('tax_query', $tax_query);

        return $this;
    }

    public function taxonomies(Closure $callback, $relation = 'AND')
    {
        call_user_func($callback, $this);

        $tax_query = $this->getQueryVar('tax_query');

        if (count($tax_query) > 1) {

            $tax_query = array_merge(['relation' => $relation], $tax_query);

            $this->setQueryVar('tax_query', $tax_query);
        }

        return $this;
    }

    public function taxonomiesSub(Closure $callback, $relation = 'AND')
    {
        $query = (new static(new WP_Query));

        $query->taxonomies($callback, $relation);

        $tax_query = $query->getQueryVar('tax_query');

        $tax_query = array_merge($this->getQueryVar('tax_query'), [$tax_query]);

        $this->setQueryVar('tax_query', $tax_query);

        return $this;
    }

    public function search($keyword)
    {
        $this->setQueryVar('s', $keyword);

        return $this;
    }

    public function post($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('p', $id);
        } elseif (is_string($id)) {
            $this->setQueryVar('name', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function posts(array $ids, $operator = 'IN')
    {
        switch ($operator) {
            case 'IN':
                $ids = (count($ids) > 0 ? $ids : [PHP_INT_MAX]);

                $this->setQueryVar('post__in', $ids);
                break;
            case 'NOT IN':
                $this->setQueryVar('post__not_in', $ids);
                break;
            case 'NAME IN':
                $this->setQueryVar('post_name__in', $ids);
                break;
            default:
                throw new InvalidArgumentException();
        }

        return $this;
    }

    public function page($id)
    {
        if (is_int($id)) {
            $this->setQueryVar('page_id', $id);
        } elseif (is_string($id)) {
            $this->setQueryVar('pagename', $id);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function parent($id)
    {
        $this->setQueryVar('post_parent', $id);

        return $this;
    }

    public function parents(array $ids, $operator = 'IN')
    {
        switch ($operator) {
            case 'IN':
                $this->setQueryVar('post_parent__in', $ids);
                break;
            case 'NOT IN':
                $this->setQueryVar('post_parent__not_in', $ids);
                break;
            default:
                throw new InvalidArgumentException();
        }

        return $this;
    }

    public function hasPassword($hasPassword = null)
    {
        $this->setQueryVar('has_password', $hasPassword);

        return $this;
    }

    public function password($password)
    {
        $this->setQueryVar('post_password', $password);

        return $this;
    }

    public function type($type)
    {
        $this->setQueryVar('post_type', $type);

        return $this;
    }

    public function status($status)
    {
        $this->setQueryVar('post_status', $status);

        return $this;
    }

    public function paginate($count, $page = 1, $offset = 0, $type = null)
    {
        if (is_int($count) && $count >= 0) {
            $this->setQueryVar('nopaging', false);

            if (str_is($type, 'ARCHIVE')) {
                $this->setQueryVar('posts_per_archive_page', $count);
            } else {
                $this->setQueryVar('posts_per_page', $count);
            }

            if ($offset > 0) {
                $offset = ($offset + ($page - 1) * $count);

                $this->setQueryVar('offset', $offset);
            } else {
                $this->setQueryVar('offset', $offset);

                if (str_is($type, 'STATIC FRONT PAGE')) {
                    $this->setQueryVar('page', $page);
                } else {
                    $this->setQueryVar('paged', $page);
                }
            }
        } elseif ($count === -1 || $count === false) {
            $this->setQueryVar('nopaging', true);
        } else {
            throw new InvalidArgumentException();
        }

        return $this;
    }

    public function limit($limit)
    {
        return $this->paginate($limit);
    }

    public function ignoreStickyPosts()
    {
        $this->setQueryVar('ignore_sticky_posts', true);

        return $this;
    }

    public function order($order = 'DESC')
    {
        $this->setQueryVar('order', $order);

        return $this;
    }

    // TODO: Multi-dimensional orderBy
    public function orderBy($orderBy = 'date', $order = 'DESC')
    {
        $this->setQueryVar('orderby', $orderBy);
        $this->setQueryVar('order', $order);

        return $this;
    }

    // TODO: Date Query implementation
    public function date()
    {
        return $this;
    }

    public function metaCompare($compare)
    {
        $this->setQueryVar('meta_compare', $compare);

        return $this;
    }

    public function metaKey($key)
    {
        $this->setQueryVar('meta_key', $key);

        return $this;
    }

    public function metaType($type)
    {
        $this->setQueryVar('meta_type', $type);

        return $this;
    }

    public function metaValue($value)
    {
        $this->setQueryVar('meta_value', $value);

        return $this;
    }

    public function meta($key, $value, $compare = '=', $type = 'CHAR')
    {
        $meta_query = compact('key', 'value', 'compare', 'type');

        if (in_array($compare, ['EXISTS', 'NOT EXISTS'])) {
            unset($meta_query['value']);
        }

        $meta_query = array_merge($this->getQueryVar('meta_query'), [$meta_query]);

        $this->setQueryVar('meta_query', $meta_query);

        return $this;
    }

    public function metas(Closure $callback, $relation = 'AND')
    {
        call_user_func($callback, $this);

        $meta_query = $this->getQueryVar('meta_query');

        if (count($meta_query) > 1) {

            $meta_query = array_merge(['relation' => $relation], $meta_query);

            $this->setQueryVar('meta_query', $meta_query);
        }

        return $this;
    }

    public function metasSub(Closure $callback, $relation = 'AND')
    {
        $query = (new static(new WP_Query));

        $query->metas($callback, $relation);

        $meta_query = $query->getQueryVar('meta_query');

        $meta_query = array_merge($this->getQueryVar('meta_query'), [$meta_query]);

        $this->setQueryVar('meta_query', $meta_query);

        return $this;
    }

    public function permission($perm)
    {
        $this->setQueryVar('perm', $perm);

        return $this;
    }

    public function mimeType($mimeType)
    {
        $this->setQueryVar('post_mime_type', $mimeType);

        return $this;
    }

    public function cacheResults($enable = true)
    {
        $this->setQueryVar('cache_results', $enable);

        return $this;
    }

    public function updatePostMetaCache($enable = true)
    {
        $this->setQueryVar('update_post_meta_cache', $enable);

        return $this;
    }

    public function updatePostTermCache($enable = true)
    {
        $this->setQueryVar('update_post_term_cache', $enable);

        return $this;
    }

    public function suppressFilters($suppress = true)
    {
        $this->setQueryVar('suppress_filters', $suppress);

        return $this;
    }

    protected function initQueryVars()
    {
        $this->setQueryVar('meta_query', []);
        $this->setQueryVar('tax_query', []);
    }
}
