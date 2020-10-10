<?php

namespace TechOnline\Utils;


class MathUtil
{
    public static function in($value, $min, $max)
    {
        return $value >= $min && $value <= $max;
    }
}