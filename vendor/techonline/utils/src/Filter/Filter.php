<?php

namespace TechOnline\Utils\Filter;


interface Filter
{
    public function add($key);

    public function has($key);

    public function save($file);

    public function restore($file);
}