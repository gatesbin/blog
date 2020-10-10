<?php

namespace TechSoft\Laravel\Partner;


use Illuminate\Support\Facades\Cache;
use TechOnline\Laravel\Dao\ModelUtil;

class PartnerUtil
{
    const CACHE_KEY_PREFIX = 'soft.partner.';

    public static function listByPosition($position = 'home')
    {
        return ModelUtil::model('partner')->where(['position' => $position])->orderBy('sort', 'asc')->get()->toArray();
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