<?php

namespace TechSoft\Laravel\Member\Traits;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use TechOnline\Laravel\Exception\TodoException;
use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Api\ApiSessionUtil;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Member\MemberUtil;
use TechSoft\Laravel\Oauth\OauthType;
use TechSoft\Laravel\Sms\SmsTemplate;
use TechSoft\Laravel\Sms\SmsUtil;





trait MemberAccountWebControllerTrait
{
    use MemberAccountApiControllerTrait;

    public function loginView()
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        if (ConfigUtil::get('ssoClientEnable', false)) {
            Input::merge(['client' => Request::domainUrl() . '/sso/client']);
            $ret = $this->ssoClientPrepare()->getData(true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            Session::put('ssoClientRedirect', $redirect);
            return Response::send(0, null, null, $ret['data']['redirect']);
        }
        if (Request::isPost()) {
            $ret = $this->login()->getData(true);
            if ($ret['code']) {
                if ($input->getTrimString('captcha')) {
                    return Response::send(-1, $ret['msg'], null, '[js]$("[data-captcha]").click()');
                }
                return Response::send(-1, $ret['msg']);
            }
            return Response::send(0, '', '', $redirect);
        }
        return $this->_view('member.login', [
            'redirect' => $redirect,
        ]);
    }

    public function logoutView()
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        if (ConfigUtil::get('ssoClientEnable', false)) {
            Input::merge(['domainUrl' => Request::domainUrl()]);
            $ret = $this->ssoClientLogoutPrepare()->getData(true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            Session::put('ssoLogoutRedirect', $redirect);
            return Response::send(0, null, null, $ret['data']['redirect']);
        }
        $ret = $this->logout()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        return Response::send(0, '', '', $redirect);
    }

    public function loginCaptchaView()
    {
        $ret = $this->loginCaptcha()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        $image = base64_decode(substr($ret['data']['image'], strlen('data:image/png;base64,')));
        return Response::raw($image, [
            'Content-Type' => 'image/png'
        ]);
    }

