<?php

namespace Expresser\PostType\Traits;

trait Excerpt
{
    private $enablePostExcerptFilters = true;

    private $postExcerptLength = 55;

    private $postExcerptMore = ' [&hellip;]';

    public function getPostExcerptAttribute($value)
    {
        if ($this->isPasswordRequired()) {
            $value = 'There is no excerpt because this is a protected post.';
        } else {
            $value = apply_filters('the_excerpt', $value);

            if (empty($value)) {
                $value = $this->suppressPostContentFilters()->post_content;
                $value = strip_shortcodes($value);
                $value = apply_filters('the_content', $value);
                $value = str_replace(']]>', ']]&gt;', $value);
            }

            if ($this->enabledPostExcerptFilters()) {
                $postExcerptLength = apply_filters('excerpt_length', $this->getPostExcerptLength());
                $postExcerptMore = apply_filters('excerpt_more', $this->getPostExcerptMore());

                $value = wp_trim_words($value, $postExcerptLength, $postExcerptMore);
            }
        }

        return $value;
    }

    public function hasPostExcerpt()
    {
        return !empty($this->post_excerpt);
    }

    public function enablePostExcerptFilters($enablePostExcerptFilters = true)
    {
        $this->enablePostExcerptFilters = $enablePostExcerptFilters;

        return $this;
    }

    public function enabledPostExcerptFilters()
    {
        return $this->enablePostExcerptFilters;
    }

    public function suppressPostExcerptFilters($suppressPostExcerptFilters = true)
    {
        return $this->enablePostExcerptFilters(!$suppressPostExcerptFilters);
    }

    public function suppressedPostExcerptFilters()
    {
        return !$this->enabledPostExcerptFilters();
    }

    public function getPostExcerptLength()
    {
        return $this->postExcerptLength;
    }

    public function setPostExcerptLength($postExcerptLength)
    {
        $this->postExcerptLength = $postExcerptLength;

        return $this;
    }

    public function getPostExcerptMore()
    {
        return $this->postExcerptMore;
    }

    public function setPostExcerptMore($postExcerptMore)
    {
        $this->postExcerptMore = $postExcerptMore;

        return $this;
    }
}
