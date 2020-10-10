<?php

namespace App\Http\Controllers\Admin;


use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Util\TagUtil;
use TechSoft\Laravel\Admin\Cms\BasicCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldDatetime;
use TechSoft\Laravel\Admin\Cms\Field\FieldImages;
use TechSoft\Laravel\Admin\Cms\Field\FieldRichtext;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldTag;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;
use TechSoft\Laravel\Admin\Support\AdminCheckController;

class BlogController extends AdminCheckController
{
    private $cmsConfigData = [
        'model' => 'blog',
        'pageTitle' => '博客',
        'group' => 'data',
        'canAdd' => true,
        'canEdit' => true,
        'canDelete' => true,
        'canView' => true,
        'fields' => [
            'isPublished' => ['type' => FieldSwitch::class, 'title' => '已发布', 'list' => true, 'view' => true, 'add' => true, 'edit' => true,],
            'postTime' => ['type' => FieldDatetime::class, 'title' => '发布时间', 'list' => true, 'view' => true, 'add' => true, 'edit' => true,],
            'tag' => ['type' => FieldTag::class, 'title' => '标签', 'view' => true, 'add' => true, 'edit' => true, 'list' => true,],
            'title' => ['type' => FieldText::class, 'title' => '标题', 'list' => true, 'view' => true, 'add' => true, 'edit' => true,],
            'seoKeywords' => ['type' => FieldText::class, 'title' => 'SEO关键词', 'view' => true, 'add' => true, 'edit' => true,],
            'seoDescription' => ['type' => FieldTextarea::class, 'title' => 'SEO描述', 'view' => true, 'add' => true, 'edit' => true,],
            'summary' => ['type' => FieldTextarea::class, 'title' => '摘要', 'view' => true, 'add' => true, 'edit' => true, 'desc' => '摘要为空显示为纯图片格式'],
            'images' => ['type' => FieldImages::class, 'title' => '图片', 'view' => true, 'add' => true, 'edit' => true,],
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

    private function getBlogTags()
    {
        $tags = [];
        foreach (ModelUtil::values('blog', 'tag') as $blogTag) {
            $blogTags = TagUtil::string2Array($blogTag);
            foreach ($blogTags as $blogTag) {
                $tags[$blogTag] = true;
            }
        }
        return array_keys($tags);
    }

    public function dataAdd(BasicCms $basicCms)
    {

        $this->cmsConfigData['fields']['tag']['map'] = $this->getBlogTags();
        return $basicCms->executeAdd($this, $this->cmsConfigData);
    }

    public function dataEdit(BasicCms $basicCms)
    {
        $this->cmsConfigData['fields']['tag']['map'] = $this->getBlogTags();
        return $basicCms->executeEdit($this, $this->cmsConfigData);
    }

    public function dataDelete(BasicCms $basicCms)
    {
        return $basicCms->executeDelete($this, $this->cmsConfigData);
    }
}