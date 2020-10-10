<?php

namespace TechOnline\Utils;


class ConstantUtil
{
    public static function dump($cls)
    {
        return (new \ReflectionClass($cls))->getConstants();
    }
}