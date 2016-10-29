<?php

namespace Expresser\PostType\Traits;

trait Content
{
    protected $suppressPostContentFilters = false;

    public function getPostContentAttribute($value)
    {
        if (post_password_required($this->ID)) {
            $value = get_the_password_form($this->ID);
        }

        if (!$this->suppressPostContentFilters) {
            $value = apply_filters('the_content', $value);
        }

        $this->suppressPostContentFilters = false;

        if (!empty($value)) {
            return $value;
        }
    }

    public function hasPostContent()
    {
        return !empty($this->post_content);
    }

    public function suppressPostContentFilters($suppressPostContentFilters)
    {
        $this->suppressPostContentFilters = $suppressPostContentFilters;

        return $this;
    }
}
