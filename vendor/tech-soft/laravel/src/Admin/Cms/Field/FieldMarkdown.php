<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use TechOnline\Utils\MarkdownUtil;
use Illuminate\Support\Facades\View;

class FieldMarkdown extends BaseField
{
    public $markdownHtmlField = null;

    public function addHtml()
    {
        return View::make('admin::cms.field.markdown.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.markdown.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data
        ])->render();
    }

    public function viewHtml(&$data)
    {
        return $data;
    }

    public function valuesSerialize($values)
    {
        if (!empty($this->markdownHtmlField)) {
            $values[$this->markdownHtmlField] = MarkdownUtil::convertToHtml($values[$this->key]);
        }
        return $values;
    }


}