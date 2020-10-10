<?php

namespace TechSoft\Laravel\Admin\Controller;

use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use TechOnline\Utils\StrUtil;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Mews\Captcha\Facades\Captcha;
use TechSoft\Laravel\Admin\Support\AdminAwareController;
use TechSoft\Laravel\Admin\Util\AdminUtil;
use TechSoft\Laravel\Config\ConfigUtil;

class LoginController extends AdminAwareController
{

    public function logout()
    {
        Session::flush();
        if (ConfigUtil::get('adminSSOClientEnable', false)) {
            $input = InputPackage::buildFromInput();
            if ($input->getTrimString('server', '') != 'true') {
                $ssoServer = ConfigUtil::get('adminSSOServer', '');
                if (empty($ssoServer)) {
                    return Response::send(-1, '请配置 同步登录服务端地址');
                }
                $clientRedirect = $input->getTrimString('redirect', '/');
                $clientLogout = Request::domainUrl() . '/logout?server=true&redirect=' . urlencode($clientRedirect);
                $ssoServerLogout = $ssoServer . '_logout?redirect=' . urlencode($clientLogout);
                return Response::send(0, null, null, $ssoServerLogout);
            }
        }
        return Response::send(0, null, null, env('ADMIN_PATH', '/'));
    }

    public function index()
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', env('ADMIN_PATH', '/admin/'));

        if ($this->adminUserId()) {
            return Response::send(0, '您已经登录', null, $redirect);
        }

        if (ConfigUtil::get('adminSSOClientEnable', false)) {
            return Response::send(0, null, null, env('ADMIN_PATH', '/admin/') . 'sso/client?redirect=' . urlencode($redirect));
        }

