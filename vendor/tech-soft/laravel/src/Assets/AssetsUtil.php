<?php

namespace TechSoft\Laravel\Assets;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class AssetsUtil
{
    public static function cdn()
    {
        return app('assetsPathDriver')->getCDN('');
    }

    public static function fix($path)
    {
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://') || Str::startsWith($path, '//')) {
            return $path;
        }
        if (Str::startsWith($path, '/')) {
            $path = substr($path, 1);
        }
        return app('assetsPathDriver')->getCDN($path) . app('assetsPathDriver')->getPathWithHash($path);
    }

    public static function fixOrDefault($path, $default)
    {
        if (empty($path)) {
            return self::fix($default);
        }
        return self::fix($path);
    }

    public static function url($file)
    {
        return app('assetsPathDriver')->getCDN($file) . app('assetsPathDriver')->getPathWithHash($file);
    }

    public static function fixCurrentDomain($path)
    {
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://')) {
            return $path;
        }
        if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        if (Request::secure()) {
            $schema = 'https';
        } else {
            $schema = 'http';
        }
        if (Str::startsWith($path, '//')) {
            return $schema . ':' . $path;
        }
        return $schema . '://' . Request::server('HTTP_HOST') . $path;
    }

    public static function fixFull($path)
    {
        $path = self::fix($path);
        if (Str::startsWith($path, 'http://') || Str::startsWith($path, 'https://')) {
            return $path;
        }
        if (Request::secure()) {
            $schema = 'https';
        } else {
            $schema = 'http';
        }
        if (Str::startsWith($path, '//')) {
            return $schema . ':' . $path;
        }
        return $schema . '://' . Request::server('HTTP_HOST') . $path;
    }

    public static function fixFullOrDefault($path, $default = null)
    {
        if (empty($path)) {
            return self::fixFull($default);
        }
        return self::fixFull($path);
    }
}
