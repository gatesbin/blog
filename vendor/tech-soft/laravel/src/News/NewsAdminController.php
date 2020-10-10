<?php

namespace TechSoft\Laravel\News;

use TechSoft\Laravel\Admin\Cms\BasicCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldCategory;
use TechSoft\Laravel\Admin\Cms\Field\FieldRichtext;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Support\AdminCheckController;

class NewsAdminController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'news',
        'pageTitle' => '新闻',
        'group' => 'data',
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'canView' => true,
        'fields' => [
            'categoryId' => ['type' => FieldCategory::class, 'title' => '分类', 'list' => true, 'view' => true, 'add' => true, 'edit' => true, 'model' => 'news_category', 'modelId' => 'id', 'modelTitle' => 'name'],
            'title' => ['type' => FieldText::class, 'title' => '标题', 'list' => true, 'view' => true, 'add' => true, 'edit' => true,],
            'content' => ['type' => FieldRichtext::class, 'title' => '内容', 'view' => true, 'add' => true, 'edit' => true,],
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