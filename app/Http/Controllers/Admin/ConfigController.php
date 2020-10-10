<?php

namespace App\Http\Controllers\Admin;

use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;
use TechSoft\Laravel\Admin\Support\AdminCheckController;
use TechSoft\Laravel\Admin\Traits\ConfigSettingTrait;
use TechSoft\Laravel\Admin\Traits\ConfigVisitTrait;

class ConfigController extends AdminCheckController
{
    use ConfigSettingTrait;
    use ConfigVisitTrait;

    public function blog(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'setting',
            'pageTitle' => '博客配置',
            'fields' => [
                'blogMessageEnable' => ['type' => FieldSwitch::class, 'title' => '开启留言功能', 'desc' => ''],
                'blogCommentEnable' => ['type' => FieldSwitch::class, 'title' => '开启评论功能', 'desc' => ''],
                'blogName' => ['type' => FieldText::class, 'title' => '博客名', 'desc' => ''],
                'blogSlogan' => ['type' => FieldText::class, 'title' => '博客标语', 'desc' => ''],
                'blogIntroduction' => ['type' => FieldTextarea::class, 'title' => '个人介绍',],
                'blogBackground' => ['type' => FieldImage::class, 'title' => '博客左侧图片',],
            ]
        ]);
    }

    public function contact(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'contact',
            'pageTitle' => '联系方式',
            'fields' => [
                'contactEmail' => ['type' => FieldText::class, 'title' => '邮箱', 'desc' => ''],
                'contactWeibo' => ['type' => FieldText::class, 'title' => '微博', 'desc' => ''],
                'contactWechat' => ['type' => FieldText::class, 'title' => '微信', 'desc' => ''],
                'contactQQ' => ['type' => FieldText::class, 'title' => 'QQ', 'desc' => ''],
            ]
        ]);
    }
}
