<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;

class FieldSwitch extends BaseField
{
    public $labelYes = '是';
    public $labelNo = '否';

    public function viewHtml(&$data)
    {
        if ($data) {
            return '<span style="color:#659f13;">' . $this->labelYes . '</span>';
        }
        return '<span style="color:#d85030;">' . $this->labelNo . '</span>';
    }


    public function listHtml(&$data)
    {
        if ($data) {
            return '<span style="color:#659f13;">' . $this->labelYes . '</span>';
        }
        return '<span style="color:#d85030;">' . $this->labelNo . '</span>';
    }
    public function searchHtml()
    {
        return View::make('admin::cms.field.switch.search', [
            'key' => &$this->key,
            'field' => &$this->field,
        ])->render();
    }


    public function addHtml()
    {
        return View::make('admin::cms.field.switch.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'labelYes' => &$this->labelYes,
            'labelNo' => &$this->labelNo,
            'default' => $this->default
        ])->render();
    }


    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.switch.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'labelYes' => &$this->labelYes,
            'labelNo' => &$this->labelNo,
            'data' => &$data,
        ])->render();
    }

}
