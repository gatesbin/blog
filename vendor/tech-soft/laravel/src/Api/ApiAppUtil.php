<?php

namespace TechSoft\Laravel\Api;

use Carbon\Carbon;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Utils\RandomUtil;

class ApiAppUtil
{
    public static function loadByAppId($appId)
    {
        return ModelUtil::get('api_app', ['appId' => $appId]);
    }

    public static function load($id)
    {
        return ModelUtil::get('api_app', ['id' => $id]);
    }

    public static function loadBy($where)
    {
        return ModelUtil::get('api_app', $where);
    }

    public static function getNew($update)
    {
        $update['appId'] = RandomUtil::number(10);
        $update['appSecret'] = RandomUtil::string(32);
        $update = ModelUtil::insert('api_app', $update);
        return ModelUtil::get('api_app', ['id' => $update['id']]);
    }


    public static function modulePermit($apiApp, $module)
    {
        if (empty($apiApp)) {
            return false;
        }
        if (!$apiApp['module' . $module . 'Enable']) {
            return false;
        }
        if (!array_key_exists('module' . $module . 'Expire', $apiApp)) {
            return false;
        }
        if (empty($apiApp['module' . $module . 'Expire'])) {
            return true;
        }
        $date = Carbon::parse($apiApp['module' . $module . 'Expire'])->toDateString();
        if (empty($date)) {
            return false;
        }
        $expire = strtotime($date) + 24 * 3600 - 1;
        if ($expire < time()) {
            return false;
        }
        return true;
    }

    public static function isExpired($apiApp, $module)
    {
        if (empty($apiApp)) {
            return false;
        }
        if (!$apiApp['module' . $module . 'Enable']) {
            return false;
        }
        if (!array_key_exists('module' . $module . 'Expire', $apiApp)) {
            return false;
        }
        if (empty($apiApp['module' . $module . 'Expire'])) {
            return true;
        }
        $date = Carbon::parse($apiApp['module' . $module . 'Expire'])->toDateString();
        if (empty($date)) {
            return false;
        }
        $expire = strtotime($date) + 24 * 3600 - 1;
        if ($expire < time()) {
            return true;
        }
        return false;
    }

    public static function expireDate($apiApp, $module)
    {
        if (!self::modulePermit($apiApp, $module)) {
            return null;
        }
        if (empty($apiApp['module' . $module . 'Expire'])) {
            return null;
        }
        return Carbon::parse($apiApp['module' . $module . 'Expire'])->toDateString();
    }
}
