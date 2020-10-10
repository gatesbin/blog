<?php

namespace TechOnline\Laravel\Util;


use TechOnline\Utils\CurlUtil;
use TechOnline\Utils\StrUtil;

class FileUtils
{
    public static function savePathToLocal($path, $ext = '')
    {
        $tempPath = public_path('temp/' . md5($path) . $ext);
        if (file_exists($tempPath)) {
            return $tempPath;
        }
        if (StrUtil::startWith($path, 'http://') || StrUtil::startWith($path, 'https://') || StrUtil::startWith($path, '//')) {
            if (StrUtil::startWith($path, '//')) {
                $path = 'http://' . $path;
            }
            $image = CurlUtil::getRaw($path);
            if (empty($image)) {
                return null;
            }
            @mkdir(public_path('temp'));
            file_put_contents($tempPath, $image);
        } else {
            if (StrUtil::startWith($path, '/')) {
                $path = substr($path, 1);
            }
            $tempPath = public_path($path);
        }
        if (!file_exists($tempPath)) {
            return null;
        }
        return $tempPath;
    }

}