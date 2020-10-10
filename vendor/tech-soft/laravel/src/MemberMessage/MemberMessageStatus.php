<?php

namespace TechSoft\Laravel\MemberMessage;

use TechOnline\Laravel\Type\BaseType;

class MemberMessageStatus implements BaseType
{
    const UNREAD = 1;
    const READ = 2;

    public static function getList()
    {
        return [
            self::UNREAD => '未读',
            self::READ => '已读',
        ];
    }
}