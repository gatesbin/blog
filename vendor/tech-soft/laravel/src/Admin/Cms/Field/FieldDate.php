<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;

class FieldDate extends BaseField
{
    public function addHtml()
    {
        return View::make('admin::cms.field.date.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.date.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data
        ])->render();
    }

    public function searchHtml()
    {
        return View::make('admin::cms.field.date.search', [
            'key' => &$this->key,
            'field' => &$this->field,
        ])->render();
    }
}