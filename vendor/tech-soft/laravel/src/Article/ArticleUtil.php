<?php

namespace TechSoft\Laravel\Article;

use Illuminate\Support\Facades\Cache;
use TechOnline\Laravel\Dao\ModelUtil;

class ArticleUtil
{
    const CACHE_KEY_PREFIX = 'soft.article.';

    public static function listByPosition($position = 'home')
    {
        return ModelUtil::model('article')->where(['position' => $position])->orderBy('id', 'asc')->get()->toArray();
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