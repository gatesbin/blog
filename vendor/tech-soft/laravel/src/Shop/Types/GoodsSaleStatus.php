<?php

namespace TechSoft\Laravel\Shop\Types;

use TechOnline\Laravel\Type\BaseType;

class GoodsSaleStatus implements BaseType
{
    const ON = 1;
    const OFF = 2;

    public static function getList()
    {
        return [
            self::ON => '正在销售',
            self::OFF => '已下架',
        ];
    }

}