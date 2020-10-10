<?php

namespace TechSoft\Laravel\Banner;

use Illuminate\Support\Facades\Cache;
use TechOnline\Laravel\Dao\ModelUtil;

class BannerUtil
{
    const CACHE_KEY_PREFIX = 'banner.';

    public static function listByPosition($position = 'home')
    {
        return ModelUtil::model('banner')->where(['position' => $position])->orderBy('sort', 'asc')->get()->toArray();
    }

    public static function listByPositionWithCache($position = 'home', $minutes = 60)
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . $position, $minutes, function () use ($position) {
            return self::listByPosition($position);
        });
    }

    public static function clearCache($position)
    {
        Cache::forget(self::CACHE_KEY_PREFIX . $position);
    }
}