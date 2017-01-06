<?php

namespace Expresser\PostType;

use Expresser\Support\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    public function find($id)
    {
        return $this->post($id)->first();
    }

    public function findAll(array $ids)
    {
        return $this->posts($ids)->get();
    }

    public function findByName($name)
    {
        return $this->post($name)->first();
    }

    public function findBySlug($name)
    {
        return $this->page($name)->first();
    }

    public function first()
    {
        return $this->limit(1)->get()->first();
    }
}
