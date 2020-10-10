<?php

namespace TechSoft\Laravel\News;

use TechSoft\Laravel\Admin\Cms\CategoryCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Support\AdminCheckController;

class NewsCategoryAdminController extends AdminCheckController
{

    private $cmsConfigData = [
        'model' => 'news_category',
        'pageTitle' => '新闻分类',
        'group' => 'data',
        'maxLevel' => 1,
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'canView' => false,
        'canSort' => true,
        'primaryKeyShow' => false,
        'fields' => [
            'name' => ['type' => FieldText::class, 'title' => '名称', 'list' => true, 'add' => true, 'edit' => true, 'view' => true],
        ]
    ];


    public function dataList(CategoryCms $categoryCms)
    {
        return $categoryCms->executeList($this, $this->cmsConfigData);
    }

    public function dataAdd(CategoryCms $categoryCms)
    {
        return $categoryCms->executeAdd($this, $this->cmsConfigData);
    }

    public function dataEdit(CategoryCms $categoryCms)
    {
        return $categoryCms->executeEdit($this, $this->cmsConfigData);
    }

    public function dataDelete(CategoryCms $categoryCms)
    {
        return $categoryCms->executeDelete($this, $this->cmsConfigData);
    }

    public function dataView(CategoryCms $categoryCms)
    {
        return $categoryCms->executeView($this, $this->cmsConfigData);
    }

    public function dataSort(CategoryCms $categoryCms)
    {
        return $categoryCms->executeSort($this, $this->cmsConfigData);
    }

}