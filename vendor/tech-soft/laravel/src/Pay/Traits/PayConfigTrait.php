<?php

namespace TechSoft\Laravel\Pay\Traits;

use TechSoft\Laravel\Config\ConfigUtil;

trait PayConfigTrait
{
    public function bootPayConfig()
    {
                try {

            if (env('PAY_ALIPAY_ON', false)) {
                $this->app->config->set('pay.alipay', [
                    'partnerId' => env('PAY_ALIPAY_PARTNER_ID', null),
                    'sellerId' => env('PAY_ALIPAY_SELLER_ID', null),
                    'key' => env('PAY_ALIPAY_KEY', null),
                ]);
            } else if (ConfigUtil::get('payAlipayOn', false)) {
                $this->app->config->set('pay.alipay', [
                    'partnerId' => ConfigUtil::get('payAlipayPartnerId'),
                    'sellerId' => ConfigUtil::get('payAlipaySellerId'),
                    'key' => ConfigUtil::get('payAlipayKey'),
                ]);
            }

        } catch (\Exception $e) {
                    }
    }
}