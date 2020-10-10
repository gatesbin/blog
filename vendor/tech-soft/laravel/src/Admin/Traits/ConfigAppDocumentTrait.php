<?php


namespace TechSoft\Laravel\Admin\Traits;


use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldRichtext;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;

trait ConfigAppDocumentTrait
{
    public function appDocument(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'appDocument',
            'pageTitle' => 'APP文档设置',
            'fields' => [
                'appDocumentAbout' => ['type' => FieldRichtext::class, 'title' => '关于我们', 'desc' => '请使用纯文本避免图片'],
                'appDocumentPolicy' => ['type' => FieldRichtext::class, 'title' => '用户协议', 'desc' => '请使用纯文本避免图片'],
                'appDocumentPrivacy' => ['type' => FieldRichtext::class, 'title' => '隐私政策', 'desc' => '请使用纯文本避免图片'],
            ]
        ]);
    }
}