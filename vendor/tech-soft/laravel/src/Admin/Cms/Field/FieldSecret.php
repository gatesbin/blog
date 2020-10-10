<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use TechOnline\Utils\StrUtil;
use Illuminate\Support\Facades\View;

class FieldSecret extends BaseField
{
    public $mask = false;
    public $length = 32;

    public function viewHtml(&$data)
    {
        if ($this->mask) {
            return StrUtil::mask(htmlspecialchars($data));
        }
        return htmlspecialchars($data);
    }

    public function listHtml(&$data)
    {
        if ($this->mask) {
            return StrUtil::mask(htmlspecialchars($data));
        }
        return htmlspecialchars($data);
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.secret.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'length' => $this->length,
            'default' => $this->default,
        ])->render();
    }

}
