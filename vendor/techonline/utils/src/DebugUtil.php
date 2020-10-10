<?php

namespace TechOnline\Utils;


class DebugUtil
{
    public static function track($flag)
    {
        static $time = [];
        if (!isset($time[$flag])) {
            $time[$flag] = microtime(true);
        }
        return sprintf('%d', (microtime(true) - $time[$flag]) * 1000) . 'ms';
    }
}