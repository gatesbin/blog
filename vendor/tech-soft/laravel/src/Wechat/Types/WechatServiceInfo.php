<?php
namespace TechSoft\Laravel\Wechat\Types;

use TechOnline\Laravel\Type\BaseType;

class WechatServiceInfo implements BaseType
{
    const SUBSCRIPTION = 0;
    const SUBSCRIPTION_FROM_OLD = 1;
    const SERVICE = 2;

    public static function getList()
    {
        return [
            self::SUBSCRIPTION => '订阅号',
            self::SUBSCRIPTION_FROM_OLD => '订阅号(老)',
            self::SERVICE => '服务号',
        ];
    }

}