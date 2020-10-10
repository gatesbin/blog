<?php

namespace TechSoft\Laravel\Pay\Controllers;

use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Request;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Pay\Services\PayOrderUtil;
use TechSoft\Laravel\Pay\Types\PayType;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Payment\Client\Notify;
use Payment\Config;
use Payment\Notify\PayNotifyInterface;
use TechSoft\Laravel\SoftApi\SoftApi;

if (class_exists('\Payment\Notify\PayNotifyInterface')) {
    class PayAlipayWebNotify implements PayNotifyInterface
    {
        public function notifyProcess(array $data)
        {
            switch ($data['trade_status']) {
                case 'TRADE_SUCCESS':
                case 'TRADE_FINISHED':
                    Log::notice('alipay notify -> data verification success.', [
                        'out_trade_no' => $data['out_trade_no'],
                        'trade_no' => $data['trade_no'],
                    ]);

                    $ret = PayOrderUtil::handleOrderPay(PayType::ALIPAY_WEB, $data['out_trade_no']);
                    if ($ret['code']) {
                        return false;
                    }
                    return true;

            }
            return true;
        }

    }
}

class NotifyController extends Controller
{
    public function index($payType = '')
    {
        switch ($payType) {

            case PayType::PAY_OFFLINE:
                $api = SoftApi::instance(ConfigUtil::get('payPayOfflineAppId'), ConfigUtil::get('payPayOfflineAppSecret'));
                $input = InputPackage::buildFromInput();
                if (!$api->signCheck($input->all())) {
                    return 'fail';
                }
                $ret = PayOrderUtil::handleOrderPay(PayType::PAY_OFFLINE, $input->getTrimString('biz_sn'));
                if ($ret['code']) {
                    return 'fail';
                }
                return 'success';

            case PayType::ALIPAY_WEB:
                $config = PayOrderUtil::payConfig($payType);
                return Notify::run(Config::ALI_CHARGE, $config, new PayAlipayWebNotify($payOrderService));

            case PayType::ALIPAY:
                PayOrderUtil::initAlipay();
                if (!app('alipay.web')->verify()) {
                    Log::notice('alipay return -> query data verification fail.', [
                        'data' => Request::instance()->getContent()
                    ]);
                    return 'fail';
                }
                switch (Input::get('trade_status')) {
                    case 'TRADE_SUCCESS':
                    case 'TRADE_FINISHED':

                        Log::notice('alipay return -> data verification success.', [
                            'out_trade_no' => Input::get('out_trade_no'),
                            'trade_no' => Input::get('trade_no')
                        ]);

                        $ret = PayOrderUtil::handleOrderPay(PayType::ALIPAY, Input::get('out_trade_no', ''));
                        if ($ret['code']) {
                            return 'fail';
                        }

                        return 'success';
                }
                return 'fail';

            case PayType::WECHAT_MOBILE:

                return PayOrderUtil::getWechatMobileApp()->payment->handleNotify(function ($notify, $successful) use ($payOrderService) {
                    if ($successful) {
                        $ret = PayOrderUtil::handleOrderPay(PayType::WECHAT_MOBILE, $notify->out_trade_no);
                        if ($ret['code']) {
                            return 'fail';
                        }
                        return true;
                    }
                    return true;
                });

            case PayType::WECHAT_MINI_PROGRAM:

                return PayOrderUtil::getWechatMiniProgramApp()->payment->handleNotify(function ($notify, $successful) use ($payOrderService) {
                    if ($successful) {
                        $ret = PayOrderUtil::handleOrderPay(PayType::WECHAT_MINI_PROGRAM, $notify->out_trade_no);
                        if ($ret['code']) {
                            return 'fail';
                        }
                        return true;
                    }
                    return true;
                });

            case PayType::WECHAT:

                return PayOrderUtil::getWechatApp()->payment->handleNotify(function ($notify, $successful) use ($payOrderService) {
                    if ($successful) {
                        $ret = PayOrderUtil::handleOrderPay(PayType::WECHAT, $notify->out_trade_no);
                        if ($ret['code']) {
                            return 'fail';
                        }
                        return true;
                    }
                    return true;
                });

        }
    }

}
