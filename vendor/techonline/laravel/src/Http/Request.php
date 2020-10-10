<?php

namespace TechOnline\Laravel\Http;


class Request
{
    public static function currentPageUrl()
    {
        if (\Illuminate\Support\Facades\Request::ajax()) {
            $redirect = \Illuminate\Support\Facades\Request::server('HTTP_REFERER');
        } else {
            $redirect = \Illuminate\Support\Facades\Request::fullUrl();
        }
        return $redirect;
    }

    public static function currentPageUrlWithOutQueries()
    {
        return \Illuminate\Support\Facades\Request::url();
    }

    public static function mergeQueries($pair = [])
    {
        $gets = (!empty($_GET) && is_array($_GET)) ? $_GET : [];
        foreach ($pair as $k => $v) {
            $gets[$k] = $v;
        }

        $urls = [];
        foreach ($gets as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (is_array($v)) {
                $v = $v[0];
            } else {
                $v = urlencode($v);
            }
            $urls[] = "$k=" . $v;
        }

        return join('&', $urls);
    }

    public static function domain()
    {
        return \Illuminate\Support\Facades\Request::server('HTTP_HOST');
    }

    public static function schema()
    {
        static $schema = null;
        if (null === $schema) {
            if (\Illuminate\Support\Facades\Request::secure()) {
                $schema = 'https';
            } else {
                $schema = 'http';
            }
        }
        return $schema;
    }

    public static function domainUrl()
    {
        return self::schema() . '://' . self::domain();
    }

    public static function isPost()
    {
        return \Illuminate\Support\Facades\Request::isMethod('post');
    }

    
    public static function instance()
    {
        return \Illuminate\Support\Facades\Request::instance();
    }

    public static function headerGet($key, $defaultValue = null)
    {
        return self::instance()->header($key, $defaultValue);
    }

    public static function headerSet($key, $value)
    {
        self::instance()->headers->set($key, $value);
    }

    public static function headers()
    {
        return self::instance()->headers->all();
    }

    public static function ip()
    {
        return self::instance()->ip();
    }

    public static function server($name)
    {
        return self::instance()->server($name);
    }
}