        if (Request::isPost()) {

            $input = InputPackage::buildFromInput();

            $username = $input->getTrimString('username');
            $password = $input->getTrimString('password');

            if (empty($username)) {
                return Response::send(-1, '用户名为空');
            }
            if (empty($password)) {
                return Response::send(-2, '密码为空');
            }

            if (config('admin.login.captcha')) {
                if (!Captcha::check($input->getTrimString('captcha'))) {
                    return Response::send(-1, '图片验证码错误', null, '[js]$(\'[data-captcha]\').click();');
                }
            }

            $ret = AdminUtil::login($username, $password);
            if ($ret['code']) {
                AdminUtil::addErrorLog(0, '登录错误', [
                    'IP' => Request::ip(),
                    '用户名' => $username,
                    '密码' => '******',
                ]);
                return Response::send(-1, '用户或密码错误:' . $ret['code'], null, '[js]$(\'[data-captcha]\').click();');
            }

            $user = $ret['data'];
            Session::put('_adminUserId', $user['id']);

            AdminUtil::addInfoLog($user['id'], '登录成功', [
                'IP' => Request::ip(),
            ]);

            $redirect = $input->getTrimString('redirect', env('ADMIN_PATH', '/admin/'));
            return Response::send(0, null, null, $redirect);
        }
        if (view()->exists('admin.login')) {
            return view('admin.login');
        }
        return view('admin::login');
    }

    public function captcha()
    {
        return Captcha::create('default');
    }

    public function ssoClient()
    {
        if (!ConfigUtil::get('adminSSOClientEnable', false)) {
            return Response::send(-1, '请开启 同步登录客户端');
        }

        $ssoServer = ConfigUtil::get('adminSSOServer', '');
        if (empty($ssoServer)) {
            return Response::send(-1, '请配置 同步登录服务端地址');
        }

        $ssoSecret = ConfigUtil::get('adminSSOClientSecret');
        if (empty($ssoSecret)) {
            return Response::send(-1, '请设置 同步登录客户端通讯秘钥');
        }

        $input = InputPackage::buildFromInput();
        $server = $input->getTrimString('server');
        if ($server) {

            $username = @base64_decode($input->getTrimString('username'));
            $timestamp = $input->getTrimString('timestamp');
            $sign = $input->getTrimString('sign');

            if (empty($username)) {
                return Response::send(-1, '同步登录返回的用户名为空');
            }
            if (empty($timestamp)) {
                return Response::send(-1, 'timestamp empty');
            }
            if (empty($sign)) {
                return Response::send(-1, 'sign empty');
            }

            $signCalc = md5(md5($ssoSecret) . md5($timestamp . '') . md5($server) . md5($username));
            if ($sign != $signCalc) {
                return Response::send(-1, 'sign error');
            }

            if (abs(time() - $timestamp) > 2400 * 2600) {
                return Response::send(-1, 'timestamp error');
            }

            if ($server != $ssoServer) {
                return Response::send(-1, '同步登录 服务端地址不是配置的' . $ssoServer);
            }

            $adminUser = AdminUtil::getByUsername($username);
            if (empty($adminUser)) {
                $adminUser = AdminUtil::add($username, null, true);
                $adminUser = AdminUtil::get($adminUser['id']);
            }

            Session::put('_adminUserId', $adminUser['id']);

            $ssoRedirect = Session::get('adminSSORedirect', null);
            if (empty($ssoRedirect)) {
                return Response::send(0, '已经登录成功 但是没有找到跳转地址');
            }
            return Response::send(0, null, null, $ssoRedirect);

        } else {

            $redirect = trim($input->getTrimString('redirect'));
            Session::put('adminSSORedirect', $redirect);

            $client = Request::domainUrl() . env('ADMIN_PATH', '/admin/') . 'sso/client';
            $timestamp = time();
            $sign = md5(md5($ssoSecret) . md5($timestamp . '') . md5($client));

            $redirect = $ssoServer . '?client=' . urlencode($client) . '&timestamp=' . $timestamp . '&sign=' . $sign;

            return Response::send(0, null, null, $redirect);

        }

    }

    public function ssoServer()
    {
        $input = InputPackage::buildFromInput();
        $client = trim($input->getTrimString('client'));
        $timestamp = intval($input->getTrimString('timestamp'));
        $sign = trim($input->getTrimString('sign'));
        if (empty($client)) {
            return Response::send(-1, 'client empty');
        }
        if (empty($timestamp)) {
            return Response::send(-1, 'timestamp empty');
        }
        if (empty($sign)) {
            return Response::send(-1, 'sign empty');
        }
        if (!ConfigUtil::get('adminSSOServerEnable', false)) {
            return Response::send(-1, '请开启 同步登录服务端');
        }
        $ssoSecret = ConfigUtil::get('adminSSOServerSecret');
        if (empty($ssoSecret)) {
            return Response::send(-1, '请设置 同步登录服务端通讯秘钥');
        }
        $signCalc = md5(md5($ssoSecret) . md5($timestamp . '') . md5($client));
        if ($sign != $signCalc) {
            return Response::send(-1, 'sign error');
        }
        if (abs(time() - $timestamp) > 2400 * 2600) {
            return Response::send(-1, 'timestamp error');
        }
        $ssoClientList = explode("\n", ConfigUtil::get('adminSSOClientList', ''));
        $valid = false;
        foreach ($ssoClientList as $item) {
            if (trim($item) == $client) {
                $valid = true;
            }
        }
        if (!$valid) {
            return Response::send(-1, '请在 同步登录服务端增加客户端地址 ' . $client);
        }
        Session::put('adminSSOClient', $client);

        if (Session::get('_adminUserId', 0)) {
            return Response::send(0, null, null, env('ADMIN_PATH', '/admin/') . 'sso/server_success');
        }

        return Response::send(0, null, null, env('ADMIN_PATH', '/admin/') . 'login?redirect=' . urlencode(env('ADMIN_PATH', '/admin/') . 'sso/server_success'));
    }

    public function ssoServerSuccess()
    {
        if (!Session::get('_adminUserId', 0)) {
            return Response::send(0, null, null, env('ADMIN_PATH', '/admin/') . 'login?redirect=' . urlencode(env('ADMIN_PATH', '/admin/') . 'sso/server_success'));
        }
        $adminUser = AdminUtil::get(Session::get('_adminUserId', 0));

        $ssoSecret = ConfigUtil::get('adminSSOServerSecret');
        if (empty($ssoSecret)) {
            return Response::send(-1, '请设置 同步登录服务端通讯秘钥');
        }

        $server = Request::domainUrl() . env('ADMIN_PATH', '/admin/') . 'sso/server';
        $timestamp = time();
        $username = $adminUser['username'];
        $sign = md5(md5($ssoSecret) . md5($timestamp . '') . md5($server) . md5($username));

        $ssoClient = Session::get('adminSSOClient', '');
        if (empty($ssoClient)) {
            return Response::send(0, '登录成功但是没有找到客户端');
        }
        Session::forget('adminSSOClient', $ssoClient);

        $redirect = $ssoClient . '?server=' . urlencode($server) . '&timestamp=' . $timestamp
            . '&username=' . urlencode(base64_encode($username)) . '&sign=' . $sign;

        return Response::send(0, null, null, $redirect);
    }

    public function ssoServerLogout()
    {
        $input = InputPackage::buildFromInput();
        Session::forget('_adminUserId');
        $redirect = $input->getTrimString('redirect', env('ADMIN_PATH', '/admin/'));
        return Response::send(0, null, null, $redirect);
    }
}
