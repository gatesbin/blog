<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;

class FieldValues extends BaseField
{
    public function __construct(&$context)
    {
        parent::__construct($context);
    }


    public function viewHtml(&$data)
    {
        if ($data) {
            $html = [];
            foreach ($data as $v) {
                $html[] = '<span>' . htmlspecialchars($v) . '</span>';
            }
            return join(',', $html);
        }
        return '';
    }

    public function listHtml(&$data)
    {
        if ($data) {
            $html = [];
            foreach ($data as $v) {
                $html[] = '<span>' . htmlspecialchars($v) . '</span>';
            }
            return join(',', $html);
        }
        return '';
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.values.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.values.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data,
        ])->render();
    }

    public function valueSerialize($value)
    {
        $value = json_encode($value);
        return $value;
    }

    public function valueUnserialize($value)
    {
        $value = @json_decode($value, true);
        if (empty($value) || !is_array($value)) {
            $value = [];
        }
        return $value;
    }

    public function inputProcess($value)
    {
        $values = @json_decode($value, true);
        if (empty($values) || !is_array($values)) {
            $values = [];
        }
        return ['code' => 0, 'msg' => null, 'data' => $values];
    }

    public function exportValue(&$data)
    {
        if (empty($data)) {
            return '';
        }
        return join(',', $data);
    }
}
