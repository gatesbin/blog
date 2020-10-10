<?php

namespace TechSoft\Laravel\Config;

use TechOnline\Laravel\Dao\ModelUtil;
use Illuminate\Support\Facades\Cache;

class ConfigUtil
{
    public static function getWithEnv($key, $defaultValue = null)
    {
        $value = env('CONFIG_' . $key);
        if (empty($value)) {
            $value = self::get($key);
        }
        if (empty($value)) {
            return $defaultValue;
        }
        return $value;
    }

    public static function getArray($key, $defaultValue = [], $useCache = true)
    {
        $value = self::get($key, json_encode($defaultValue), $useCache);
        $value = @json_decode($value, true);
        if (!is_array($value) || empty($value)) {
            $value = [];
        }
        return $value;
    }

    public static function get($key, $defaultValue = '', $useCache = true)
    {
        $cacheFlag = 'config/' . $key;
        $value = null;
        if ($useCache) {
            $value = Cache::get($cacheFlag);
            if (null !== $value) {
                if (empty($value)) {
                    return $defaultValue;
                }
                return $value;
            }
        }
        if (null === $value) {
            $config = ModelUtil::get('config', ['key' => $key]);
            if ($config) {
                Cache::forever($cacheFlag, $config['value']);
                if (empty($config['value'])) {
                    return $defaultValue;
                }
                return $config['value'];
            } else {
                Cache::forever($cacheFlag, $defaultValue);
            }
        }
        return $defaultValue;
    }

    public static function getBoolean($key, $defaultValue = false)
    {
        $value = self::get($key, null);
        if (null === $value) {
            return $defaultValue;
        }
        return $value ? true : false;
    }

    public static function getInteger($key, $defaultValue = 0)
    {
        $value = self::get($key, null);
        if (null === $value) {
            return $defaultValue;
        }
        return intval($value);
    }

    public static function getString($key, $defaultValue = '')
    {
        $value = self::get($key, null);
        if (null === $value) {
            return $defaultValue;
        }
        return '' . $value;
    }

    public static function set($key, $value)
    {
        $config = ModelUtil::get('config', ['key' => $key]);
        if ($config) {
            ModelUtil::update('config', ['id' => $config['id']], ['value' => $value]);
        } else {
            ModelUtil::insert('config', ['key' => $key, 'value' => $value]);
        }
        $cacheFlag = 'config/' . $key;
        Cache::forget($cacheFlag);
    }

}
