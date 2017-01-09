<?php

namespace Expresser\PostType;

use Expresser\Support\Filter;

class Attachment extends Native
{
    public $post_type = 'attachment';

    public $post_status = ['draft', 'inherit'];

    public function getCacheableAccessors()
    {
        return array_merge(parent::getCacheableAccessors(), [
            'filepath',
            'thumbnail_url',
            'url',
        ]);
    }

    public function getCaptionAttribute()
    {
        if (!empty($this->post_excerpt)) {
            return $this->post_excerpt;
        }
    }

    public function getDescriptionAttribute()
    {
        if (!empty($this->post_content)) {
            return $this->post_content;
        }
    }

    public function getFilenameAttribute()
    {
        return basename($this->filepath);
    }

    public function getFilepathAttribute()
    {
        return get_attached_file($this->ID);
    }

    public function getThumbnailUrlAttribute()
    {
        $src = wp_get_attachment_image_src($this->ID, 'thumbnail', true);

        if (is_array($src)) {
            return $src[0];
        }
    }

    public function getUrlAttribute()
    {
        $url = wp_get_attachment_url($this->ID);

        if (Filter::isUrl($url)) {
            return $url;
        }
    }

    public function newQuery()
    {
        return parent::newQuery()->status($this->post_status);
    }
}
