<?php

namespace TechSoft\Laravel\Member\Traits;


use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Response;
use TechOnline\Laravel\Util\FileUtils;
use TechOnline\Utils\FileUtil;
use TechOnline\Utils\FormatUtil;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Mews\Captcha\Facades\Captcha;
use TechSoft\Laravel\Api\ApiSessionUtil;
use TechSoft\Laravel\Api\ResponseCodes;
use TechSoft\Laravel\Mail\MailUtil;
use TechSoft\Laravel\Member\Events\MemberUserUpdatedEvent;
use TechSoft\Laravel\Member\MemberUtil;
use TechSoft\Laravel\Sms\SmsTemplate;
use TechSoft\Laravel\Sms\SmsUtil;
use TechSoft\Laravel\Util\ApiSessionCaptchaUtil;


trait MemberProfileApiControllerTrait
{
    public function password()
    {
        $input = InputPackage::buildFromInput();

        $passwordOld = $input->getTrimString('passwordOld');
        $passwordNew = $input->getTrimString('passwordNew');
        $passwordRepeat = $input->getTrimString('passwordRepeat');

        if ($passwordNew != $passwordRepeat) {
            return Response::json(-1, '两次新密码输入不一致');
        }

        $ret = MemberUtil::changePassword($this->memberUserId(), $passwordNew, $passwordOld);
        if ($ret['code']) {
            return Response::json(-1, $ret['msg']);
        }
        Event::fire(new MemberUserUpdatedEvent($this->memberUserId(), 'password'));
        return Response::json(0, '修改成功', null, '[reload]');
    }

    public function avatar()
    {
        switch (Input::get('type')) {
            case 'cropper':
                $avatar = Input::get('avatar');
                if (empty($avatar)) {
                    return Response::json(-1, '头像内容为空');
                }
                $avatarType = null;
                if (Str::startsWith($avatar, 'data:image/jpeg;base64,')) {
                    $avatarType = 'jpg';
                    $avatar = substr($avatar, strlen('data:image/jpeg;base64,'));
                } else if (Str::startsWith($avatar, 'data:image/png;base64,')) {
                    $avatarType = 'png';
                    $avatar = substr($avatar, strlen('data:image/png;base64,'));
                }
                if (empty($avatarType)) {
                    return Response::json(-1, '头像数据为空');
                }
                $avatar = @base64_decode($avatar);
                if (empty($avatar)) {
                    return Response::json(-1, '头像内容为空');
                }
                $ret = MemberUtil::setAvatar($this->memberUserId(), $avatar, $avatarType);
                if ($ret['code']) {
                    return $ret;
                }
                Event::fire(new MemberUserUpdatedEvent($this->memberUserId(), 'avatar'));
                return Response::json(0, '保存成功', null, '[reload]');
            default:
                $avatar = Input::get('avatar');
                if (empty($avatar)) {
                    return Response::json(-1, '头像未修改');
                }
                $avatar = FileUtils::savePathToLocal($avatar);
                if (empty($avatar)) {
                    return Response::json(-1, '读取头像文件失败:-1');
                }
                $avatarExt = FileUtil::extension($avatar);
                if (!in_array($avatarExt, config('data.upload.image.extensions'))) {
                    return Response::json(-1, '头像格式不合法');
                }
                $avatar = file_get_contents($avatar);
                if (empty($avatar)) {
                    return Response::json(-1, '读取头像文件失败:-2');
                }
                $ret = MemberUtil::setAvatar($this->memberUserId(), $avatar, $avatarExt);
                if ($ret['code']) {
                    return $ret;
                }
                Event::fire(new MemberUserUpdatedEvent($this->memberUserId(), 'avatar'));
                return Response::json(0, '保存成功', null, '[reload]');
        }
    }

    public function captcha()
    {
        return Response::json(0, 'ok', [
            'image' => ApiSessionCaptchaUtil::create('memberProfileCaptcha'),
        ]);
    }

    public function email()
    {
        $input = InputPackage::buildFromInput();
        $email = $input->getEmail('email');
        $verify = $input->getTrimString('verify');

        if (empty($email)) {
            return Response::json(-1, '邮箱不能为空');
        }
        if (!FormatUtil::isEmail($email)) {
            return Response::json(-1, '邮箱格式不正确');
        }
        if (empty($verify)) {
            return Response::json(-1, '验证码不能为空');
        }
        if ($verify != ApiSessionUtil::get('memberProfileEmailVerify')) {
            return Response::json(-1, '验证码不正确');
        }
        if (ApiSessionUtil::get('memberProfileEmailVerifyTime') + 60 * 60 < time()) {
            return Response::json(0, '验证码已过期');
        }
        if ($email != ApiSessionUtil::get('memberProfileEmail')) {
            return Response::json(-1, '两次邮箱不一致');
        }

        $memberUserExists = MemberUtil::getByEmail($email);
        if (!empty($memberUserExists)) {
            if ($memberUserExists['id'] != $this->memberUserId()) {
                return Response::json(-1, '该邮箱已被其他账户绑定');
            }
            if ($memberUserExists['id'] == $this->memberUserId() && $memberUserExists['email'] == $email) {
                return Response::json(-1, '邮箱未修改，无需重新绑定。');
            }
        }

        MemberUtil::update($this->memberUserId(), [
            'emailVerified' => true,
            'email' => $email,
        ]);

        Event::fire(new MemberUserUpdatedEvent($this->memberUserId(), 'email'));
        ApiSessionUtil::forget('memberProfileEmailVerify');
        ApiSessionUtil::forget('memberProfileEmailVerifyTime');
        ApiSessionUtil::forget('memberProfileEmail');

        return Response::json(0, '修改成功');
    }

