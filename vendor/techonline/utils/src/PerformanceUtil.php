<?php

namespace TechOnline\Utils;


class PerformanceUtil
{
    public static function recordTime($group = '_default')
    {
        static $time = [];
        if (!isset($time[$group])) {
            $time[$group] = microtime(true);
        }
        return intval((microtime(true) - $time[$group]) * 1000);
    }
}