    public function registerView()
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        if (Request::isPost()) {
            $ret = $this->register()->getData(true);
            if ($ret['code']) {
                if ($input->getTrimString('captcha')) {
                    return Response::send(-1, $ret['msg'], null, '[js]$("[data-captcha]").click()');
                }
                return Response::send(-1, $ret['msg']);
            }
            return Response::send(0, '', '', $redirect);
        }
        return $this->_view('member.register', [
            'redirect' => $redirect,
        ]);
    }

    public function registerEmailVerifyView()
    {
        $ret = $this->registerEmailVerify()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return Response::send(0, $ret['msg']);
    }

    public function registerPhoneVerifyView()
    {
        $ret = $this->registerPhoneVerify()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return Response::send(0, $ret['msg']);
    }

    public function registerCaptchaView()
    {
        $ret = $this->registerCaptcha()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        $image = base64_decode(substr($ret['data']['image'], strlen('data:image/png;base64,')));
        return Response::raw($image, [
            'Content-Type' => 'image/png'
        ]);
    }

    public function registerCaptchaVerifyView()
    {
        $ret = $this->registerCaptchaVerify()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg'], null, '[js]$("[data-captcha]").click()');
        }
        return Response::send(0, $ret['msg']);
    }

    public function retrieveView()
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        return $this->_view('member.retrieve', [
            'redirect' => $redirect,
        ]);
    }

    public function retrievePhoneView()
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        if (Request::isPost()) {
            $ret = $this->retrievePhone()->getData(true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            return Response::send(0, $ret['msg'], null, '/retrieve/reset');
        }
        return $this->_view('member.retrievePhone', [
            'redirect' => $redirect,
        ]);
    }

    public function retrievePhoneVerifyView()
    {
        $ret = $this->retrievePhoneVerify()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return Response::send(0, $ret['msg']);
    }

    public function retrieveEmailView()
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        if (Request::isPost()) {
            $ret = $this->retrieveEmail()->getData(true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            return Response::send(0, $ret['msg'], null, '/retrieve/reset');
        }
        return $this->_view('member.retrieveEmail', [
            'redirect' => $redirect,
        ]);
    }

    public function retrieveEmailVerifyView()
    {
        $ret = $this->retrieveEmailVerify()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return Response::send(0, $ret['msg']);
    }

    public function retrieveCaptchaView()
    {
        $ret = $this->retrieveCaptcha()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        $image = base64_decode(substr($ret['data']['image'], strlen('data:image/png;base64,')));
        return Response::raw($image, [
            'Content-Type' => 'image/png'
        ]);
    }

    public function retrieveResetView()
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        if (Request::isPost()) {
            $ret = $this->retrieveReset()->getData(true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            return Response::send(0, $ret['msg'], null, '/');
        }
        $ret = $this->retrieveResetInfo()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return $this->_view('member.retrieveReset', [
            'redirect' => $redirect,
            'memberUser' => $ret['data']['memberUser'],
        ]);
    }

    public function oauthLoginView($oauthType = null)
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', '/');
        $callback = Request::domainUrl() . '/oauth_callback_' . $oauthType;
        $ret = $this->oauthLogin($oauthType, $callback)->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        ApiSessionUtil::put('oauthRedirect', $redirect);
        return Response::send(0, null, null, $ret['data']['redirect']);
    }

    public function oauthCallbackView($oauthType = null)
    {
        $callback = Request::domainUrl() . '/oauth_callback_' . $oauthType;
        $ret = $this->oauthCallback($oauthType, $callback)->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        $bind = Request::domainUrl() . '/oauth_bind_' . $oauthType;
        return Response::send(0, null, null, $bind);
    }

    public function oauthBindView($oauthType = null)
    {
        $input = InputPackage::buildFromInput();
        $redirect = ApiSessionUtil::get('oauthRedirect', '/');
        if (Request::isPost()) {
            $ret = $this->oauthBind($oauthType)->getData(true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            ApiSessionUtil::forget('oauthRedirect');
            return Response::send(0, $ret['msg'], null, $redirect);
        }
        $oauthOpenId = ApiSessionUtil::get('oauthOpenId');
        if (empty($oauthOpenId)) {
            return Response::send(-1, '用户授权数据为空');
        }
        $oauthUserInfo = ApiSessionUtil::get('oauthUserInfo', []);
        $ret = $this->oauthTryLogin($oauthType)->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        if ($ret['data']['memberUserId'] > 0) {
            $redirect = ApiSessionUtil::get('oauthRedirect', '/');
            ApiSessionUtil::forget('oauthRedirect');
            return Response::send(0, null, null, $redirect);
        }
        return $this->_view('member.oauthBind', [
            'oauthOpenId' => $oauthOpenId,
            'oauthUserInfo' => $oauthUserInfo,
            'redirect' => $redirect,
        ]);
    }

    public function ssoClientView()
    {
        $ret = $this->ssoClient()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        $redirect = Session::get('ssoClientRedirect', '/');
        return Response::send(0, null, null, $redirect);
    }

    public function ssoServerView()
    {
        $input = InputPackage::buildFromInput();
        $ret = $this->ssoServer()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        $serverSuccessUrl = '/sso/server_success?' . http_build_query([
                'client' => $input->getTrimString('client'),
                'domainUrl' => Request::domainUrl(),
            ]);
        if ($ret['data']['isLogin']) {
            return Response::send(0, null, null, $serverSuccessUrl);
        }
        return Response::send(0, null, null, '/login?' . http_build_query(['redirect' => $serverSuccessUrl]));
    }

    public function ssoServerSuccessView()
    {
        $ret = $this->ssoServerSuccess()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return Response::send(0, null, null, $ret['data']['redirect']);
    }

    public function ssoServerLogoutView()
    {
        $input = InputPackage::buildFromInput();
        $ret = $this->ssoServerLogout()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        $redirect = $input->getTrimString('redirect', '/');
        return Response::send(0, null, null, $redirect);
    }

    public function ssoClientLogoutView()
    {
        $ret = $this->ssoClientLogout()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        $redirect = Session::get('ssoLogoutRedirect', '/');
        return Response::send(0, null, null, $redirect);
    }

}