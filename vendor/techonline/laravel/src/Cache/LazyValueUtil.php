<?php

namespace TechOnline\Laravel\Cache;


class LazyValueUtil
{
    public static function hash($key, $callback)
    {
        return CacheUtil::rememberForever("LazyValue.$key", function () use ($callback) {
            return md5(serialize($callback()));
        });
    }

    public static function notifyChange($key)
    {
        CacheUtil::forget("LazyValue.$key");
    }
}