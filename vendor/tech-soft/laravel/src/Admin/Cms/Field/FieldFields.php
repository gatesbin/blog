<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;

class FieldFields extends BaseField
{
    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_SELECT = 'select';


    public static function parseHtml($data, $fieldName = 'fields[]')
    {
        if (empty($data) || !is_array($data)) {
            return '';
        }
        $html = [];
        foreach ($data as $field) {
            if (empty($field['name']) || empty($field['type'])) {
                continue;
            }
            $html[] = '<div class="line">';
            $html[] = '<div class="label">' . htmlspecialchars($field['name']) . '</div>';
            $html[] = '<div class="field">';
            switch ($field['type']) {
                case self::FIELD_TYPE_TEXT:
                    $html[] = '<input type="text" name="' . $fieldName . '" value="" />';
                    break;
                case self::FIELD_TYPE_SELECT:
                    $option = [];

                    foreach (explode("\n", $field['option']) as $opt) {
                        $opt = trim($opt);
                        if (empty($opt)) {
                            continue;
                        }
                        $option[] = $opt;
                    }
                    $html[] = '<select name="' . $fieldName . '">';
                    foreach ($option as $opt) {
                        $html[] = '<option value="' . htmlspecialchars($opt) . '">' . htmlspecialchars($opt) . '</option>';
                    }
                    $html[] = '</select>';
                    break;
            }
            $html[] = '</div>';
            $html[] = '</div>';
        }
        return join('', $html);
    }

    public static function viewSummary($data)
    {
        if (empty($data) || !is_array($data)) {
            return '';
        }
        $fields = [];
        foreach ($data as $field) {
            if (empty($field['name']) || empty($field['type'])) {
                continue;
            }
            switch ($field['type']) {
                case self::FIELD_TYPE_TEXT:
                    $fields[] = $field['name'] . '：[单行文本]';
                    break;
                case self::FIELD_TYPE_SELECT:
                    $fields[] = $field['name'] . '：[选择框]';
                    break;
            }
        }
        return join('<br />', $fields);
    }

    public function viewHtml(&$data)
    {
        return self::viewSummary($data);
    }

    public function listHtml(&$data)
    {
        return self::viewSummary($data);
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.fields.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.fields.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data
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
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }

    public function inputProcess($value)
    {
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return ['code' => 0, 'msg' => null, 'data' => $value];
    }

}
