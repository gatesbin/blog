<?php

namespace TechSoft\Laravel\Admin\Traits;


use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;

trait ConfigContactTrait
{
    public function contact(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'contact',
            'pageTitle' => '联系方式',
            'fields' => [
                'contactPhone' => ['type' => FieldText::class, 'title' => '电话', 'desc' => ''],
                'contactEmail' => ['type' => FieldText::class, 'title' => '邮箱', 'desc' => ''],
                'contactQQ' => ['type' => FieldText::class, 'title' => 'QQ', 'desc' => ''],
                'contactWechat' => ['type' => FieldText::class, 'title' => '微信', 'desc' => ''],
                'contactWechatQrcode' => ['type' => FieldImage::class, 'title' => '微信二维码', 'desc' => ''],
                'contactWechatOfficialAccount' => ['type' => FieldText::class, 'title' => '微信公众号', 'desc' => ''],
                'contactWechatOfficialAccountQrcode' => ['type' => FieldImage::class, 'title' => '微信公众号二维码', 'desc' => ''],
                'contactSina' => ['type' => FieldText::class, 'title' => '新浪微博', 'desc' => ''],
                'contactSinaQrcode' => ['type' => FieldImage::class, 'title' => '新浪微博二维码', 'desc' => ''],
            ]
        ]);
    }
}