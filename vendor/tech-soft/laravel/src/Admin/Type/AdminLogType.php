<?php

namespace TechSoft\Laravel\Admin\Type;

use TechOnline\Laravel\Type\BaseType;

class AdminLogType implements BaseType
{
    const INFO = 1;
    const ERROR = 2;

    public static function getList()
    {
        return [
            self::INFO => '信息',
            self::ERROR => '错误',
        ];
    }
}
