<?php

namespace TechSoft\Laravel\Pay\Services;

use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Payment\Client\Charge;
use Payment\Common\PayException;
use Payment\Config;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Api\ApiSessionUtil;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Pay\Events\OrderPayedEvent;
use TechSoft\Laravel\Pay\Types\PayOrderStatus;
use TechSoft\Laravel\Pay\Types\PayType;
use TechSoft\Laravel\SoftApi\SoftApi;

class PayOrderUtil
{
    public static function createPayOrder($biz, $bizId, $payType, $feeTotal, $param = [])
    {
        ModelUtil::delete('pay_order', ['biz' => $biz, 'bizId' => $bizId]);
        $order = [];
        $order['biz'] = $biz;
        $order['bizId'] = $bizId;
        $order['payType'] = $payType;
        $order['status'] = PayOrderStatus::NEW_ORDER;
        $order['payOrderId'] = null;
        $order['feeTotal'] = $feeTotal;
        $order['timePayCreated'] = null;
        $order['timePay'] = null;
        $order['feeRefund'] = null;
        $order['timeRefundCreated'] = null;
        $order['timeRefundSuccess'] = null;
        $order['timeClosed'] = null;
        $order['param'] = json_encode($param);
        $order = ModelUtil::insert('pay_order', $order);
        $order['param'] = $param;
        return $order;
    }

    public static function update($id, $data)
    {
        return ModelUtil::update('pay_order', ['id' => $id], $data);
    }

    public static function get($id)
    {
        $m = ModelUtil::get('pay_order', ['id' => $id]);
        ModelUtil::decodeRecordJson($m, 'param');
        return $m;
    }

    public static function getByBizAndBizId($biz, $bizId)
    {
        return ModelUtil::get('pay_order', [
            'biz' => $biz,
            'bizId' => $bizId,
        ]);
    }

    
    public static function create($biz, $bizId, $payType, $feeTotal, $title, $body, $redirect = null, $option = [])
    {
        $option['param'] = [
            'redirect' => $redirect,
        ];

        switch ($payType) {
            case PayType::ALIPAY_WEB:
                return self::createAlipayWeb($biz, $bizId, $feeTotal, $title, $body, $option);

            case PayType::ALIPAY:
                return self::createAlipay($biz, $bizId, $feeTotal, $title, $body, $option);

            case PayType::WECHAT:
                return self::createWechat($biz, $bizId, $feeTotal, $title, $body, $option);

            case PayType::WECHAT_MOBILE:
                return self::createWechatMobile($biz, $bizId, $feeTotal, $title, $body, $option);

            case PayType::WECHAT_MINI_PROGRAM:
                return self::createWechatMiniProgram($biz, $bizId, $feeTotal, $title, $body, $option);

            case PayType::PAY_OFFLINE:
                return self::createPayOffline($biz, $bizId, $feeTotal, $title, $body, $option);
        }
        return Response::generate(-1, 'error payType');
    }

    private static function createPayOffline($biz, $bizId, $feeTotal, $title, $body, $option)
    {
        if (!ConfigUtil::get('payPayOfflineOn', false)) {
            return Response::generate(-1, 'pay_offline pay not enable');
        }
        $order = self::createPayOrder($biz, $bizId, PayType::PAY_OFFLINE, $feeTotal, $option['param']);
        $api = SoftApi::instance(ConfigUtil::get('payPayOfflineAppId'), ConfigUtil::get('payPayOfflineAppSecret'));
        $ret = $api->payOfflineCreate(
            config('pay.payOrderOutTradeNoPrefix') . '_' . $order['id'],
            $feeTotal,
            action('\TechSoft\Laravel\Pay\Controllers\NotifyController@index', ['payType' => PayType::PAY_OFFLINE]),
            action('\TechSoft\Laravel\Pay\Controllers\ReturnController@index', ['payType' => PayType::PAY_OFFLINE])
        );
        if ($ret['code']) {
            return Response::generate(-1, '订单创建失败:' . $ret['msg']);
        }
        self::update($order['id'], ['status' => PayOrderStatus::CREATED]);
        return Response::generate(0, null, ['link' => $ret['data']['pay_url']]);
    }

