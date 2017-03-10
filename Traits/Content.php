<?php

namespace Expresser\PostType\Traits;

trait Content
{
    private $enablePostContentFilters = true;

    public function getPostContentAttribute($value)
    {
        if ($this->isPasswordRequired()) {
            $value = $this->password_form;
        } else {
            if ($this->enabledPostContentFilters()) {
                $value = apply_filters('the_content', $value);
                $value = str_replace(']]>', ']]&gt;', $value);
            }
        }

        return $value;
    }

    public function hasPostContent()
    {
        return !empty($this->post_content);
    }

    public function enablePostContentFilters($enablePostContentFilters = true)
    {
        $this->enablePostContentFilters = $enablePostContentFilters;

        return $this;
    }

    public function enabledPostContentFilters()
    {
        return $this->enablePostContentFilters;
    }

    public function suppressPostContentFilters($suppressPostContentFilters = true)
    {
        return $this->enablePostContentFilters(!$suppressPostContentFilters);
    }

    public function suppressedPostContentFilters()
    {
        return !$this->enabledPostContentFilters();
    }
}
