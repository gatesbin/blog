<?php

namespace TechSoft\Laravel\Crawl;


interface BaseQueue
{
    function append($handler, $param = [], $id = null);

    function exists($id);

    function size();

    function poll();
}