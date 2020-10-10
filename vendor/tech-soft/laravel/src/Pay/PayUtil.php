<?php

namespace TechSoft\Laravel\Pay;

use TechOnline\Laravel\Util\AgentUtil;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Pay\Types\PayType;

class PayUtil
{
    public static function hasPay()
    {
        if (self::isWechatMobileEnable()) {
            return true;
        }
        if (self::isAlipayEnable()) {
            return true;
        }
        if (self::isAlipayWebEnable()) {
            return true;
        }
        if (self::isWechatEnable()) {
            return true;
        }
        if (self::isAlipayManualEnable()) {
            return true;
        }
        if (self::isWechatManualEnable()) {
            return true;
        }
        if (self::isOfflinePayEnable()) {
            return true;
        }
        return false;
    }

    public static function isPayEnable($payType)
    {
        switch ($payType) {
            case PayType::ALIPAY:
                return self::isAlipayEnable();
            case PayType::ALIPAY_WEB:
                return self::isAlipayWebEnable();
            case PayType::WECHAT_MOBILE:
                return self::isWechatMobileEnable();
            case PayType::WECHAT_MINI_PROGRAM:
                return self::isWechatMiniProgramEnable();
            case PayType::WECHAT:
                return self::isWechatEnable();
            case PayType::ALIPAY_MANUAL:
                return self::isAlipayManualEnable();
            case PayType::WECHAT_MANUAL:
                return self::isWechatManualEnable();
            case PayType::OFFLINE_PAY:
                return self::isOfflinePayEnable();
        }
        return false;
    }

    public static function isWechatMobileEnable()
    {
        if (AgentUtil::isWechat()) {
            if (ConfigUtil::getWithEnv('payWechatMobileOn', false)) {
                return true;
            }
        }
        return false;
    }

    public static function isWechatMiniProgramEnable()
    {
        if (AgentUtil::isWechat()) {
            if (ConfigUtil::getWithEnv('payWechatMiniProgramOn', false)) {
                return true;
            }
        }
        return false;
    }

    public static function isWechatEnable()
    {
        if (AgentUtil::isMobile()) {
            return false;
        }
        if (ConfigUtil::get('payWechatOn', false)) {
            return true;
        }
        return false;
    }

    public static function isAlipayEnable()
    {
        if (AgentUtil::isWechat()) {
            return false;
        }
        if (ConfigUtil::getWithEnv('payAlipayOn', false)) {
            return true;
        }
        return false;
    }

    public static function isAlipayManualEnable()
    {
        if (ConfigUtil::get('payAlipayManualOn', false)) {
            return true;
        }
        return false;
    }

    public static function isAlipayWebEnable()
    {
        if (ConfigUtil::get('payAlipayWebOn', false)) {
            return true;
        }
        return false;
    }

    public static function isWechatManualEnable()
    {
        if (ConfigUtil::get('payWechatManualOn', false)) {
            return true;
        }
        return false;
    }

    public static function isOfflinePayEnable()
    {
        if (ConfigUtil::get('payOfflinePayOn', false)) {
            return true;
        }
        return false;
    }
}