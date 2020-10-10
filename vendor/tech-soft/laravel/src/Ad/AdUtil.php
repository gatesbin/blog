<?php

namespace TechSoft\Laravel\Ad;

use Illuminate\Support\Facades\Cache;
use TechOnline\Laravel\Dao\ModelUtil;

class AdUtil
{
    const CACHE_KEY_PREFIX = 'soft.ad.';

    public static function listByPosition($position)
    {
        return ModelUtil::model('ad')->where(['position' => $position])->orderBy('sort', 'asc')->get()->toArray();
    }

    public static function listByPositionWithCache($position = 'home', $minutes = 60)
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . $position, $minutes, function () use ($position) {
            return self::listByPosition($position);
        });
    }

    public function clearCache($position)
    {
        Cache::forget(self::CACHE_KEY_PREFIX . $position);
    }
}