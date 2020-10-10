<?php

namespace TechSoft\Laravel\Admin\Traits;


use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;

trait ConfigSettingTrait
{
    public function setting(ConfigCms $configCms, $param = [])
    {
        $fileds = [
            'siteLogo' => ['type' => FieldImage::class, 'title' => '网站Logo', 'desc' => ''],
            'siteName' => ['type' => FieldText::class, 'title' => '网站名称', 'desc' => ''],
            'siteSlogan' => ['type' => FieldText::class, 'title' => '网站副标题', 'desc' => ''],
            'siteDomain' => ['type' => FieldText::class, 'title' => '网站域名', 'desc' => ''],
            'siteKeywords' => ['type' => FieldText::class, 'title' => '网站关键词', 'desc' => ''],
            'siteDescription' => ['type' => FieldTextarea::class, 'title' => '网站描述', 'desc' => ''],
            'siteBeian' => ['type' => FieldText::class, 'title' => '网站备案编号', 'desc' => ''],
            'siteFavIco' => ['type' => FieldImage::class, 'title' => '网站ICO', 'desc' => '',],
        ];
        if (!empty($param['siteTemplateOptions'])) {
            $fileds['siteTemplate'] = ['type' => FieldSelect::class, 'title' => '网站模板', 'desc' => '', 'options' => $param['siteTemplateOptions']];
        }

        return $configCms->execute($this, [
            'group' => 'setting',
            'pageTitle' => '基本配置',
            'fields' => $fileds
        ]);
    }
}
