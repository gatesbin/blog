<?php

namespace TechSoft\Laravel\Member\Traits;

use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Response;
use TechOnline\Laravel\Redis\RedisUtil;
use TechOnline\Laravel\Util\AtomicUtil;
use TechOnline\Utils\CurlUtil;
use TechOnline\Utils\FileUtil;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Mews\Captcha\Facades\Captcha;
use TechSoft\Laravel\Api\ApiSessionUtil;
use TechSoft\Laravel\Api\ResponseCodes;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Mail\MailTemplate;
use TechSoft\Laravel\Mail\MailUtil;
use TechSoft\Laravel\Member\Events\MemberUserLoginedEvent;
use TechSoft\Laravel\Member\Events\MemberUserPasswordResetedEvent;
use TechSoft\Laravel\Member\Events\MemberUserRegisteredEvent;
use TechSoft\Laravel\Member\MemberUtil;
use TechSoft\Laravel\Oauth\Driver\Oauth;
use TechSoft\Laravel\Oauth\OauthHandle;
use TechSoft\Laravel\Oauth\OauthType;
use TechSoft\Laravel\Oauth\OauthUtil;
use TechSoft\Laravel\Sms\SmsTemplate;
use TechSoft\Laravel\Sms\SmsUtil;
use TechSoft\Laravel\Util\ApiSessionCaptchaUtil;














trait MemberAccountApiControllerTrait
{
    private function getOauthConfig($type, $callback = null)
    {
        $config = [
            'APP_KEY' => null,
            'APP_SECRET' => null,
            'CALLBACK' => $callback,
        ];
        switch ($type) {
            case OauthType::WECHAT_MOBILE:
                if (!OauthUtil::isWechatMobileEnable()) {
                    return null;
                }
                $config['APP_KEY'] = ConfigUtil::getWithEnv('oauthWechatMobileAppId');
                $config['APP_SECRET'] = ConfigUtil::getWithEnv('oauthWechatMobileAppSecret');
                return $config;
            case OauthType::QQ:
                if (!OauthUtil::isQQEnable()) {
                    return null;
                }
                $config['APP_KEY'] = ConfigUtil::getWithEnv('oauthQQKey');
                $config['APP_SECRET'] = ConfigUtil::getWithEnv('oauthQQAppSecret');
                return $config;
            case OauthType::WEIBO:
                if (!OauthUtil::isWeiboEnable()) {
                    return null;
                }
                $config['APP_KEY'] = ConfigUtil::getWithEnv('oauthWeiboKey');
                $config['APP_SECRET'] = ConfigUtil::getWithEnv('oauthWeiboAppSecret');
                return $config;
            case OauthType::WECHAT:
                if (!OauthUtil::isWechatEnable()) {
                    return null;
                }
                $config['APP_KEY'] = ConfigUtil::getWithEnv('oauthWechatAppId');
                $config['APP_SECRET'] = ConfigUtil::getWithEnv('oauthWechatAppSecret');
                return $config;
        }
        return null;
    }

    public function oauthTryLogin($oauthType = null)
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        $type = $input->getType('type', OauthType::class);
        if (empty($type)) {
            $type = $oauthType;
        }
        $oauthOpenId = ApiSessionUtil::get('oauthOpenId');
        if (empty($oauthOpenId)) {
            return Response::json(-1, '用户授权数据为空');
        }
        $oauthUserInfo = ApiSessionUtil::get('oauthUserInfo', []);
        switch ($type) {
            case OauthType::WECHAT_MOBILE:
            case OauthType::WECHAT:
                if (!empty($oauthUserInfo['unionId'])) {
                                        $memberUserId = MemberUtil::getIdByOauth(OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                    if ($memberUserId) {
                        MemberUtil::putOauth($memberUserId, $type, $oauthOpenId);
                        ApiSessionUtil::put('memberUserId', $memberUserId);
                        ApiSessionUtil::forget('oauthOpenId');
                        ApiSessionUtil::forget('oauthUserInfo');
                        return Response::json(0, null, [
                            'memberUserId' => $memberUserId,
                        ]);
                    }
                }
                $memberUserId = MemberUtil::getIdByOauth($type, $oauthOpenId);
                if ($memberUserId) {
                    ApiSessionUtil::put('memberUserId', $memberUserId);
                    ApiSessionUtil::forget('oauthOpenId');
                    ApiSessionUtil::forget('oauthUserInfo');
                    return Response::json(0, null, [
                        'memberUserId' => $memberUserId,
                    ]);
                }
                break;
            case OauthType::QQ:
            case OauthType::WEIBO:
                $memberUserId = MemberUtil::getIdByOauth($type, $oauthOpenId);
                if ($memberUserId) {
                    ApiSessionUtil::put('memberUserId', $memberUserId);
                    ApiSessionUtil::forget('oauthOpenId');
                    ApiSessionUtil::forget('oauthUserInfo');
                    return Response::json(0, null, [
                        'memberUserId' => $memberUserId,
                    ]);
                }
                break;
        }
        return Response::json(0, null, [
            'memberUserId' => 0,
        ]);
    }

