<?php
namespace TechSoft\Laravel\Wechat\Types;

use TechOnline\Laravel\Type\BaseType;

class WechatAuthType implements BaseType
{
    const CONFIG = 1;
    const OAUTH = 2;

    public static function getList()
    {
        return [
            self::CONFIG => '服务器配置公众号',
            self::OAUTH => '第三方授权公众号',
        ];
    }

}