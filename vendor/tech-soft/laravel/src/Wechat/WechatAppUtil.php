<?php

namespace TechSoft\Laravel\Wechat;

use EasyWeChat\Foundation\Application;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Wechat\Types\WechatAuthType;

class WechatAppUtil
{
    public static function oauthMobileApp()
    {
        if (!ConfigUtil::get('oauthWechatMobileEnable')) {
            return null;
        }
        return WechatUtil::app(0, [
            'enable' => true,
            'appId' => ConfigUtil::get('oauthWechatMobileAppId', ''),
            'appSecret' => ConfigUtil::get('oauthWechatMobileAppSecret', ''),
            'appToken' => '',
            'appEncodingKey' => '',
            'authType' => WechatAuthType::CONFIG,
        ], ['payment' => false,]);
    }

    public static function shareApp()
    {
        if (!ConfigUtil::get('shareWechatMobileEnable')) {
            return null;
        }
        return WechatUtil::app(0, [
            'enable' => true,
            'appId' => ConfigUtil::get('shareWechatMobileAppId', ''),
            'appSecret' => ConfigUtil::get('shareWechatMobileAppSecret', ''),
            'appToken' => '',
            'appEncodingKey' => '',
            'authType' => WechatAuthType::CONFIG,
        ], ['payment' => false,]);
    }

    public static function wxApp()
    {
        if (!ConfigUtil::get('wxappEnable')) {
            return null;
        }
        $options = [
                        'mini_program' => [
                'app_id' => ConfigUtil::get('wxappAppId', ''),
                'secret' => ConfigUtil::get('wxappAppSecret', ''),
                                'token' => '',
                'aes_key' => ''
            ],
                    ];
        return new Application($options);
    }

}