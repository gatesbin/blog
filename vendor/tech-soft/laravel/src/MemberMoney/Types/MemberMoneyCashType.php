<?php

namespace TechSoft\Laravel\MemberMoney\Types;


use TechOnline\Laravel\Type\BaseType;

class MemberMoneyCashType implements BaseType
{
    const ALIPAY = 1;

    public static function getList()
    {
        return [
            self::ALIPAY => '支付宝',
        ];
    }

}