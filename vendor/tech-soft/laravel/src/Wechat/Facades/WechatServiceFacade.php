<?php

namespace TechSoft\Laravel\Wechat\Facades;

use Illuminate\Support\Facades\Facade;

class WechatServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wechatService';
    }
}