<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;
use TechSoft\Laravel\Data\DataUtil;
use TechSoft\Laravel\Util\HtmlUtil;

class FieldRichtext extends BaseField
{
    public function addHtml()
    {
        return View::make('admin::cms.field.richtext.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.richtext.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data
        ])->render();
    }

    public function viewHtml(&$data)
    {
        return $data;
    }

    public function listHtml(&$data)
    {
        $summary = HtmlUtil::extractTextAndImages($data);
        return parent::listHtml($summary['text']);
    }

    public function inputProcess($value)
    {
        $value = DataUtil::storeContentTempPath($value);
        return ['code' => 0, 'msg' => null, 'data' => $value];
    }


}
