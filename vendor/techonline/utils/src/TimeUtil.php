<?php

namespace TechOnline\Utils;


class TimeUtil
{
    public static function humanTimeLength($timeSeconds)
    {
        $pcs = [];
        if ($timeSeconds >= 24 * 3600) {
            $v = intval($timeSeconds / (24 * 3600));
            $pcs[] = $v . '天';
            $timeSeconds %= (24 * 3600);
        }
        if ($timeSeconds >= 3600) {
            $v = intval($timeSeconds / 3600);
            $pcs[] = $v . '小时';
            $timeSeconds %= 3600;
        }
        if ($timeSeconds >= 60) {
            $v = intval($timeSeconds / 60);
            $pcs[] = $v . '分钟';
            $timeSeconds %= 60;
        }
        if ($timeSeconds > 0) {
            $pcs[] = $timeSeconds . '秒';
        }
        return join('', $pcs);
    }

    
    public static function isDatetimeEmpty($datetime)
    {
        $timestamp = strtotime($datetime);
        if (empty($timestamp) || $timestamp < 0) {
            return true;
        }
        return false;
    }

    
    public static function isDateEmpty($date)
    {
        $timestamp = strtotime($date);
        if (empty($timestamp) || $timestamp < 0) {
            return true;
        }
        return false;
    }

    public static function isTimeEmpty($time)
    {
        $timestamp = strtotime('2019-01-01 ' . $time);
        if (empty($timestamp) || $timestamp < 0) {
            return true;
        }
        return false;
    }


    public static function isDateExpired($expire)
    {
        if (self::isDateEmpty($expire)) {
            return false;
        }
        if (strtotime($expire) < time()) {
            return true;
        }
        return false;
    }


}