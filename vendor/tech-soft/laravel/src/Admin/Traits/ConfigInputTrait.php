<?php


namespace TechSoft\Laravel\Admin\Traits;


use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Html\HtmlType;

trait ConfigInputTrait
{
    public function input(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'input',
            'pageTitle' => '输入设置',
            'fields' => [
                'editorType' => ['type' => FieldSelect::class, 'title' => '编辑器设置', 'desc' => '', 'options' => [
                    HtmlType::RICH_TEXT => '富文本',
                    HtmlType::MARKDOWN => 'Markdown',
                ],],
            ]
        ]);
    }
}