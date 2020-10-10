<?php


namespace TechSoft\Laravel\Admin\Traits;


use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;

trait ConfigAppBasicTrait
{
    public function appBasic(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'appBasic',
            'pageTitle' => 'APP设置',
            'fields' => [
                'appVersion' => ['type' => FieldText::class, 'title' => '版本', 'desc' => '格式为 x.x.x'],
                'appMinVersion' => ['type' => FieldText::class, 'title' => '强制最低版本', 'desc' => '格式为 x.x.x，小于该版本的会在用户端强制升级'],
                'appDownloadUrl' => ['type' => FieldText::class, 'title' => '最新下载链接', 'desc' => 'APP下载完整URL'],
                'appWebServiceUrl' => ['type' => FieldText::class, 'title' => '我的客服URL', 'desc' => ''],
                'appWebHelpCenterUrl' => ['type' => FieldText::class, 'title' => '帮助中心URL', 'desc' => ''],
            ]
        ]);
    }
}