    public static function getWechatApp()
    {
        $payStoragePath = base_path('storage/cache/pay/');
        if (!file_exists($payStoragePath)) {
            @mkdir($payStoragePath, 0777, true);
        }

        if (!file_exists($certPath = $payStoragePath . 'wechat_cert.pem')) {
            file_put_contents($certPath, ConfigUtil::get('payWechatFileCert'));
        }
        if (!file_exists($keyPath = $payStoragePath . 'wechat_key.pem')) {
            file_put_contents($keyPath, ConfigUtil::get('payWechatFileKey'));
        }

        $options = [
            'debug' => true,
            'app_id' => ConfigUtil::get('payWechatAppId'),
            'secret' => ConfigUtil::get('payWechatAppSecret'),
            'token' => ConfigUtil::get('payWechatAppToken'),
            'aes_key' => null,
            'log' => [
                'level' => 'debug',
                'file' => storage_path('logs/wechat_pay.log'),
            ],
            'payment' => [
                'merchant_id' => ConfigUtil::get('payWechatMerchantId'),
                'key' => ConfigUtil::get('payWechatKey'),
                'device_info' => 'WEB',
                'cert_path' => $certPath,
                'key_path' => $keyPath,
            ],
        ];
                return new Application($options);
    }

    private function createWechat($biz, $bizId, $feeTotal, $title, $body, $option)
    {
        if (!ConfigUtil::get('payWechatOn', false)) {
            return Response::generate(-1, 'wechat pay not enable');
        }

        $order = self::createPayOrder($biz, $bizId, PayType::WECHAT, $feeTotal, $option['param']);

        $attributes = [
            'trade_type' => 'NATIVE',
            'body' => $title,
            'detail' => $body,
            'out_trade_no' => config('pay.payOrderOutTradeNoPrefix') . '_' . $order['id'],
            'total_fee' => intval($feeTotal * 100),
            'notify_url' => action('\TechSoft\Laravel\Pay\Controllers\NotifyController@index', ['payType' => PayType::WECHAT]),
        ];

        if (isset($option['limitPay']) && $option['limitPay'] == 'no_credit') {
            $attributes['limit_pay'] = 'no_credit';
        }

        $app = self::getWechatApp();

        $ret = $app->payment->prepare(new Order($attributes));
                if (!isset($ret['return_code']) || $ret['return_code'] != 'SUCCESS') {
            return Response::generate(-1, '创建订单失败:1:(' . (isset($ret['return_msg']) ? $ret['return_msg'] : 'NULL') . ')');
        }

        if (!isset($ret['result_code'])) {
            return Response::generate(-1, '订单创建失败:2');
        }

        if ($ret['result_code'] != 'SUCCESS') {
            
            $errMsg = $ret['err_code_des'];
            return Response::generate(-1, '创建订单失败:3:(' . $errMsg . ')');
        }

        
        self::update($order['id'], ['status' => PayOrderStatus::CREATED]);
        $codeUrl = $ret['code_url'];
        return Response::generate(0, null, ['codeUrl' => $codeUrl, 'successRedirect' => ApiSessionUtil::get('payRedirect')]);

    }

    public static function getWechatMobileApp()
    {
        $payStoragePath = base_path('storage/cache/pay/');
        if (!file_exists($payStoragePath)) {
            @mkdir($payStoragePath, 0777, true);
        }

        if (!file_exists($certPath = $payStoragePath . 'wechat_mobile_cert.pem')) {
            file_put_contents($certPath, ConfigUtil::get('payWechatMobileFileCert'));
        }
        if (!file_exists($keyPath = $payStoragePath . 'wechat_mobile_key.pem')) {
            file_put_contents($keyPath, ConfigUtil::get('payWechatMobileFileKey'));
        }

        $options = [
            'debug' => true,
            'app_id' => ConfigUtil::getWithEnv('payWechatMobileAppId'),
            'secret' => ConfigUtil::getWithEnv('payWechatMobileAppSecret'),
            'token' => ConfigUtil::getWithEnv('payWechatMobileAppToken'),
            'aes_key' => null,
            'log' => [
                'level' => 'debug',
                'file' => storage_path('logs/wechat_pay.log'),
            ],
            'payment' => [
                'merchant_id' => ConfigUtil::getWithEnv('payWechatMobileMerchantId'),
                'key' => ConfigUtil::getWithEnv('payWechatMobileKey'),
                'device_info' => 'WEB',
                'cert_path' => $certPath,
                'key_path' => $keyPath,
            ],
        ];

        return new Application($options);
    }

