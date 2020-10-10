<?php

namespace TechSoft\Laravel\Api;

use TechOnline\Laravel\Type\BaseType;

class Platform implements BaseType
{
    const ANDROID = 1;
    const IOS = 2;

    public static function getList()
    {
        return [
            self::ANDROID => 'Android',
            self::IOS => 'iOS',
        ];
    }

}