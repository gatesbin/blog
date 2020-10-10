<?php

namespace TechOnline\Utils;


class TimezoneUtil
{
    private static $TIMEZONE_BASE = 8;

    
    public static function date($timezone = +8, $format = 'Y-m-d H:i:s', $time = null)
    {
        if (null === $time) {
            $time = time();
        } else if ($time && is_string($time)) {
            $time = strtotime($time);
        }
        return date($format, $time - (self::$TIMEZONE_BASE - $timezone) * 3600);
    }

    public static function dateRevert($timezone = +8, $format = 'Y-m-d H:i:s', $time = null)
    {
        if (null === $time) {
            $time = time();
        } else if ($time && is_string($time)) {
            $time = strtotime($time);
        }
        return date($format, $time + (self::$TIMEZONE_BASE - $timezone) * 3600);
    }

    public static function dayRange($day, $timezone = +8, $format = 'Y-m-d H:i:s')
    {
        $time = strtotime($day . ' 00:00:00');
        return [
            'start' => TimezoneUtil::dateRevert($timezone, $format, $time),
            'end' => TimezoneUtil::dateRevert($timezone, $format, $time + 3600 * 24 - 1)
        ];
    }

    
    
    public static function calcTimezones($targetHour, $timestamp = null, $baseTimezone = null)
    {
        if (null === $timestamp) {
            $timestamp = time();
        }
        if (null === $baseTimezone) {
            $baseTimezone = self::$TIMEZONE_BASE;
        }
        $timestamp = intval($timestamp / 3600) * 3600;
        $timezones = [];
        $hour = intval(date('H', $timestamp));
        $timezone = $baseTimezone + $targetHour - $hour;
        if ($timezone > 12) {
            $timezone -= 24;
        } else if ($timezone < -12) {
            $timezone += 24;
        }
        $timezones[] = $timezone;
        if ($timezone == 12 || $timezone == -12) {
            $timezones[] = -$timezone;
        }
        return $timezones;
    }


}
