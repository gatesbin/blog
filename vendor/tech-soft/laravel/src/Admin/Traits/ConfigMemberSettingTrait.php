<?php

namespace TechSoft\Laravel\Admin\Traits;


use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;

trait ConfigMemberSettingTrait
{
    public function memberSetting(ConfigCms $configCms)
    {
        $fileds = [
            'loginCaptchaEnable' => ['type' => FieldSwitch::class, 'title' => '启用登录验证码', 'desc' => ''],
            'registerDisable' => ['type' => FieldSwitch::class, 'title' => '禁用注册', 'desc' => ''],
            'registerEmailEnable' => ['type' => FieldSwitch::class, 'title' => '启用邮箱注册', 'desc' => ''],
            'registerPhoneEnable' => ['type' => FieldSwitch::class, 'title' => '启用手机注册', 'desc' => ''],
            'retrieveDisable' => ['type' => FieldSwitch::class, 'title' => '禁用找回密码', 'desc' => ''],
            'retrievePhoneEnable' => ['type' => FieldSwitch::class, 'title' => '启用手机找回密码', 'desc' => ''],
            'retrieveEmailEnable' => ['type' => FieldSwitch::class, 'title' => '启用邮箱找回密码', 'desc' => ''],
        ];
        return $configCms->execute($this, [
            'group' => 'memberSetting',
            'pageTitle' => '用户功能',
            'fields' => $fileds
        ]);
    }
}
