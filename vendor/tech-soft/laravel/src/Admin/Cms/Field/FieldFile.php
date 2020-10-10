<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use TechOnline\Utils\FileUtil;
use Illuminate\Support\Facades\View;
use TechSoft\Laravel\Assets\AssetsUtil;

class FieldFile extends BaseField
{
    public $server = null;
    public $cdn = '';
    public $category = 'file';

    public function __construct(&$context)
    {
        parent::__construct($context);
    }


    public function viewHtml(&$data)
    {
        if ($data) {
            return '<a href="' . AssetsUtil::fix($data, $this->cdn) . '" target="_blank" data-uk-tooltip title="' . FileUtil::name($data) . '">' . strtoupper(FileUtil::extension($data)) . '文件</a>';
        }
        return '';
    }

    public function listHtml(&$data)
    {
        if ($data) {
            return '<a href="' . AssetsUtil::fix($data, $this->cdn) . '" target="_blank" data-uk-tooltip title="' . FileUtil::name($data) . '">' . strtoupper(FileUtil::extension($data)) . '文件</a>';
        }
        return '';
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.file.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'server' => &$this->server,
            'default' => $this->default,
            'category' => $this->category,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.file.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'server' => &$this->server,
            'data' => &$data,
            'category' => $this->category,
        ])->render();
    }

}
