<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;
use TechSoft\Laravel\Assets\AssetsUtil;

class FieldImage extends BaseField
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
            return '<a href="' . AssetsUtil::fix($data, $this->cdn) . '" style="border:1px solid #CCC;display:inline-block;height:42px;width:42px;box-sizing:border-box;border-radius:2px;" data-image-preview><img src="' . AssetsUtil::fix($data, $this->cdn) . '" style="height:40px;width:40px;display:inline-block;" /></a>';
        }
        return '';
    }

    public function listHtml(&$data)
    {
        if ($data) {
            return '<a href="' . AssetsUtil::fix($data, $this->cdn) . '" style="border:1px solid #CCC;display:inline-block;height:42px;width:42px;box-sizing:border-box;border-radius:2px;" data-image-preview><img src="' . AssetsUtil::fix($data, $this->cdn) . '" style="height:40px;width:40px;display:inline-block;" /></a>';
        }
        return '';
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.image.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'server' => &$this->server,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.image.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'server' => &$this->server,
            'data' => &$data,
        ])->render();
    }

}
