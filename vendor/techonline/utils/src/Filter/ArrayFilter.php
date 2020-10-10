<?php

namespace TechOnline\Utils\Filter;


class ArrayFilter implements Filter
{
    const NAME = 'array';

    private $map = array();

    public static function build()
    {
        return new ArrayFilter();
    }

    public function save($file)
    {
        file_put_contents($file, serialize($this->map));
    }

    public function restore($file)
    {
        $data = file_get_contents($file);
        $this->map = unserialize($data);
    }

    public function add($key)
    {
        $this->map[$key] = true;
    }

    public function has($key)
    {
        return !empty($this->map[$key]);
    }
}