<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;
use TechSoft\Laravel\Util\HtmlUtil;

class FieldTextarea extends BaseField
{
    public function addHtml()
    {
        return View::make('admin::cms.field.textarea.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.textarea.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data
        ])->render();
    }

    public function viewHtml(&$data)
    {
        return HtmlUtil::text2html($data);
    }
}
