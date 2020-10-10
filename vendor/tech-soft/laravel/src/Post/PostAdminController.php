<?php

namespace TechSoft\Laravel\Post;


use TechSoft\Laravel\Admin\Cms\BasicCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldRichtext;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;
use TechSoft\Laravel\Admin\Support\AdminCheckController;

class PostAdminController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'post',
        'pageTitle' => '文章管理',
        'group' => 'data',
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'canView' => true,
        'fields' => [
            'position' => ['type' => FieldSelect::class, 'title' => '位置', 'list' => true, 'view' => true, 'add' => true, 'edit' => true, 'options' => [
                'footer' => '页面底部',
            ],],
            'title' => ['type' => FieldText::class, 'title' => '标题', 'list' => true, 'view' => true, 'add' => true, 'edit' => true,],
            'content' => ['type' => FieldRichtext::class, 'title' => '内容', 'view' => true, 'add' => true, 'edit' => true,],
            'sort' => ['type' => FieldText::class, 'title' => '排序', 'list' => true, 'view' => true, 'add' => true, 'edit' => true, 'default' => 999,],
        ]
    ];

    public function dataPostChange($type, $data)
    {
        PostUtil::clearCache();
    }

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