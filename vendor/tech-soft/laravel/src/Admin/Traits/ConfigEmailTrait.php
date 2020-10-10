<?php

namespace TechSoft\Laravel\Admin\Traits;


use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use Illuminate\Support\Facades\Mail;
use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;
use TechSoft\Laravel\Config\ConfigUtil;

trait ConfigEmailTrait
{
    public function email(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'email',
            'pageTitle' => '邮件发送',
            'fields' => [
                'systemEmailEnable' => ['type' => FieldSwitch::class, 'title' => '开启邮件发送', 'desc' => ''],
                'systemEmailSmtpServer' => ['type' => FieldText::class, 'title' => 'SMTP服务器地址', 'desc' => ''],
                'systemEmailSmtpSsl' => ['type' => FieldSwitch::class, 'title' => 'SMTP是否为SSL', 'desc' => ''],
                'systemEmailSmtpUser' => ['type' => FieldText::class, 'title' => 'SMTP用户', 'desc' => ''],
                'systemEmailSmtpPassword' => ['type' => FieldText::class, 'title' => 'SMTP密码', 'desc' => ''],
            ]
        ]);
    }

    public function emailTest(ConfigCms $configCms)
    {
        if (Request::isPost()) {
            $email = InputPackage::buildFromInput()->getEmail('email');
            if (empty($email)) {
                return Response::send(-1, '邮箱为空或格式不正确');
            }
            config([
                'mail' => [
                    'driver' => 'smtp',
                    'host' => ConfigUtil::get('systemEmailSmtpServer'),
                    'port' => ConfigUtil::get('systemEmailSmtpSsl', false) ? 465 : 25,
                    'encryption' => ConfigUtil::get('systemEmailSmtpSsl', false) ? 'ssl' : 'tls',
                    'from' => array('address' => ConfigUtil::get('systemEmailSmtpUser'), 'name' => ConfigUtil::get('siteName') . ' @ ' . ConfigUtil::get('siteDomain')),
                    'username' => ConfigUtil::get('systemEmailSmtpUser'),
                    'password' => ConfigUtil::get('systemEmailSmtpPassword'),
                ]
            ]);
            $emailUserName = $email;
            $subject = '测试邮件';
            try {
                Mail::send('soft::mail.test', [], function ($message) use ($email, $emailUserName, $subject) {
                    $message->to($email, $emailUserName)->subject($subject);
                });
            } catch (\Exception $e) {
                return Response::send(-1, '邮件发送失败:(' . $e->getMessage() . ')');
            }
            return Response::send(0, '测试邮件成功发送到' . $email);
        }
        return $configCms->execute($this, [
            'group' => 'emailTest',
            'pageTitle' => '邮件发送测试',
            'fields' => [
                'email' => ['type' => FieldText::class, 'title' => '测试接收邮箱', 'desc' => ''],
            ]
        ]);
    }
}
