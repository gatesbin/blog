<?php

namespace TechSoft\Laravel\Shop\Types;

use TechOnline\Laravel\Type\BaseType;

class OrderStatus implements BaseType
{
    const WAIT_PAY = 1;
    const WAIT_SHIPPING = 2;
    const WAIT_CONFIRM = 3;
    const COMPLETED = 50;
    const CANCEL_EXPIRED = 98;
    const CANCEL = 99;
    const CANCEL_QUEUE = 100;

    public static function getList()
    {
        return [
            self::WAIT_PAY => '待付款',
            self::WAIT_SHIPPING => '待发货',
            self::WAIT_CONFIRM => '待收货',
            self::COMPLETED => '订单完成',
            self::CANCEL_EXPIRED => '订单过期取消',
            self::CANCEL => '订单取消',
            self::CANCEL_QUEUE => '正在取消',
        ];
    }
}