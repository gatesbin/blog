<?php

namespace TechSoft\Laravel\Wechat\Support;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Oauth\Driver\Oauth;
use TechSoft\Laravel\Wechat\Facades\WechatAuthorizationServerFacade;
use TechSoft\Laravel\Wechat\WechatUtil;

trait AuthorizationOauthTrait
{
    private $dispatchUrl;
    private $dispatchAction;
    private $loginAction;

    private $config = [];

    protected function setConfig($dispatchUrl, $dispatchAction, $loginAction)
    {
        $this->dispatchUrl = $dispatchUrl;
        $this->dispatchAction = $dispatchAction;
        $this->loginAction = $loginAction;
    }

    protected function initConfig($accountId, &$app)
    {
        $callback = action($this->loginAction, ['accountId' => $accountId]);
        $dispatchPath = action($this->dispatchAction, ['accountId' => $accountId, 'callback' => bin2hex($callback)], false);
        $dispatch = $this->dispatchUrl . $dispatchPath;

        $this->config = [
            'APP_KEY' => $app->account['appId'],
            'APP_SECRET' => 'empty',
            'CALLBACK' => $dispatch,
            'AUTHORIZE' => 'scope=snsapi_base,snsapi_userinfo&state=&component_appid=' . WechatAuthorizationServerFacade::getComponentAppId(),
        ];
    }

    public function jump($accountId = 0)
    {
        $app = WechatUtil::app($accountId);
        if (empty($app)) {
            return Response::send(-1, 'app not found');
        }

        $this->initConfig($accountId, $app);
        $sns = Oauth::getInstance('WechatmobileAuthorization', $this->config);
        $url = $sns->getRequestCodeURL();

        $redirect = Input::get('redirect', '/');
        if (!empty($redirect)) {
            Session::put('oauthRedirect', $redirect);
        }

        return redirect($url);
    }

    
    public function dispatch($accountId = '', $callback = '')
    {
        $app = WechatUtil::app($accountId);
        if (empty($app)) {
            return Response::send(-1, 'app not found');
        }

        $code = Input::get('code', '');
        if (empty($code)) {
            return Response::send(-1, '登录失败(code为空)');
        }

        if (empty($callback)) {
            return Response::send(-1, '登录失败(callback为空)');
        }

        $callback = @hex2bin($callback);
        if (empty($callback)) {
            return Response::send(-1, '登录失败(callback解析失败)');
        }

        if (strpos($callback, '?') === false) {
            $callback .= '?';
        } else {
            $callback .= '&';
        }
        $callback .= 'code=' . urlencode($code);

        return redirect($callback);

    }

    
    protected function parseLogin($accountId = '')
    {
        $app = WechatUtil::app($accountId);
        if (empty($app)) {
            return Response::send(-1, 'app not found');
        }

        $code = Input::get('code', '');
        if (empty($code)) {
            return Response::generate(-1, '登录失败(code为空)');
        }

        $token = null;
        $openid = null;
        $oauth = null;
        try {
            $this->initConfig($accountId, $app);
            $oauth = Oauth::getInstance('WechatmobileAuthorization', $this->config);
            $token = $oauth->getAccessToken($code, null, [
                'component_appid' => WechatAuthorizationServerFacade::getComponentAppId(),
                'component_access_token' => WechatAuthorizationServerFacade::getComponentAccessToken(),
            ]);
            $openid = $oauth->openid();
        } catch (\Exception $e) {
            return Response::generate(-1, '登录失败(' . $e->getMessage() . ')');
        }

        if (empty($token) || empty($openid)) {
            return Response::generate(-1, '登录失败(token=' . print_r($token, true) . ',openid=' . $openid . ')');
        }

        return Response::generate(0, null, [
            'openid' => $openid,
            'oauth' => &$oauth
        ]);
    }
}