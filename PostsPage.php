<?php

namespace Expresser\PostType;

class PostsPage extends Page
{
    public function newQuery()
    {
        $query = parent::newQuery();

        $id = get_option('page_for_posts');

        if (is_numeric($id) && ($id = (int)$id) > 0) {
            $query->post($id);
        } else {
            $query->post(PHP_INT_MAX);
        }

        return $query;
    }
}
