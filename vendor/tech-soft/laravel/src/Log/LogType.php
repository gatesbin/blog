<?php

namespace TechSoft\Laravel\Log;

use TechOnline\Laravel\Type\BaseType;

class LogType implements BaseType
{
    const INFO = 1;
    const ERROR = 2;

    public static function getList()
    {
        return [
            self::INFO => '消息',
            self::ERROR => '错误',
        ];
    }
}