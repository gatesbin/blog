<?php

namespace TechOnline\Utils;


class ReUtil
{
    public static function group($regx, $text, $groupIndex)
    {
        if (preg_match($regx, $text, $mat)) {
            return $mat[$groupIndex];
        }
        return null;
    }

    public static function group0($regx, $text)
    {
        return self::group($regx, $text, 0);
    }

    public static function group1($regx, $text)
    {
        return self::group($regx, $text, 1);
    }
}