    public static function getWechatMiniProgramApp()
    {
        $payStoragePath = base_path('storage/cache/pay/');
        if (!file_exists($payStoragePath)) {
            @mkdir($payStoragePath, 0777, true);
        }

        if (!file_exists($certPath = $payStoragePath . 'wechat_mini_program_cert.pem')) {
            file_put_contents($certPath, ConfigUtil::get('payWechatMiniProgramFileCert'));
        }
        if (!file_exists($keyPath = $payStoragePath . 'wechat_mini_program_key.pem')) {
            file_put_contents($keyPath, ConfigUtil::get('payWechatMiniProgramFileKey'));
        }

        $options = [
            'debug' => true,
            'app_id' => ConfigUtil::getWithEnv('payWechatMiniProgramAppId'),
            'secret' => ConfigUtil::getWithEnv('payWechatMiniProgramAppSecret'),
            'token' => ConfigUtil::getWithEnv('payWechatMiniProgramAppToken'),
            'aes_key' => null,
            'log' => [
                'level' => 'debug',
                'file' => storage_path('logs/wechat_pay.log'),
            ],
            'payment' => [
                'merchant_id' => ConfigUtil::getWithEnv('payWechatMiniProgramMerchantId'),
                'key' => ConfigUtil::getWithEnv('payWechatMiniProgramKey'),
                'device_info' => 'WEB',
                'cert_path' => $certPath,
                'key_path' => $keyPath,
            ],
        ];

        return new Application($options);
    }

    private function createWechatMobile($biz, $bizId, $feeTotal, $title, $body, $option)
    {
        if (!ConfigUtil::get('payWechatMobileOn', false)) {
            return Response::generate(-1, 'wechat mobile pay not enable');
        }

        if (empty($option['openId'])) {
            return Response::generate(-1, 'wechat mobile openId empty');
        }

        $order = self::createPayOrder($biz, $bizId, PayType::WECHAT_MOBILE, $feeTotal, $option['param']);

        $attributes = [
            'openid' => $option['openId'],
            'trade_type' => 'JSAPI',
            'body' => $title,
            'detail' => $body,
            'out_trade_no' => config('pay.payOrderOutTradeNoPrefix') . '_' . $order['id'],
            'total_fee' => intval($feeTotal * 100),
            'notify_url' => action('\TechSoft\Laravel\Pay\Controllers\NotifyController@index', ['payType' => PayType::WECHAT_MOBILE]),
        ];

        if (isset($option['limitPay']) && $option['limitPay'] == 'no_credit') {
            $attributes['limit_pay'] = 'no_credit';
        }

        $app = self::getWechatMobileApp();

        $ret = $app->payment->prepare(new Order($attributes))->toArray();

        if (!isset($ret['return_code']) || $ret['return_code'] != 'SUCCESS') {
            return Response::generate(-1, '创建订单失败:2:(' . $ret['return_msg'] . ')');
        }

        if (!isset($ret['result_code'])) {
            return Response::generate(-1, '订单创建失败:2');
        }

        if ($ret['result_code'] == 'SUCCESS') {
            
            self::update($order['id'], [
                'status' => PayOrderStatus::CREATED,
                'payOrderId' => $ret['prepay_id'],
                'timePayCreated' => Carbon::now(),
            ]);
            $json = $app->payment->configForPayment($ret['prepay_id']);
            return Response::generate(0, null, ['json' => $json, 'successRedirect' => ApiSessionUtil::get('payRedirect')]);
        } else {
            
            $errMsg = $ret['err_code_des'];
            return Response::generate(-1, '创建订单失败:4:(' . $errMsg . ')');
        }

    }

