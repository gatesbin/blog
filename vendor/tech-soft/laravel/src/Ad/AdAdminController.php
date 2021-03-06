<?php

namespace TechSoft\Laravel\Ad;

use TechSoft\Laravel\Admin\Cms\BasicCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Support\AdminCheckController;

class AdAdminController extends AdminCheckController
{
    protected $cmsConfigBasic = [
        'model' => 'ad',
        'pageTitle' => '广告位',
        'group' => 'data',
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'fields' => [
            'position' => ['type' => FieldSelect::class, 'title' => '位置', 'list' => true, 'edit' => true, 'add' => true, 'desc' => '请保持默认', 'options' => []],
            'sort' => ['type' => FieldText::class, 'title' => '排序', 'list' => true, 'edit' => true, 'add' => true, 'desc' => '数字越小越靠前',],
            'image' => ['type' => FieldImage::class, 'title' => '图片', 'list' => true, 'edit' => true, 'add' => true,],
            'link' => ['type' => FieldText::class, 'title' => '链接', 'list' => true, 'edit' => true, 'add' => true, 'desc' => '链接为空将不会跳转',],
        ]
    ];

    public function __construct()
    {
        parent::__construct();
        $this->setUpConfig();
    }

    protected function setUpConfig()
    {

    }

    public function dataPostAdd(&$data)
    {
        AdUtil::clearCache($data['position']);
    }

    public function dataPostEdit(&$data)
    {
        AdUtil::clearCache($data['position']);
    }

    public function dataPostDelete(&$data)
    {
        AdUtil::clearCache($data['position']);
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