<?php namespace Expresser\PostType;

class Pdf extends Attachment {

  public function mimeType() {

    return 'application/pdf';
  }

  public function newQuery() {

    return parent::newQuery()->mimeType($this->mime_type);
  }
}
