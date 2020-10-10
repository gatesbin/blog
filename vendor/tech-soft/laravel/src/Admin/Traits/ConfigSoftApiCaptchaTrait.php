<?php


namespace TechSoft\Laravel\Admin\Traits;


use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;

trait ConfigSoftApiCaptchaTrait
{
    public function softApiCaptcha(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'softApiCaptcha',
            'pageTitle' => '安全验证码',
            'fields' => [
                'softApiCaptchaEnable' => ['type' => FieldSwitch::class, 'title' => '启用', 'desc' => '
<div>
    访问 <a href="http://api.' . __BASE_SITE__ . '" target="_blank">https://api.' . __BASE_SITE__ . '</a> 申请智能验证码服务
</div>'],
                'softApiCaptchaAppId' => ['type' => FieldText::class, 'title' => 'AppId', 'desc' => ''],
                'softApiCaptchaAppSecret' => ['type' => FieldText::class, 'title' => 'AppSecret', 'desc' => ''],
            ]
        ]);
    }
}