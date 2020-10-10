<?php

namespace App\Http\Controllers\Admin;

use TechSoft\Laravel\Admin\Cms\BasicCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldDate;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;
use TechSoft\Laravel\Admin\Support\AdminCheckController;

class MessageController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'message',
        'pageTitle' => '留言',
        'group' => 'data',
        'canEdit' => true,
        'canDelete' => true,
        'canView' => true,
        'fields' => [
            'created_at' => ['type' => FieldDate::class, 'title' => '时间', 'list' => true, 'view' => true,],
            'username' => ['type' => FieldText::class, 'title' => '用户', 'list' => true, 'view' => true, 'edit' => true,],
            'email' => ['type' => FieldText::class, 'title' => '邮箱', 'view' => true, 'edit' => true, 'list' => true,],
            'url' => ['type' => FieldText::class, 'title' => 'URL', 'list' => true, 'view' => true, 'edit' => true,],
            'content' => ['type' => FieldTextarea::class, 'title' => '内容', 'view' => true, 'edit' => true, 'list' => true,],
            'reply' => ['type' => FieldTextarea::class, 'title' => '回复内容', 'view' => true, 'edit' => true, 'list' => true,],
        ]
    ];

    public function dataList(BasicCms $basicCms)
    {
        return $basicCms->executeList($this, $this->cmsConfigData);
    }

    public function dataView(BasicCms $basicCms)
    {
        return $basicCms->executeView($this, $this->cmsConfigData);
    }

    public function dataAdd(BasicCms $basicCms)
    {
        return $basicCms->executeAdd($this, $this->cmsConfigData);
    }

    public function dataEdit(BasicCms $basicCms)
    {
        return $basicCms->executeEdit($this, $this->cmsConfigData);
    }

    public function dataDelete(BasicCms $basicCms)
    {
        return $basicCms->executeDelete($this, $this->cmsConfigData);
    }
}