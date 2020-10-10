<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;

class FieldSelect extends BaseField
{
    public $options;
    public $optionType;

    public function viewHtml(&$data)
    {
        return $this->listHtml($data);
    }

    public function listHtml(&$data)
    {
        if (empty($this->options) && !empty($this->optionType)) {
            $type = $this->optionType;
            $this->options = $type::getList();
        }

        if (isset($this->options[$data])) {
            return $this->options[$data];
        }
        return '';
    }

    public function addHtml()
    {
        if (empty($this->options) && !empty($this->optionType)) {
            $type = $this->optionType;
            $this->options = $type::getList();
        }
        return View::make('admin::cms.field.select.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'options' => &$this->options,
            'default' => $this->default,
        ])->render();
    }


    public function editHtml(&$data)
    {
        if (empty($this->options) && !empty($this->optionType)) {
            $type = $this->optionType;
            $this->options = $type::getList();
        }
        return View::make('admin::cms.field.select.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'options' => &$this->options,
            'data' => &$data
        ])->render();
    }

    public function searchHtml()
    {
        if (empty($this->options) && !empty($this->optionType)) {
            $type = $this->optionType;
            $this->options = $type::getList();
        }
        return View::make('admin::cms.field.select.search', [
            'key' => &$this->key,
            'field' => &$this->field,
            'options' => &$this->options,
        ])->render();
    }

    public function exportValue(&$data)
    {
        if (empty($this->options) && !empty($this->optionType)) {
            $type = $this->optionType;
            $this->options = $type::getList();
        }

        if (isset($this->options[$data])) {
            return $this->options[$data];
        }
        return '';
    }

}
