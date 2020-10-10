<?php

namespace TechSoft\Laravel\Pay\Types;

use TechOnline\Laravel\Type\BaseType;

class PayType implements BaseType
{
    const ALIPAY = 'alipay';
    const WECHAT_MOBILE = 'wechat_mobile';
    const WECHAT_MINI_PROGRAM = 'wechat_mini_program';
    const WECHAT = 'wechat';

    const ALIPAY_MANUAL = 'alipay_manual';
    const WECHAT_MANUAL = 'wechat_manual';

        const ALIPAY_WEB = 'alipay_web';

    const OFFLINE_PAY = 'offline_pay';
    const PAY_OFFLINE = 'pay_offline';

    public static function getList()
    {
        return [
            self::ALIPAY => '支付宝',
            self::ALIPAY_WEB => '支付宝-Web',
            self::WECHAT_MOBILE => '微信手机',
            self::WECHAT_MINI_PROGRAM => '微信小程序',
            self::WECHAT => '微信网页',
            self::ALIPAY_MANUAL => '支付宝手动',
            self::WECHAT_MANUAL => '微信手动',
            self::OFFLINE_PAY => '货到付款',

            self::PAY_OFFLINE => '自助结算',
        ];
    }


}