    private function createWechatMiniProgram($biz, $bizId, $feeTotal, $title, $body, $option)
    {
        if (!ConfigUtil::get('payWechatMiniProgramOn', false)) {
            return Response::generate(-1, 'wechat mini program pay not enable');
        }

        if (empty($option['openId'])) {
            return Response::generate(-1, 'wechat mini program openId empty');
        }

        $order = self::createPayOrder($biz, $bizId, PayType::WECHAT_MINI_PROGRAM, $feeTotal, $option['param']);

        $attributes = [
            'openid' => $option['openId'],
            'trade_type' => 'JSAPI',
            'body' => $title,
            'detail' => $body,
            'out_trade_no' => config('pay.payOrderOutTradeNoPrefix') . '_' . $order['id'],
            'total_fee' => intval($feeTotal * 100),
            'notify_url' => action('\TechSoft\Laravel\Pay\Controllers\NotifyController@index', ['payType' => PayType::WECHAT_MINI_PROGRAM]),
        ];

        if (isset($option['limitPay']) && $option['limitPay'] == 'no_credit') {
            $attributes['limit_pay'] = 'no_credit';
        }

        $app = self::getWechatMiniProgramApp();

        $ret = $app->payment->prepare(new Order($attributes))->toArray();

        if (!isset($ret['return_code']) || $ret['return_code'] != 'SUCCESS') {
            return Response::generate(-1, '创建订单失败:2:(' . $ret['return_msg'] . ')');
        }

        if (!isset($ret['result_code'])) {
            return Response::generate(-1, '订单创建失败:2');
        }

        if ($ret['result_code'] == 'SUCCESS') {
            
            self::update($order['id'], [
                'status' => PayOrderStatus::CREATED,
                'payOrderId' => $ret['prepay_id'],
                'timePayCreated' => Carbon::now(),
            ]);
            $json = $app->payment->configForPayment($ret['prepay_id']);
            return Response::generate(0, null, ['json' => $json, 'successRedirect' => ApiSessionUtil::get('payRedirect')]);
        } else {
            
            $errMsg = $ret['err_code_des'];
            return Response::generate(-1, '创建订单失败:4:(' . $errMsg . ')');
        }

    }

    public static function initAlipay()
    {
        config([
            'latrell-alipay.partner_id' => config('pay.alipay.partnerId'),
            'latrell-alipay.seller_id' => config('pay.alipay.sellerId'),
            'latrell-alipay-web.key' => config('pay.alipay.key'),
            'latrell-alipay-web.return_url' => action('\TechSoft\Laravel\Pay\Controllers\ReturnController@index', ['payType' => PayType::ALIPAY]),
            'latrell-alipay-web.notify_url' => action('\TechSoft\Laravel\Pay\Controllers\NotifyController@index', ['payType' => PayType::ALIPAY]),
        ]);
    }

    public static function payConfig($type)
    {
        switch ($type) {
            case PayType::ALIPAY_WEB:
                return [
                    'use_sandbox' => false,
                    'app_id' => ConfigUtil::get('payAlipayWebAppId'),
                    'sign_type' => 'RSA2',
                    'ali_public_key' => ConfigUtil::get('payAlipayWebAliPublicKey'),
                    'rsa_private_key' => ConfigUtil::get('payAlipayWebRSAPrivateKey'),
                    'limit_pay' => [
                                                                                                                                                                                            ],                    'notify_url' => action('\TechSoft\Laravel\Pay\Controllers\NotifyController@index', ['payType' => PayType::ALIPAY_WEB]),
                    'return_url' => action('\TechSoft\Laravel\Pay\Controllers\ReturnController@index', ['payType' => PayType::ALIPAY_WEB]),
                    'return_raw' => true,
                ];
        }
        return null;
    }

