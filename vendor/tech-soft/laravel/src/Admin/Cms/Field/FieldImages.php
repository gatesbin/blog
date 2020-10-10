<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use Illuminate\Support\Facades\View;
use TechSoft\Laravel\Assets\AssetsUtil;

class FieldImages extends BaseField
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
            foreach ($data as $image) {
                $html[] = '<a href="' . AssetsUtil::fix($image, $this->cdn) . '" style="border:1px solid #CCC;display:inline-block;height:42px;width:42px;box-sizing:border-box;border-radius:2px;" data-image-preview><img src="' . AssetsUtil::fix($image, $this->cdn) . '" style="height:40px;width:40px;display:inline-block;" /></a>';
            }
            return join(' ', $html);
        }
        return '';
    }

    public function listHtml(&$data)
    {
        if ($data) {
            $html = [];
            foreach ($data as $image) {
                $html[] = '<a href="' . AssetsUtil::fix($image, $this->cdn) . '" style="border:1px solid #CCC;display:inline-block;height:42px;width:42px;box-sizing:border-box;border-radius:2px;" data-image-preview><img src="' . AssetsUtil::fix($image, $this->cdn) . '" style="height:40px;width:40px;display:inline-block;" /></a>';
            }
            return join(' ', $html);
        }
        return '';
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.images.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'server' => &$this->server,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.images.edit', [
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
        $value = @json_decode($value, true);
        if (empty($value)) {
            $value = [];
        }
        return ['code' => 0, 'msg' => null, 'data' => $value];
    }

}
