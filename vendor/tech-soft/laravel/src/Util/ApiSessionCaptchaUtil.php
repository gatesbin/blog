<?php

namespace TechSoft\Laravel\Util;


use TechOnline\Laravel\Http\Response;
use Illuminate\Support\Facades\Session;
use Mews\Captcha\Facades\Captcha;
use TechSoft\Laravel\Api\ApiSessionUtil;

class ApiSessionCaptchaUtil
{
    public static function create($apiSessionKey)
    {
        $captcha = Captcha::create('default');
        ApiSessionUtil::put($apiSessionKey, Session::get('captcha'));
        return 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent());
    }

    public static function check($apiSessionKey, $captcha)
    {
        Session::put('captcha', ApiSessionUtil::get($apiSessionKey));
        ApiSessionUtil::forget($apiSessionKey);
        if (Captcha::check($captcha)) {
            return true;
        }
        return false;

    }
}