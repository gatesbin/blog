<?php

namespace TechSoft\Laravel\Feedback\Admin;


use TechSoft\Laravel\Admin\Cms\BasicCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldDate;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldRichtext;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;
use TechSoft\Laravel\Admin\Support\AdminCheckController;

class FeedbackController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'feedback',
        'pageTitle' => '意见反馈',
        'group' => 'data',
        'canAdd' => false,
        'canEdit' => false,
        'canDelete' => true,
        'canView' => true,
        'fields' => [
            'created_at' => ['type' => FieldDate::class, 'title' => '时间', 'list' => true, 'view' => true, 'add' => true, 'edit' => true,],
            'title' => ['type' => FieldText::class, 'title' => '主题', 'list' => true, 'view' => true, 'add' => true, 'edit' => true,],
            'content' => ['type' => FieldTextarea::class, 'title' => '内容', 'list' => true, 'view' => true, 'add' => true, 'edit' => true,],
            'contact' => ['type' => FieldText::class, 'title' => '联系方式', 'list' => true, 'view' => true, 'add' => true, 'edit' => true,],
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