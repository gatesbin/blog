<?php

namespace TechSoft\Laravel\Member\Traits;


use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;


trait MemberProfileWebControllerTrait
{
    use MemberProfileApiControllerTrait;

    public function passwordView()
    {
        if (Request::isPost()) {
            $ret = $this->password()->getData(true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            return Response::send(0, $ret['msg']);
        }
        return $this->_view('memberProfile.password');
    }

    public function avatarView()
    {
        if (Request::isPost()) {
            $ret = $this->avatar()->getData(true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            return Response::send(0, $ret['msg'], null, '[reload]');
        }
        return $this->_view('memberProfile.avatar');
    }

    public function captchaView()
    {
        $ret = $this->captcha()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        $image = base64_decode(substr($ret['data']['image'], strlen('data:image/png;base64,')));
        return Response::raw($image, [
            'Content-Type' => 'image/png'
        ]);
    }

    public function emailView()
    {
        if (Request::isPost()) {
            $ret = $this->email()->getData(true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            return Response::send(0, $ret['msg'], null, '[reload]');
        }
        return $this->_view('memberProfile.email');
    }

    public function emailVerifyView()
    {
        $ret = $this->emailVerify()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return Response::send(0, $ret['msg']);
    }

    public function phoneView()
    {
        if (Request::isPost()) {
            $ret = $this->phone()->getData(true);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            return Response::send(0, $ret['msg'], null, '[reload]');
        }
        return $this->_view('memberProfile.phone');
    }

    public function phoneVerifyView()
    {
        $ret = $this->phoneVerify()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return Response::send(0, $ret['msg']);
    }

}