<?php

namespace TechSoft\Laravel\Article;

use TechSoft\Laravel\Admin\Cms\BasicCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldRichtext;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Support\AdminCheckController;

class ArticleAdminController extends AdminCheckController
{
    protected $cmsConfigBasic = [
        'model' => 'article',
        'pageTitle' => '文章管理',
        'group' => 'data',
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'fields' => [
            'position' => ['type' => FieldSelect::class, 'title' => '位置', 'list' => true, 'edit' => true, 'add' => true, 'desc' => '请保持默认', 'options' => [
                'footer' => '网站底部',
            ]],
            'title' => ['type' => FieldText::class, 'title' => '标题', 'list' => true, 'edit' => true, 'add' => true,],
            'content' => ['type' => FieldRichtext::class, 'title' => '内容', 'list' => true, 'edit' => true, 'add' => true,],
        ]
    ];


    public function dataPostAdd(&$data)
    {
        ArticleUtil::clearCache($data['position']);
    }

    public function dataPostEdit(&$data)
    {
        ArticleUtil::clearCache($data['position']);
    }

    public function dataPostDelete(&$data)
    {
        ArticleUtil::clearCache($data['position']);
    }

    public function dataList(BasicCms $basicCms)
    {
        return $basicCms->executeList($this, $this->cmsConfigBasic);;
    }

    public function dataDelete(BasicCms $basicCms)
    {
        return $basicCms->executeDelete($this, $this->cmsConfigBasic);
    }

    public function dataEdit(BasicCms $basicCms)
    {
        return $basicCms->executeEdit($this, $this->cmsConfigBasic);
    }

    public function dataAdd(BasicCms $basicCms)
    {
        return $basicCms->executeAdd($this, $this->cmsConfigBasic);
    }


}