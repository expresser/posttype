<?php

namespace Expresser\PostType;

class Pdf extends Attachment
{
    public $post_mime_type = 'application/pdf';

    public function newQuery()
    {
        return parent::newQuery()->mimeType($this->post_mime_type);
    }
}