    public function oauthBind($oauthType = null)
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        $type = $input->getType('type', OauthType::class);
        if (empty($type)) {
            $type = $oauthType;
        }
        $oauthOpenId = ApiSessionUtil::get('oauthOpenId');
        if (empty($oauthOpenId)) {
            return Response::json(-1, '用户授权数据为空');
        }
        $oauthUserInfo = ApiSessionUtil::get('oauthUserInfo', []);

                if ($this->memberUserId()) {
            switch ($type) {
                case OauthType::WECHAT_MOBILE:
                case OauthType::WECHAT:
                    if (!empty($oauthUserInfo['unionId'])) {
                                                $memberUserId = MemberUtil::getIdByOauth(OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                        if ($memberUserId && $this->memberUserId() != $memberUserId) {
                            MemberUtil::forgetOauth(OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                        }
                        MemberUtil::putOauth($this->memberUserId(), OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                    }
                    $memberUserId = MemberUtil::getIdByOauth($type, $oauthOpenId);
                    if ($memberUserId && $this->memberUserId() != $memberUserId) {
                        MemberUtil::forgetOauth($type, $oauthOpenId);
                    }
                    MemberUtil::putOauth($this->memberUserId(), $type, $oauthOpenId);
                    break;
                case OauthType::QQ:
                case OauthType::WEIBO:
                    $memberUserId = MemberUtil::getIdByOauth($type, $oauthOpenId);
                    if ($memberUserId && $this->memberUserId() != $memberUserId) {
                        MemberUtil::forgetOauth($type, $oauthOpenId);
                    }
                    MemberUtil::putOauth($this->memberUserId(), $type, $oauthOpenId);
                    break;
            }
            ApiSessionUtil::forget('oauthOpenId');
            ApiSessionUtil::forget('oauthUserInfo');
            return Response::json(0, null, null, $redirect);
        }

                switch ($type) {
            case OauthType::WECHAT_MOBILE:
            case OauthType::WECHAT:
                if (!empty($oauthUserInfo['unionId'])) {
                                        $memberUserId = MemberUtil::getIdByOauth(OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                    if ($memberUserId) {
                        MemberUtil::putOauth($memberUserId, $type, $oauthOpenId);
                        ApiSessionUtil::put('memberUserId', $memberUserId);
                        ApiSessionUtil::forget('oauthOpenId');
                        ApiSessionUtil::forget('oauthUserInfo');
                        return Response::json(0, null);
                    }
                }
                $memberUserId = MemberUtil::getIdByOauth($type, $oauthOpenId);
                if ($memberUserId) {
                    ApiSessionUtil::put('memberUserId', $memberUserId);
                    ApiSessionUtil::forget('oauthOpenId');
                    ApiSessionUtil::forget('oauthUserInfo');
                    return Response::json(0, null);
                }
                break;
            case OauthType::QQ:
            case OauthType::WEIBO:
                $memberUserId = MemberUtil::getIdByOauth($type, $oauthOpenId);
                if ($memberUserId) {
                    ApiSessionUtil::put('memberUserId', $memberUserId);
                    ApiSessionUtil::forget('oauthOpenId');
                    ApiSessionUtil::forget('oauthUserInfo');
                    return Response::json(0, null);
                }
                break;
        }

        if (ConfigUtil::getWithEnv('registerDisable', false)) {
            return Response::json(-1, '用户注册已禁用');
        }
        $username = $input->getTrimString('username');
        $ret = MemberUtil::register($username, null, null, null, true);
        if ($ret['code']) {
            return Response::json(-1, $ret['msg']);
        }
        $memberUserId = $ret['data']['id'];
        Event::fire(new MemberUserRegisteredEvent($memberUserId));
        switch ($type) {
            case OauthType::WECHAT_MOBILE:
            case OauthType::WECHAT:
                if (!empty($oauthUserInfo['unionId'])) {
                    MemberUtil::putOauth($memberUserId, OauthType::WECHAT_UNION, $oauthUserInfo['unionId']);
                }
                MemberUtil::putOauth($memberUserId, $type, $oauthOpenId);
                break;
            case OauthType::QQ:
            case OauthType::WEIBO:
                MemberUtil::putOauth($memberUserId, $type, $oauthOpenId);
                break;
            default:
                return Response::json(-1, 'oauthType error');
        }

        if (!empty($oauthUserInfo['avatar'])) {
            $avatarExt = FileUtil::extension($oauthUserInfo['avatar']);
            $avatar = CurlUtil::getRaw($oauthUserInfo['avatar']);
            if (!empty($avatar)) {
                if (empty($avatarExt)) {
                    $avatarExt = 'jpg';
                }
                MemberUtil::setAvatar($memberUserId, $avatar, $avatarExt);
            }
        }
        ApiSessionUtil::put('memberUserId', $memberUserId);
        ApiSessionUtil::forget('oauthOpenId');
        ApiSessionUtil::forget('oauthUserInfo');
        return Response::json(0, null);
    }

    public function oauthCallback($oauthType = null, $callback = null)
    {
        $input = InputPackage::buildFromInput();
        $type = $input->getType('type', OauthType::class);
        if (empty($type)) {
            $type = $oauthType;
        }
        $code = $input->getTrimString('code');
        if (empty($code)) {
            return Response::json(-1, '登录失败(code为空)', null, '/');
        }
        $config = $this->getOauthConfig($type, $callback);
        if (empty($config)) {
            return Response::json(-1, '授权登录配置错误', null, '/');
        }
        $oauth = Oauth::getInstance($type, $config);
        $token = null;
        $openid = null;
        try {
            $token = $oauth->getAccessToken($code, null);
            $openid = $oauth->openid();
        } catch (\Exception $e) {
            return Response::json(-1, '登录失败(' . $e->getMessage() . ')', null, '/');
        }

        if (empty($token) || empty($openid)) {
            return Response::json(-1, '登录失败(token=' . print_r($token, true) . ',openid=' . $openid . ')', null, '/');
        }
        $userInfo = [];
        switch ($type) {
            case OauthType::WECHAT_MOBILE:
            case OauthType::WECHAT:
                $data = $oauth->call('sns/userinfo');
                if (!empty($data ['errcode'])) {
                    return Response::json(-1, "微信登录失败：" . $data['errmsg'], null, '/');
                }
                $userInfo['username'] = $data['nickname'];
                $userInfo['avatar'] = $data['headimgurl'];
                $userInfo['unionId'] = empty($data['unionid']) ? null : $data['unionid'];
                break;
            case OauthType::QQ:
                $data = $oauth->call('user/get_user_info');
                if (!isset($data['ret']) || $data['ret'] != 0) {
                    return Response::json(-1, 'QQ登录失败:' . json_encode($data), null, '/');
                }
                $userInfo['username'] = $data['nickname'];
                foreach (['figureurl_qq_2', 'figureurl_2', 'figureurl_qq_1', 'figureurl_1', 'figureurl'] as $avatarField) {
                    if (isset($data[$avatarField]) && $data[$avatarField]) {
                        $userInfo['avatar'] = $data[$avatarField];
                        break;
                    }
                }
                break;
            case OauthType::WEIBO:
                $data = $oauth->call('users/show', "uid=" . $openid);
                if (!isset($data ['screen_name']) || empty($data ['screen_name'])) {
                    return Response::json(-1, '微博登录失败:' . json_encode($data), null, '/');
                }
                $userInfo['username'] = $data['screen_name'];
                $userInfo['avatar'] = empty($data['profile_image_url']) ? null : $data['profile_image_url'];
                break;
        }
        if (empty($userInfo)) {
            return Response::json(-1, '获取用户信息失败');
        }
        ApiSessionUtil::put('oauthOpenId', $openid);
        ApiSessionUtil::put('oauthUserInfo', $userInfo);
        return Response::json(0, 'ok', [
            'optnid' => $openid,
            'user' => $userInfo,
        ]);
    }

    public function oauthLogin($oauthType = null, $callback = null)
    {
        $input = InputPackage::buildFromInput();
        if (empty($oauthType)) {
            $oauthType = $input->getType('type', OauthType::class);
        }
        $config = $this->getOauthConfig($oauthType, $callback);
        if (empty($config)) {
            return Response::json(-1, '授权登录配置错误');
        }

        if (empty($config['APP_KEY']) || empty($config['APP_SECRET'])) {
            return Response::json(-1, 'APP_KEY和APP_SECRET不能为空');
        }

        $oauthWechatProxy = ConfigUtil::getWithEnv('oauthWechatMobileProxy');
        if ($oauthWechatProxy && in_array($oauthType, [OauthType::WECHAT_MOBILE])) {
            $url = $oauthWechatProxy
                . '?appid=' . $config['APP_KEY'] . '&scope=snsapi_userinfo&redirect_uri='
                . urlencode($config['CALLBACK']);
        } else {
            $sns = Oauth::getInstance($oauthType, $config);
            $url = $sns->getRequestCodeURL();
        }
        return Response::json(0, 'ok', [
            'redirect' => $url,
        ]);
    }


    public function ssoClientLogoutPrepare()
    {
        if (!ConfigUtil::get('ssoClientEnable', false)) {
            return Response::json(-1, '请开启 同步登录客户端');
        }
        $input = InputPackage::buildFromInput();
        $domainUrl = $input->getTrimString('domainUrl');

        $ssoClientServer = ConfigUtil::get('ssoClientServer', '');
        if (empty($ssoClientServer)) {
            return Response::json(-1, '请配置 同步登录服务端地址');
        }

        $redirect = $ssoClientServer . '_logout' . '?' . http_build_query(['redirect' => $domainUrl . '/sso/client_logout',]);
        return Response::json(0, 'ok', [
            'redirect' => $redirect,
        ]);
    }

    public function ssoClientLogout()
    {
        if (!ConfigUtil::get('ssoClientEnable', false)) {
            return Response::json(-1, '请开启 同步登录客户端');
        }
        ApiSessionUtil::forget('memberUserId');
        return Response::json(0, 'ok');
    }

    public function ssoServerLogout()
    {
        if (!ConfigUtil::get('ssoServerEnable', false)) {
            return Response::json(-1, '请开启 同步登录服务端');
        }
        ApiSessionUtil::forget('memberUserId');
        return Response::json(0, 'ok');
    }

    public function ssoServerSuccess()
    {
        if (!ConfigUtil::get('ssoServerEnable', false)) {
            return Response::json(-1, '请开启 同步登录服务端');
        }

        $memberUserId = ApiSessionUtil::get('memberUserId', 0);
        if (!$memberUserId) {
            return Response::json(-1, '未登录');
        }
        $memberUser = $this->memberUser();
        $ssoServerSecret = ConfigUtil::get('ssoServerSecret');
        if (empty($ssoServerSecret)) {
            return Response::json(-1, '请设置 同步登录服务端通讯秘钥');
        }

        $input = InputPackage::buildFromInput();
        $client = $input->getTrimString('client');
        $domainUrl = $input->getTrimString('domainUrl');
        if (empty($domainUrl) || empty($client)) {
            return Response::json(-1, '数据错误');
        }
        $ssoClientList = explode("\n", ConfigUtil::get('ssoServerClientList', ''));
        $valid = false;
        foreach ($ssoClientList as $item) {
            if (trim($item) == $client) {
                $valid = true;
            }
        }
        if (!$valid) {
            return Response::json(-1, '数据错误(2)');
        }
        $server = $domainUrl . '/sso/server';
        $timestamp = time();
        $username = $memberUser['username'];
        $sign = md5(md5($ssoServerSecret) . md5($timestamp . '') . md5($server) . md5($username));

        $redirect = $client
            . '?server=' . urlencode($server)
            . '&timestamp=' . $timestamp
            . '&username=' . urlencode(base64_encode($username))
            . '&sign=' . $sign;

        return Response::json(0, null, [
            'redirect' => $redirect
        ]);
    }

    public function ssoServer()
    {
        if (!ConfigUtil::get('ssoServerEnable', false)) {
            return Response::json(-1, '请开启 同步登录服务端');
        }
        $input = InputPackage::buildFromInput();
        $client = $input->getTrimString('client');
        $timestamp = $input->getInteger('timestamp');
        $sign = $input->getTrimString('sign');
        if (empty($client)) {
            return Response::json(-1, 'client 为空');
        }
        if (empty($timestamp)) {
            return Response::json(-1, 'timestamp 为空');
        }
        if (empty($sign)) {
            return Response::json(-1, 'sign 为空');
        }
        $ssoSecret = ConfigUtil::get('ssoServerSecret');
        if (empty($ssoSecret)) {
            return Response::json(-1, '请设置 同步登录服务端通讯秘钥');
        }
        $signCalc = md5(md5($ssoSecret) . md5($timestamp . '') . md5($client));
        if ($sign != $signCalc) {
            return Response::json(-1, 'sign 错误');
        }
        if (abs(time() - $timestamp) > 3600) {
            return Response::json(-1, 'timestamp 错误');
        }
        $ssoClientList = explode("\n", ConfigUtil::get('ssoServerClientList', ''));
        $valid = false;
        foreach ($ssoClientList as $item) {
            if (trim($item) == $client) {
                $valid = true;
            }
        }
        if (!$valid) {
            return Response::json(-1, '请在 同步登陆服务端增加客户端地址 ' . $client);
        }
        $isLogin = false;
        if (intval(ApiSessionUtil::get('memberUserId', 0)) > 0) {
            $isLogin = true;
        }
        return Response::json(0, 'ok', [
            'isLogin' => $isLogin,
        ]);
    }

    public function ssoClient()
    {
        if (!ConfigUtil::get('ssoClientEnable', false)) {
            return Response::json(-1, '请开启 同步登录客户端');
        }

        $ssoClientServer = ConfigUtil::get('ssoClientServer', '');
        if (empty($ssoClientServer)) {
            return Response::json(-1, '请配置 同步登录服务端地址');
        }

        $ssoClientSecret = ConfigUtil::get('ssoClientSecret');
        if (empty($ssoClientSecret)) {
            return Response::json(-1, '请设置 同步登录客户端通讯秘钥');
        }

        $input = InputPackage::buildFromInput();
        $server = $input->getTrimString('server');
        $timestamp = $input->getInteger('timestamp');
        $sign = $input->getTrimString('sign');
        $username = @base64_decode($input->getTrimString('username'));

        if (empty($username)) {
            return Response::json(-1, '同步登录返回的用户名为空');
        }
        if (empty($timestamp)) {
            return Response::json(-1, 'timestamp为空');
        }
        if (empty($sign)) {
            return Response::json(-1, 'sign为空');
        }
        $signCalc = md5(md5($ssoClientSecret) . md5($timestamp . '') . md5($server) . md5($username));
        if ($sign != $signCalc) {
            return Response::json(-1, 'sign错误');
        }
        if (abs(time() - $timestamp) > 3600) {
            return Response::json(-1, 'timestamp错误');
        }
        if ($server != $ssoClientServer) {
            return Response::json(-1, '同步登录 服务端地址不是配置的' . $ssoClientServer);
        }
        $memberUser = MemberUtil::getByUsername($username);
        if (empty($memberUser)) {
            $ret = MemberUtil::register($username, null, null, null, true);
            if ($ret['code']) {
                return Response::json(-1, $ret['msg']);
            }
            $memberUser = MemberUtil::get($ret['data']['id']);
        }
        if (method_exists($this, 'hookLoginPreCheck')) {
            $ret = $this->hookLoginPreCheck($memberUser);
            if ($ret['code']) {
                return Response::json(-1, $ret['msg']);
            }
        }
        ApiSessionUtil::put('memberUserId', $memberUser['id']);
        return Response::json(0, 'ok');
    }

    public function ssoClientPrepare()
    {
        if (!ConfigUtil::getBoolean('ssoClientEnable')) {
            return Response::json(-1, 'SSO未开启');
        }
        $ssoClientServer = ConfigUtil::get('ssoClientServer');
        $ssoClientSecret = ConfigUtil::get('ssoClientSecret');
        $input = InputPackage::buildFromInput();
        $client = $input->getTrimString('client', '/');
        if (!Str::endsWith($client, '/sso/client')) {
            return Response::json(-1, 'client参数错误');
        }
        $timestamp = time();
        $sign = md5(md5($ssoClientSecret) . md5($timestamp . '') . md5($client));
        $redirect = $ssoClientServer . '?client=' . urlencode($client) . '&timestamp=' . $timestamp . '&sign=' . $sign;
        return Response::json(0, 'ok', [
            'redirect' => $redirect,
        ]);
    }

    public function logout()
    {
        ApiSessionUtil::forget('memberUserId');
        return Response::json(0, 'ok');
    }

    public function login()
    {
        $input = InputPackage::buildFromInput();

        $username = $input->getTrimString('username');
        $password = $input->getTrimString('password');
        if (empty($username)) {
            return Response::json(-1, '请输入用户');
        }
        if (empty($password)) {
            return Response::json(-1, '请输入密码');
        }

        if (ConfigUtil::get('loginCaptchaEnable', false)) {
            $captcha = $input->getTrimString('captcha');
            if (!ApiSessionCaptchaUtil::check('loginCaptcha', $captcha)) {
                return Response::json(ResponseCodes::CAPTCHA_ERROR, '验证码错误');
            }
        }
        ApiSessionUtil::forget('loginCaptcha');

        $memberUser = null;
        if (!$memberUser) {
            $ret = MemberUtil::login($username, null, null, $password);
            if (0 == $ret['code']) {
                $memberUser = $ret['data'];
            }
        }
        if (!$memberUser) {
            $ret = MemberUtil::login(null, $username, null, $password);
            if (0 == $ret['code']) {
                $memberUser = $ret['data'];
            }
        }
        if (!$memberUser) {
            $ret = MemberUtil::login(null, null, $username, $password);
            if (0 == $ret['code']) {
                $memberUser = $ret['data'];
            }
        }
        if (!$memberUser) {
            return Response::json(ResponseCodes::CAPTCHA_ERROR, '登录失败');
        }
        if (method_exists($this, 'hookLoginPreCheck')) {
            $ret = $this->hookLoginPreCheck($memberUser);
            if ($ret['code']) {
                return Response::json(-1, $ret['msg']);
            }
        }
        ApiSessionUtil::put('memberUserId', $memberUser['id']);
        Event::fire(new MemberUserLoginedEvent($memberUser['id']));
        return Response::json(0, 'ok');
    }

    public function loginCaptcha()
    {
        return Response::json(0, 'ok', [
            'image' => ApiSessionCaptchaUtil::create('loginCaptcha'),
        ]);
    }

    public function register()
    {
        if (ConfigUtil::get('registerDisable', false)) {
            return Response::json(-1, '禁止注册');
        }

        $input = InputPackage::buildFromInput();

        $username = $input->getTrimString('username');
        $password = $input->getTrimString('password');
        $phone = $input->getPhone('phone');
        $phoneVerify = $input->getTrimString('phoneVerify');
        $email = $input->getEmail('email');
        $emailVerify = $input->getTrimString('emailVerify');
        $password = $input->getTrimString('password');
        $passwordRepeat = $input->getTrimString('passwordRepeat');
        $captcha = $input->getTrimString('captcha');

        if (empty($username)) {
            return Response::json(-1, '用户名不能为空');
        }
                if (Str::contains($username, '@')) {
            return Response::json(-1, '用户名不能包含特殊字符');
        }
        if (preg_match('/^\\d{11}$/', $username)) {
            return Response::json(-1, '用户名不能为纯数字');
        }

        if (!ApiSessionUtil::get('registerCaptchaPass', false)) {
            if (!ApiSessionCaptchaUtil::check('registerCaptcha', $captcha)) {
                ApiSessionUtil::atomicProduce('registerCaptchaPassCount', 1);
                return Response::json(-1, '图片验证失败');
            }
        }
        if (!ApiSessionUtil::atomicConsume('registerCaptchaPassCount')) {
            return Response::json(-1, '请重新输入图片验证码');
        }

        if (ConfigUtil::get('registerPhoneEnable')) {
            if (empty($phone)) {
                return Response::json(-1, '请输入手机');
            }
            if ($phoneVerify != ApiSessionUtil::get('registerPhoneVerify')) {
                return Response::json(-1, '手机验证码不正确.');
            }
            if (ApiSessionUtil::get('registerPhoneVerifyTime') + 60 * 60 < time()) {
                return Response::json(-1, '手机验证码已过期');
            }
            if ($phone != ApiSessionUtil::get('registerPhone')) {
                return Response::json(-1, '两次手机不一致');
            }
        }
        if (ConfigUtil::get('registerEmailEnable')) {
            if (empty($email)) {
                return Response::json(-1, '请输入邮箱');
            }
            if ($emailVerify != ApiSessionUtil::get('registerEmailVerify')) {
                return Response::json(-1, '邮箱验证码不正确.');
            }
            if (ApiSessionUtil::get('registerEmailVerifyTime') + 60 * 60 < time()) {
                return Response::json(-1, '邮箱验证码已过期');
            }
            if ($email != ApiSessionUtil::get('registerEmail')) {
                return Response::json(-1, '两次邮箱不一致');
            }
        }
        if (empty($password)) {
            return Response::json(-1, '请输入密码');
        }
        if ($password != $passwordRepeat) {
            return Response::json(-1, '两次输入密码不一致');
        }

        $ret = MemberUtil::register($username, $phone, $email, $password);
        if ($ret['code']) {
            return Response::json(-1, $ret['msg']);
        }
        $memberUserId = $ret['data']['id'];
        $update = [];
        if (ConfigUtil::get('registerPhoneEnable')) {
            $update['phoneVerified'] = true;
        }
        if (ConfigUtil::get('registerEmailEnable')) {
            $update['emailVerified'] = true;
        }
        if (!empty($update)) {
            MemberUtil::update($memberUserId, $update);
        }
        Event::fire(new MemberUserRegisteredEvent($memberUserId));
        ApiSessionUtil::forget('registerCaptchaPass');
        return Response::json(0, '注册成功');
    }

    public function registerEmailVerify()
    {
        if (ConfigUtil::get('registerDisable', false)) {
            return Response::json(-1, '禁止注册');
        }
        if (!ConfigUtil::get('registerEmailEnable')) {
            return Response::json(-1, '注册未开启邮箱');
        }
        $input = InputPackage::buildFromInput();

        $email = $input->getEmail('target');
        if (empty($email)) {
            return Response::json(-1, '邮箱不能为空');
        }

        if (!ApiSessionUtil::get('registerCaptchaPass', false)) {
            return Response::json(-1, '请先验证图片验证码');
        }
        if (!ApiSessionUtil::atomicConsume('registerCaptchaPassCount')) {
            return Response::json(-1, '请重新输入图片验证码');
        }

        $memberUser = MemberUtil::getByEmail($email);
        if (!empty($memberUser)) {
            return Response::json(-1, '邮箱已经被占用');
        }

        if (ApiSessionUtil::get('registerEmailVerifyTime') && $email == ApiSessionUtil::get('registerEmail')) {
            if (ApiSessionUtil::get('registerEmailVerifyTime') + 60 * 10 > time()) {
                return Response::json(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        ApiSessionUtil::put('registerEmailVerify', $verify);
        ApiSessionUtil::put('registerEmailVerifyTime', time());
        ApiSessionUtil::put('registerEmail', $email);

        MailUtil::send($email, '注册账户验证码', MailTemplate::VERIFY, ['code' => $verify]);

        return Response::json(0, '验证码发送成功');
    }

    public function registerPhoneVerify()
    {
        if (ConfigUtil::get('registerDisable', false)) {
            return Response::json(-1, '禁止注册');
        }
        if (!ConfigUtil::get('registerPhoneEnable')) {
            return Response::json(-1, '注册未开启手机');
        }
        $input = InputPackage::buildFromInput();

        $phone = $input->getPhone('target');
        if (empty($phone)) {
            return Response::json(-1, '手机不能为空');
        }

        if (!ApiSessionUtil::get('registerCaptchaPass', false)) {
            return Response::json(-1, '请先验证图片验证码');
        }
        if (!ApiSessionUtil::atomicConsume('registerCaptchaPassCount')) {
            return Response::json(-1, '请重新输入图片验证码');
        }

        $memberUser = MemberUtil::getByPhone($phone);
        if (!empty($memberUser)) {
            return Response::json(-1, '手机已经被占用');
        }

        if (ApiSessionUtil::get('registerPhoneVerifyTime') && $phone == ApiSessionUtil::get('registerPhone')) {
            if (ApiSessionUtil::get('registerPhoneVerifyTime') + 60 * 10 > time()) {
                return Response::json(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        ApiSessionUtil::put('registerPhoneVerify', $verify);
        ApiSessionUtil::put('registerPhoneVerifyTime', time());
        ApiSessionUtil::put('registerPhone', $phone);

        $ret = SmsUtil::send($phone, SmsTemplate::VERIFY, ['code' => $verify]);

        return Response::json(0, '验证码发送成功');
    }

    public function registerCaptchaVerify()
    {
        $input = InputPackage::buildFromInput();
        $captcha = $input->getTrimString('captcha');
        if (!ApiSessionCaptchaUtil::check('registerCaptcha', $captcha)) {
            ApiSessionUtil::atomicRemove('registerCaptchaPassCount');
            return Response::json(ResponseCodes::CAPTCHA_ERROR, '验证码错误');
        }
        ApiSessionUtil::put('registerCaptchaPass', true);
        $registerCaptchaPassCount = 1;
        if (ConfigUtil::get('registerEmailEnable')) {
            $registerCaptchaPassCount++;
        }
        if (ConfigUtil::get('registerPhoneEnable')) {
            $registerCaptchaPassCount++;
        }
        ApiSessionUtil::atomicProduce('registerCaptchaPassCount', $registerCaptchaPassCount);
        return Response::json(0, 'ok');
    }

    public function registerCaptcha()
    {
        ApiSessionUtil::forget('registerCaptchaPass');
        return Response::json(0, 'ok', [
            'image' => ApiSessionCaptchaUtil::create('registerCaptcha')
        ]);
    }

    public function retrievePhone()
    {
        if (ConfigUtil::get('retrieveDisable', false)) {
            return Response::json(-1, '找回密码已禁用');
        }

        $input = InputPackage::buildFromInput();

        if (!ConfigUtil::get('retrievePhoneEnable', false)) {
            return Response::json(-1, '找回密码没有开启');
        }

        $phone = $input->getPhone('phone');
        $verify = $input->getTrimString('verify');

        if (empty($phone)) {
            return Response::json(-1, '手机为空或不正确');
        }
        if (empty($verify)) {
            return Response::json(-1, '验证码不能为空');
        }
        if ($verify != ApiSessionUtil::get('retrievePhoneVerify')) {
            return Response::json(-1, '手机验证码不正确');
        }
        if (ApiSessionUtil::get('retrievePhoneVerifyTime') + 60 * 60 < time()) {
            return Response::json(0, '手机验证码已过期');
        }
        if ($phone != ApiSessionUtil::get('retrievePhone')) {
            return Response::json(-1, '两次手机不一致');
        }

        $memberUser = MemberUtil::getByPhone($phone);
        if (empty($memberUser)) {
            return Response::json(-1, '手机没有绑定任何账号');
        }

        ApiSessionUtil::forget('retrievePhoneVerify');
        ApiSessionUtil::forget('retrievePhoneVerifyTime');
        ApiSessionUtil::forget('retrievePhone');

        ApiSessionUtil::put('retrieveMemberUserId', $memberUser['id']);

        return Response::json(0, null);
    }

    public function retrievePhoneVerify()
    {
        if (ConfigUtil::get('retrieveDisable', false)) {
            return Response::json(-1, '找回密码已禁用');
        }

        $input = InputPackage::buildFromInput();
        $phone = $input->getPhone('target');
        if (empty($phone)) {
            return Response::json(-1, '手机为空或格式不正确');
        }

        $captcha = $input->getTrimString('captcha');
        if (!ApiSessionCaptchaUtil::check('retrieveCaptcha', $captcha)) {
            return Response::json(-1, '图片验证码错误');
        }

        $memberUser = MemberUtil::getByPhone($phone);
        if (empty($memberUser)) {
            return Response::json(-1, '手机没有绑定任何账号');
        }

        if (ApiSessionUtil::get('retrievePhoneVerifyTime') && $phone == ApiSessionUtil::get('retrievePhone')) {
            if (ApiSessionUtil::get('retrievePhoneVerifyTime') + 60 * 2 > time()) {
                return Response::json(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        ApiSessionUtil::put('retrievePhoneVerify', $verify);
        ApiSessionUtil::put('retrievePhoneVerifyTime', time());
        ApiSessionUtil::put('retrievePhone', $phone);

        SmsUtil::send($phone, SmsTemplate::VERIFY, ['code' => $verify]);

        return Response::json(0, '验证码发送成功');
    }

    public function retrieveEmail()
    {
        if (ConfigUtil::get('retrieveDisable', false)) {
            return Response::json(-1, '找回密码已禁用');
        }

        if (!ConfigUtil::get('retrieveEmailEnable', false)) {
            return Response::json(-1, '找回密码没有开启');
        }

        $input = InputPackage::buildFromInput();

        $email = $input->getEmail('email');
        $verify = $input->getTrimString('verify');

        if (empty($email)) {
            return Response::json(-1, '邮箱为空或格式不正确');
        }
        if (empty($verify)) {
            return Response::json(-1, '验证码不能为空');
        }
        if ($verify != ApiSessionUtil::get('retrieveEmailVerify')) {
            return Response::json(-1, '邮箱验证码不正确');
        }
        if (ApiSessionUtil::get('retrieveEmailVerifyTime') + 60 * 60 < time()) {
            return Response::json(0, '邮箱验证码已过期');
        }
        if ($email != ApiSessionUtil::get('retrieveEmail')) {
            return Response::json(-1, '两次邮箱不一致');
        }

        $memberUser = MemberUtil::getByEmail($email);
        if (empty($memberUser)) {
            return Response::json(-1, '邮箱没有绑定任何账号');
        }

        ApiSessionUtil::forget('retrieveEmailVerify');
        ApiSessionUtil::forget('retrieveEmailVerifyTime');
        ApiSessionUtil::forget('retrieveEmail');

        ApiSessionUtil::put('retrieveMemberUserId', $memberUser['id']);

        return Response::json(0, null);
    }

    public function retrieveEmailVerify()
    {
        if (ConfigUtil::get('retrieveDisable', false)) {
            return Response::json(-1, '找回密码已禁用');
        }

        $input = InputPackage::buildFromInput();

        $email = $input->getEmail('target');
        if (empty($email)) {
            return Response::json(-1, '邮箱格式不正确或为空');
        }

        $captcha = $input->getTrimString('captcha');
        if (!ApiSessionCaptchaUtil::check('retrieveCaptcha', $captcha)) {
            return Response::json(-1, '图片验证码错误');
        }

        $memberUser = MemberUtil::getByEmail($email);
        if (empty($memberUser)) {
            return Response::json(-1, '邮箱没有绑定任何账号');
        }

        if (ApiSessionUtil::get('retrieveEmailVerifyTime') && $email == ApiSessionUtil::get('retrieveEmail')) {
            if (ApiSessionUtil::get('retrieveEmailVerifyTime') + 60 * 10 > time()) {
                return Response::json(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        ApiSessionUtil::put('retrieveEmailVerify', $verify);
        ApiSessionUtil::put('retrieveEmailVerifyTime', time());
        ApiSessionUtil::put('retrieveEmail', $email);

        MailUtil::send($email, '找回密码验证码', MailTemplate::VERIFY, ['code' => $verify]);

        return Response::json(0, '验证码发送成功');
    }

    public function retrieveResetInfo()
    {
        $retrieveMemberUserId = ApiSessionUtil::get('retrieveMemberUserId');
        if (empty($retrieveMemberUserId)) {
            return Response::json(-1, '请求错误');
        }
        $memberUser = MemberUtil::get($retrieveMemberUserId);
        $username = $memberUser['username'];
        if (empty($username)) {
            $username = $memberUser['phone'];
        }
        if (empty($username)) {
            $username = $memberUser['email'];
        }
        return Response::json(0, null, [
            'memberUser' => [
                'username' => $username,
            ]
        ]);
    }

    public function retrieveReset()
    {
        if (ConfigUtil::get('retrieveDisable', false)) {
            return Response::json(-1, '找回密码已禁用');
        }

        $input = InputPackage::buildFromInput();
        $retrieveMemberUserId = ApiSessionUtil::get('retrieveMemberUserId');
        if (empty($retrieveMemberUserId)) {
            return Response::json(-1, '请求错误');
        }
        $password = $input->getTrimString('password');
        $passwordRepeat = $input->getTrimString('passwordRepeat');
        if (empty($password)) {
            return Response::json(-1, '请输入密码');
        }
        if ($password != $passwordRepeat) {
            return Response::json(-1, '两次输入密码不一致');
        }
        $memberUser = MemberUtil::get($retrieveMemberUserId);
        if (empty($memberUser)) {
            return Response::json(-1, '用户不存在');
        }
        $ret = MemberUtil::changePassword($memberUser['id'], $password, null, true);
        if ($ret['code']) {
            return Response::json(-1, $ret['msg']);
        }
        Event::fire(new MemberUserPasswordResetedEvent($memberUser['id'], $password));
        ApiSessionUtil::forget('retrieveMemberUserId');
        return Response::json(0, '成功设置新密码,请您登录');
    }

    public function retrieveCaptcha()
    {
        return Response::json(0, 'ok', [
            'image' => ApiSessionCaptchaUtil::create('retrieveCaptcha'),
        ]);
    }
}
