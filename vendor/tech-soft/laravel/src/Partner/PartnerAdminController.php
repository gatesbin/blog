<?php

namespace TechSoft\Laravel\Partner;

use TechSoft\Laravel\Admin\Cms\BasicCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Support\AdminCheckController;

class PartnerAdminController extends AdminCheckController
{
    protected $cmsConfigBasic = [
        'model' => 'partner',
        'pageTitle' => '友情链接',
        'group' => 'data',
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'fields' => [
            'position' => ['type' => FieldSelect::class, 'title' => '位置', 'list' => true, 'edit' => true, 'add' => true, 'desc' => '请保持默认', 'options' => [
                'pcHome' => 'PC首页',
            ]],
            'title' => ['type' => FieldText::class, 'title' => '标题', 'list' => true, 'edit' => true, 'add' => true,],
            'logo' => ['type' => FieldImage::class, 'title' => 'Logo', 'list' => true, 'edit' => true, 'add' => true,],
            'link' => ['type' => FieldText::class, 'title' => '链接', 'list' => true, 'edit' => true, 'add' => true, 'desc' => '网址',],
            'sort' => ['type' => FieldText::class, 'title' => '排序', 'list' => true, 'edit' => true, 'add' => true, 'desc' => '数字越小越靠前',],
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
        PartnerUtil::clearCache($data['position']);
    }

    public function dataPostEdit(&$data)
    {
        PartnerUtil::clearCache($data['position']);
    }

    public function dataPostDelete(&$data)
    {
        PartnerUtil::clearCache($data['position']);
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