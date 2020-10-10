<?php

namespace TechSoft\Laravel\Pay\Controllers;

use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Api\ApiSessionUtil;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Pay\Services\PayOrderUtil;
use TechSoft\Laravel\Pay\Types\PayType;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use TechSoft\Laravel\SoftApi\SoftApi;

class ReturnController extends Controller
{

    public function index($payType = '')
    {
        switch ($payType) {

            case PayType::PAY_OFFLINE:
                $api = SoftApi::instance(ConfigUtil::get('payPayOfflineAppId'), ConfigUtil::get('payPayOfflineAppSecret'));
                $input = InputPackage::buildFromInput();
                if (!$api->signCheck($input->all())) {
                    return Response::send(-1, '支付失败:-1');
                }
                $order = PayOrderUtil::getOrderPay(PayType::PAY_OFFLINE, $input->getTrimString('biz_sn'));
                if (empty($order)) {
                    return Response::send(-2, '支付失败:-2');
                }
                $redirect = empty($order['param']['redirect']) ? null : $order['param']['redirect'];
                if (null === $redirect) {
                    return Response::send(0, '支付成功');
                } else {
                    return Response::send(0, null, null, $redirect);
                }
                break;

                        case PayType::ALIPAY_WEB:
                $redirect = ApiSessionUtil::get('payRedirect');
                if (null === $redirect) {
                    return Response::send(0, '支付成功');
                } else {
                    return Response::send(0, null, null, $redirect);
                }
                break;

            case PayType::ALIPAY:

                PayOrderUtil::initAlipay();

                if (!app('alipay.web')->verify()) {
                    Log::notice('alipay return -> query data verification fail.', [
                        'data' => Request::getQueryString()
                    ]);
                    return Response::send(-1, '支付失败:-1');
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
                            return Response::send(-1, '支付失败:' . $ret['msg']);
                        }

                        $redirect = ApiSessionUtil::get('payRedirect');
                        if (null === $redirect) {
                            return Response::send(0, '支付成功');
                        } else {
                            return Response::send(0, null, null, $redirect);
                        }
                }

                return Response::send(-1, '支付失败');
        }
    }

}
