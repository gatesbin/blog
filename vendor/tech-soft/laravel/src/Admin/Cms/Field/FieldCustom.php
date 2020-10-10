<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;

class FieldCustom extends BaseField
{
    public $viewAdd = '';
    public $viewEdit = '';
    public $valueSerialize = '';
    public $valueUnserialize = '';

    public function addHtml()
    {
        if (empty($this->viewAdd)) {
            return '[FieldCustom missing viewAdd]';
        }
        return View::make($this->viewAdd, [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        if (empty($this->viewEdit)) {
            return '[FieldCustom missing viewEdit]';
        }
        return View::make($this->viewEdit, [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data
        ])->render();
    }

    public function inputGet($inputAll)
    {
        return $inputAll;
    }

}
