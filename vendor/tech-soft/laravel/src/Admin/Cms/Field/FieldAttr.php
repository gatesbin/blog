<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;

class FieldAttr extends BaseField
{
    public $server = null;
    public $cdn = '';

    public function __construct(&$context)
    {
        parent::__construct($context);
    }


    public function viewHtml(&$data)
    {
        if ($data) {
            $html = [];
            foreach ($data as $v) {
                $html[] = "<div><b>" . htmlspecialchars($v['name']) . '</b>：<span>' . htmlspecialchars($v['value']) . '</span></div>';
            }
            return join('', $html);
        }
        return '';
    }

    public function listHtml(&$data)
    {
        if ($data) {
            $html = [];
            foreach ($data as $v) {
                $html[] = "<div><b>" . htmlspecialchars($v['name']) . '</b>：<span>' . htmlspecialchars($v['value']) . '</span></div>';
            }
            return join('', $html);
        }
        return '';
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.attr.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'server' => &$this->server,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {

        return View::make('admin::cms.field.attr.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'server' => &$this->server,
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
        if (empty($value)) {
            $value = [];
        }
        return $value;
    }

    public function inputProcess($value)
    {
        $names = (!empty($value['name']) && is_array($value['name'])) ? $value['name'] : [];
        $values = (!empty($value['value']) && is_array($value['value'])) ? $value['value'] : [];
        $value = [];
        foreach ($names as $k => $v) {
            if (empty($v)) {
                continue;
            }
            $value[] = [
                'name' => $v,
                'value' => empty($values[$k]) ? '' : $values[$k]
            ];
        }

        return ['code' => 0, 'msg' => null, 'data' => $value];
    }
}
