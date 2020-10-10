<?php

namespace TechSoft\Laravel\Wechat\Facades;

use Illuminate\Support\Facades\Facade;

class WechatAuthorizationServerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wechatAuthorizationServer_1';
    }
}