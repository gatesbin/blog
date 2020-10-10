<?php

namespace TechSoft\Laravel\Controllers;


use EasyWeChat\Encryption\Encryptor;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Wechat\WechatServiceUtil;

class NotifyController extends Controller
{
    public function index()
    {

        $wechatAuthorizationAppId = ConfigUtil::get('wechatAuthorizationAppId');
        $wechatAuthorizationAppSecret = ConfigUtil::get('wechatAuthorizationAppSecret');
        $wechatAuthorizationToken = ConfigUtil::get('wechatAuthorizationToken');
        $wechatAuthorizationEncodingKey = ConfigUtil::get('wechatAuthorizationEncodingKey');

        $msgSignature = Input::get('msg_signature');
        $nonce = Input::get('nonce');
        $timestamp = Input::get('timestamp');

        $postXML = $wechatAuthorizationServer->getRawContent();

        $encryptor = new Encryptor($wechatAuthorizationAppId, $wechatAuthorizationToken, $wechatAuthorizationEncodingKey);

        $msg = $encryptor->decryptMsg($msgSignature, $nonce, $timestamp, $postXML);

        Log::notice("WECHAT_NOTIFY " . json_encode($msg, true));

        switch ($msg['InfoType']) {
                                    case 'component_verify_ticket':
                ConfigUtil::set('wechatAuthorizationComponentVerifyTicket', $msg['ComponentVerifyTicket']);
                Log::notice("WECHAT_NOTIFY UPDATE wechatAuthorizationComponentVerifyTicket -> " . $msg['ComponentVerifyTicket']);
                break;
            case 'unauthorized':
                
                $appId = $msg['AuthorizerAppid'];
                $account = WechatServiceUtil::loadAccountByAppIdAndAuthType($appId, WechatAuthType::OAUTH);
                if (empty($account)) {
                    return;
                }
                WechatServiceUtil::update($account['id'], ['authStatus' => WechatAuthStatus::CANCELED]);
                break;
            case 'authorized':
                
                break;
            case 'updateauthorized':
                
                break;

        }
    }
}