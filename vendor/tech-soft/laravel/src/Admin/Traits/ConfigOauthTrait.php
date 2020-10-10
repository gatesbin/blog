<?php

namespace TechSoft\Laravel\Admin\Traits;


use TechOnline\Laravel\Http\Request;
use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;

trait ConfigOauthTrait
{
    public function oauthWechat(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'oauthWechat',
            'pageTitle' => '微信授权登录',
            'fields' => [

                'oauthWechatMobileEnable' => ['type' => FieldSwitch::class, 'title' => '[手机] 开启微信授权登录', 'desc' => ''],
                'oauthWechatMobileProxy' => ['type' => FieldText::class, 'title' => '[手机] 授权回调域名代理', 'desc' => '如不清楚此参数意义,请留空'],
                'oauthWechatMobileAppId' => ['type' => FieldText::class, 'title' => '[手机] AppId', 'desc' => ''],
                'oauthWechatMobileAppSecret' => ['type' => FieldText::class, 'title' => '[手机] AppSecret', 'desc' => ''],

                'oauthWechatEnable' => ['type' => FieldSwitch::class, 'title' => '[PC端] 开启PC微信扫码登录', 'desc' => '回调域名请填写 <code>' . Request::server('HTTP_HOST') . '</code>'],
                'oauthWechatAppId' => ['type' => FieldText::class, 'title' => '[PC端] AppId', 'desc' => ''],
                'oauthWechatAppSecret' => ['type' => FieldText::class, 'title' => '[PC端] AppSecret', 'desc' => ''],

            ]
        ]);
    }

    public function oauthQQ(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'oauthQQ',
            'pageTitle' => 'QQ授权登录',
            'fields' => [
                'oauthQQEnable' => ['type' => FieldSwitch::class, 'title' => '开启QQ授权登录', 'desc' => '回调地址请填写 <code>' . Request::schema() . '://' . Request::server('HTTP_HOST') . '/oauth_callback_qq</code>'],
                'oauthQQKey' => ['type' => FieldText::class, 'title' => 'APP ID', 'desc' => ''],
                'oauthQQAppSecret' => ['type' => FieldText::class, 'title' => 'APP KEY', 'desc' => ''],
            ]
        ]);
    }

    public function oauthWeibo(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'oauthWeibo',
            'pageTitle' => '微博授权登录',
            'fields' => [
                'oauthWeiboEnable' => ['type' => FieldSwitch::class, 'title' => '开启微博授权登录', 'desc' => '回调地址请填写 <code>' . Request::schema() . '://' . Request::server('HTTP_HOST') . '/oauth_callback_weibo</code>'],
                'oauthWeiboKey' => ['type' => FieldText::class, 'title' => 'Key', 'desc' => ''],
                'oauthWeiboAppSecret' => ['type' => FieldText::class, 'title' => 'AppSecret', 'desc' => ''],
            ]
        ]);
    }

    public function oauthWechatMiniProgram(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'wechatMiniProgram',
            'pageTitle' => '微信小程序',
            'fields' => [
                'oauthWechatMiniProgramEnable' => ['type' => FieldSwitch::class, 'title' => '启用小程序', 'desc' => ''],
                'oauthWechatMiniProgramAppId' => ['type' => FieldText::class, 'title' => 'AppId', 'desc' => ''],
                'oauthWechatMiniProgramAppSecret' => ['type' => FieldText::class, 'title' => 'AppSecret', 'desc' => ''],
            ]
        ]);
    }

}