<?php

namespace TechSoft\Laravel\Oauth;

use TechSoft\Laravel\Config\ConfigUtil;

class OauthUtil
{
    public static function hasOauth()
    {
        if (self::isWechatMobileEnable()) {
            return true;
        }
        if (self::isQQEnable()) {
            return true;
        }
        if (self::isWeiboEnable()) {
            return true;
        }
        if (self::isWechatEnable()) {
            return true;
        }
        if (self::isWechatMiniProgramEnable()) {
            return true;
        }
        return false;
    }

    public static function isWechatMobileEnable()
    {
        if (ConfigUtil::getWithEnv('oauthWechatMobileEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isWechatMiniProgramEnable()
    {
        if (ConfigUtil::getWithEnv('oauthWechatMiniProgramEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isQQEnable()
    {
        if (ConfigUtil::getWithEnv('oauthQQEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isWechatEnable()
    {
        if (ConfigUtil::getWithEnv('oauthWechatEnable', false)) {
            return true;
        }
        return false;
    }

    public static function isWeiboEnable()
    {
        if (ConfigUtil::getWithEnv('oauthWeiboEnable', false)) {
            return true;
        }
        return false;
    }
}