    private function createAlipayWeb($biz, $bizId, $feeTotal, $title, $body, $option)
    {
        $config = self::payConfig(PayType::ALIPAY_WEB);
        $order = self::createPayOrder($biz, $bizId, PayType::ALIPAY_WEB, $feeTotal, $option['param']);
        $payData = [
            'body' => $body,
            'subject' => $title,
            'order_no' => config('pay.payOrderOutTradeNoPrefix') . '_' . $order['id'],
            'timeout_express' => time() + 3600 * 24,            'amount' => $feeTotal,
            'return_param' => '',
            'goods_type' => '0',
            'store_id' => '',
            'qr_mod' => '',
        ];
        try {
            $url = Charge::run(Config::ALI_CHANNEL_WEB, $config, $payData);
        } catch (PayException $e) {
            return Response::generate(-1, "创建支付错误(" . $e->errorMessage() . ")");
        }

        self::update($order['id'], ['status' => PayOrderStatus::CREATED]);

        $data = [];
        $data['link'] = $url;

        return Response::generate(0, 'ok', $data);

    }

    private static function createAlipay($biz, $bizId, $feeTotal, $title, $body, $option)
    {

        self::initAlipay();

                if (!config('latrell-alipay.partner_id') || !config('latrell-alipay.seller_id') || !config('latrell-alipay-web.key')) {
            return Response::generate(-1, 'alipay config error');
        }

        $order = self::createPayOrder($biz, $bizId, PayType::ALIPAY, $feeTotal, $option['param']);

        if (!empty($option['alipay_wap'])) {
            $alipay = app('alipay.wap');
        } else {
            $alipay = app('alipay.web');
        }
        $alipay->setOutTradeNo(config('pay.payOrderOutTradeNoPrefix') . '_' . $order['id']);
        $alipay->setTotalFee($feeTotal);
        $alipay->setSubject($title);
        $alipay->setBody($body);

        $data = [];
        $data['link'] = $alipay->getPayLink();

        self::update($order['id'], [
            'status' => PayOrderStatus::CREATED,
        ]);

        return Response::generate(0, 'ok', $data);
    }

    public static function getOrderPay($type, $outTradeNo)
    {
        $order = null;
        $pieces = explode('_', $outTradeNo);
        if (count($pieces) != 2) {
            return null;
        }

        $outTradeNoPrefix = $pieces[0];
        $orderId = $pieces[1];

        if ($outTradeNoPrefix != config('pay.payOrderOutTradeNoPrefix')) {
            return null;
        }
        return self::get($orderId);
    }

    public static function handleOrderPay($type, $outTradeNo)
    {
        $shouldFireEvent = false;
        $order = null;

        Log::notice($type . ' notify -> data verification success.', [
            'out_trade_no' => $outTradeNo
        ]);

        $pieces = explode('_', $outTradeNo);
        if (count($pieces) != 2) {
            return Response::generate(-1, 'outTradeNo error');
        }

        $outTradeNoPrefix = $pieces[0];
        $orderId = $pieces[1];

        if ($outTradeNoPrefix != config('pay.payOrderOutTradeNoPrefix')) {
            return Response::generate(-1, 'outTradeNo prefix not match');
        }

        try {
            ModelUtil::transactionBegin();
            $order = ModelUtil::getWithLock('pay_order', ['id' => $orderId]);
            if (empty($order)) {
                ModelUtil::transactionCommit();
                return Response::generate(-1, 'order not found');
            }
            if ($order['status'] == PayOrderStatus::CREATED) {
                Log::notice("aliay return -> update order to payed");
                ModelUtil::update('pay_order', ['id' => $order['id']], [
                    'status' => PayOrderStatus::PAYED,
                    'timePay' => Carbon::now(),
                ]);
                $shouldFireEvent = true;
            }
            ModelUtil::transactionCommit();
        } catch (\Exception $e) {
            ModelUtil::transactionRollback();
        }

        if ($shouldFireEvent) {
            $event = new OrderPayedEvent();
            $event->biz = $order['biz'];
            $event->bizId = $order['bizId'];
            $event->order = $order;
            Event::fire($event);
            Log::notice('fire-order-log->' . print_r($event, true));
        }

        return Response::generate(0, null);
    }

}
