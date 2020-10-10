<?php


namespace TechSoft\Laravel\Admin\Traits;


use TechOnline\Laravel\Redis\RedisUtil;
use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldRichtext;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;
use TechSoft\Laravel\Firewall\FirewallUtil;

trait ConfigArticleLicenseTrait
{
    public function articleLicense(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'license',
            'pageTitle' => '使用协议',
            'fields' => [
                'articleLicenseEnable' => ['type' => FieldSwitch::class, 'title' => '协议启用', 'desc' => ''],
                'articleLicenseTitle' => ['type' => FieldText::class, 'title' => '协议标题', 'desc' => ''],
                'articleLicenseContent' => ['type' => FieldRichtext::class, 'title' => '协议内容', 'desc' => ''],
            ]
        ]);
    }

}