<?php

namespace TechSoft\Laravel\Post;


use TechOnline\Laravel\Cache\CacheUtil;
use TechOnline\Laravel\Dao\ModelUtil;

class PostUtil
{
    public static function clearCache()
    {
        CacheUtil::forget('post');
    }

    public static function all()
    {
        return CacheUtil::remember('post', 3600, function () {
            return ModelUtil::all('post', [], ['id', 'created_at', 'updated_at', 'title', 'position'], ['sort', 'asc']);
        });
    }

    public static function listPost($position)
    {
        $all = self::all();
        $posts = array_filter($all, function ($item) use ($position) {
            return $item['position'] == $position;
        });
        return array_values($posts);
    }

    public static function get($id)
    {
        return ModelUtil::get('post', $id);
    }
}