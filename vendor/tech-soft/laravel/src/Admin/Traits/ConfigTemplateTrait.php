<?php


namespace TechSoft\Laravel\Admin\Traits;


use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;

trait ConfigTemplateTrait
{
    public function template(ConfigCms $configCms)
    {
        $options = [
            'default' => '默认',
        ];
        $themeOptions = [
            'default' => '默认',
        ];
        if (!empty($this->_templateThemeOptions)) {
            $themeOptions = $this->_templateThemeOptions;
        }
        return $configCms->execute($this, [
            'group' => 'template',
            'pageTitle' => '模板设置',
            'fields' => [
                'siteTemplate' => ['type' => FieldSelect::class, 'title' => '模板', 'desc' => '', 'options' => $options,],
                'siteTemplateTheme' => ['type' => FieldSelect::class, 'title' => '主题', 'desc' => '', 'options' => $themeOptions,],
            ]
        ]);
    }
}