    public function emailVerify()
    {
        $email = Input::get('target');
        if (empty($email)) {
            return Response::json(-1, '邮箱不能为空');
        }
        if (!FormatUtil::isEmail($email)) {
            return Response::json(-1, '邮箱格式不正确');
        }

        if (!ApiSessionCaptchaUtil::check('memberProfileCaptcha', Input::get('captcha'))) {
            return Response::json(ResponseCodes::CAPTCHA_ERROR, '验证码错误');
        }

        $memberUserExists = MemberUtil::getByEmail($email);
        if (!empty($memberUserExists)) {
            if ($memberUserExists['id'] != $this->memberUserId()) {
                return Response::json(-1, '该邮箱已被其他账户绑定');
            }
            if ($memberUserExists['id'] == $this->memberUserId() && $memberUserExists['email'] == $email) {
                return Response::json(-1, '邮箱未修改，无需重新绑定。');
            }
        }

        if (ApiSessionUtil::get('memberProfileEmailVerifyTime') && $email == ApiSessionUtil::get('memberProfileEmail')) {
            if (ApiSessionUtil::get('memberProfileEmailVerifyTime') + 60 * 10 > time()) {
                return Response::json(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        ApiSessionUtil::put('memberProfileEmailVerify', $verify);
        ApiSessionUtil::put('memberProfileEmailVerifyTime', time());
        ApiSessionUtil::put('memberProfileEmail', $email);

        MailUtil::send($email, '验证码', 'verify', ['code' => $verify]);

        return Response::json(0, '验证码发送成功');

    }

    public function phone()
    {

        $input = InputPackage::buildFromInput();

        $phone = $input->getPhone('phone');
        $verify = $input->getTrimString('verify');

        if (empty($phone)) {
            return Response::json(-1, '手机不能为空');
        }
        if (!FormatUtil::isPhone($phone)) {
            return Response::json(-1, '手机格式不正确');
        }
        if (empty($verify)) {
            return Response::json(-1, '验证码不能为空');
        }
        if ($verify != ApiSessionUtil::get('memberProfilePhoneVerify')) {
            return Response::json(-1, '验证码不正确');
        }
        if (ApiSessionUtil::get('memberProfilePhoneVerifyTime') + 60 * 60 < time()) {
            return Response::json(0, '验证码已过期');
        }
        if ($phone != ApiSessionUtil::get('memberProfilePhone')) {
            return Response::json(-1, '两次手机不一致');
        }

        $memberUserExists = MemberUtil::getByPhone($phone);
        if (!empty($memberUserExists)) {
            if ($memberUserExists['id'] != $this->memberUserId()) {
                return Response::json(-1, '该手机已被其他账户绑定');
            }
            if ($memberUserExists['id'] == $this->memberUserId() && $memberUserExists['phone'] == $phone) {
                return Response::json(-1, '手机号未修改，无需重新绑定。');
            }
        }

        MemberUtil::update($this->memberUserId(), [
            'phoneVerified' => true,
            'phone' => $phone,
        ]);

        Event::fire(new MemberUserUpdatedEvent($this->memberUserId(), 'phone'));
        ApiSessionUtil::forget('memberProfilePhoneVerify');
        ApiSessionUtil::forget('memberProfilePhoneVerifyTime');
        ApiSessionUtil::forget('memberProfilePhone');

        return Response::json(0, '修改成功');
    }

    public function phoneVerify()
    {
        $phone = Input::get('target');
        if (empty($phone)) {
            return Response::json(-1, '手机不能为空');
        }
        if (!FormatUtil::isPhone($phone)) {
            return Response::json(-1, '手机格式不正确');
        }

        if (!ApiSessionCaptchaUtil::check('memberProfileCaptcha', Input::get('captcha'))) {
            return Response::json(ResponseCodes::CAPTCHA_ERROR, '图片验证码错误');
        }

        $memberUserExists = MemberUtil::getByPhone($phone);
        if (!empty($memberUserExists)) {
            if ($memberUserExists['id'] != $this->memberUserId()) {
                return Response::json(-1, '该手机已被其他账户绑定');
            }
            if ($memberUserExists['id'] == $this->memberUserId() && $memberUserExists['phone'] == $phone) {
                return Response::json(-1, '手机号未修改，无需重新绑定。');
            }
        }

        if (ApiSessionUtil::get('memberProfilePhoneVerifyTime') && $phone == ApiSessionUtil::get('memberProfilePhone')) {
            if (ApiSessionUtil::get('memberProfilePhoneVerifyTime') + 60 * 2 > time()) {
                return Response::json(0, '验证码发送成功!');
            }
        }

        $verify = rand(100000, 999999);
        ApiSessionUtil::put('memberProfilePhoneVerify', $verify);
        ApiSessionUtil::put('memberProfilePhoneVerifyTime', time());
        ApiSessionUtil::put('memberProfilePhone', $phone);

        SmsUtil::send($phone, SmsTemplate::VERIFY, ['code' => $verify]);

        return Response::json(0, '验证码发送成功');

    }
}