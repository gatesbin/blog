<?php

namespace TechSoft\Laravel\Member\Type;

use TechOnline\Laravel\Type\BaseType;

class Gender implements BaseType
{
    const MALE = 1;
    const FEMALE = 2;
    const UNKNOWN = 0;

    public static function getList()
    {
        return [
            self::MALE => '男',
            self::FEMALE => '女',
            self::UNKNOWN => '未知',
        ];
    }

}