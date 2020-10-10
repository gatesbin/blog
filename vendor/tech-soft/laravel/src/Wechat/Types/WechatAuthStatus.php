<?php
namespace TechSoft\Laravel\Wechat\Types;

use TechOnline\Laravel\Type\BaseType;

class WechatAuthStatus implements BaseType
{
    const NORMAL = 1;
    const CANCELED = 2;

    public static function getList()
    {
        return [
            self::NORMAL => '正常',
            self::CANCELED => '已取消',
        ];
    }

}