<?php

namespace TechSoft\Laravel\Sms;

use TechOnline\Laravel\Type\BaseType;

class SmsSender implements BaseType
{
    const SOFT_API = 'softApi';
    const ALIYUN = 'aliyun';
    const JUHE = 'juhe';

    public static function getList()
    {
        return [
            self::SOFT_API => '通用接口',
            self::ALIYUN => '阿里云',
            self::JUHE => '聚合',
        ];
    }

}