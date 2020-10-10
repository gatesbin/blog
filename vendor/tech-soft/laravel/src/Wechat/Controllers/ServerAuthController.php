<?php

namespace TechSoft\Laravel\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Wechat\Facades\WechatAuthorizationServerFacade;
use TechSoft\Laravel\Wechat\Types\WechatAuthStatus;
use TechSoft\Laravel\Wechat\Types\WechatAuthType;
use TechSoft\Laravel\Wechat\WechatServiceUtil;

class ServerAuthController extends Controller
{
    private $dispatchUrl;
    private $dispatchAction;
    private $bindAction;

    protected function setConfig($dispatchUrl, $dispatchAction, $bindAction)
    {
        $this->dispatchUrl = $dispatchUrl;
        $this->dispatchAction = $dispatchAction;
        $this->bindAction = $bindAction;
    }

    public function jump()
    {
        $callback = action($this->bindAction);
        $dispatchPath = action($this->dispatchAction, ['callback' => bin2hex($callback)], false);
        $dispatch = $this->dispatchUrl . $dispatchPath;

        $authUrl = WechatAuthorizationServerFacade::getAuthUrl($dispatch);

        return Response::send(0, '正在跳转...', null, $authUrl);
    }

    public function dispatch($callback = '')
    {
        $authCode = Input::get('auth_code');
        if (empty($authCode)) {
            return Response::send(-1, 'auth code empty');
        }

        if (empty($callback)) {
            return Response::send(-1, 'callback empty');
        }

        $callback = @hex2bin($callback);
        if (empty($callback)) {
            return Response::send(-1, 'callback parse error');
        }

        if (strpos($callback, '?') === false) {
            $callback .= '?';
        } else {
            $callback .= '&';
        }
        $callback .= 'auth_code=' . urlencode($authCode);

        return redirect($callback);
    }

    public function bind()
    {
        $authCode = Input::get('auth_code');
        if (empty($authCode)) {
            return Response::send(-1, 'auth code empty');
        }

        $queryInfo = WechatAuthorizationServerFacade::getQueryAuth($authCode);

        $wechatAccountAppId = $queryInfo['authorization_info']['authorizer_appid'];
        $wechatAccount = WechatServiceUtil::loadAccountByAppIdAndAuthType($wechatAccountAppId, WechatAuthType::OAUTH);

        if (empty($wechatAccount)) {
                        $data = [];
            $data['authType'] = WechatAuthType::OAUTH;
            $data['authStatus'] = WechatAuthStatus::NORMAL;
            $data['enable'] = true;
            $data['appId'] = $wechatAccountAppId;
            $data['alias'] = WechatServiceUtil::generateAccountAlias();
            $data['authorizerRefreshToken'] = $queryInfo['authorization_info']['authorizer_refresh_token'];
            $wechatAccount = WechatServiceUtil::add($data);
            $accountId = $wechatAccount['id'];
        } else {
                        $data = [];
            $data['authStatus'] = WechatAuthStatus::NORMAL;
            $data['authorizerRefreshToken'] = $queryInfo['authorization_info']['authorizer_refresh_token'];
            $wechatAccount = WechatServiceUtil::update($wechatAccount['id'], $data);
            $accountId = $wechatAccount['id'];
        }

        $authorizerInfo = WechatAuthorizationServerFacade::getAuthorizerInfo($wechatAccountAppId);
        $data = [];
        $data['name'] = $authorizerInfo['authorizer_info']['nick_name'];
        if (!empty($authorizerInfo['authorizer_info']['head_img'])) {
            $data['avatar'] = $authorizerInfo['authorizer_info']['head_img'];
        }
        $data['serviceInfo'] = $authorizerInfo['authorizer_info']['service_type_info']['id'];
        $data['verifyInfo'] = $authorizerInfo['authorizer_info']['verify_type_info']['id'];
        $data['username'] = $authorizerInfo['authorizer_info']['user_name'];
        $data['wechat'] = $authorizerInfo['authorizer_info']['alias'];

        $data['func'] = [];
        foreach ($authorizerInfo['authorization_info']['func_info'] as $funcInfo) {
            $data['func'][] = $funcInfo['funcscope_category']['id'];
        }

        WechatServiceUtil::update($accountId, $data);

        Session::put('authWechatAccountId', $accountId);

        $wechatAccount = WechatServiceUtil::load($accountId);
        $ret = $this->checkAndBind($wechatAccount);

        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }

        return Response::send(0, '绑定成功(请关闭当前页面)');
    }

    protected function checkAndBind($wechatAccount)
    {
        return Response::generate(0, null);
    }

}