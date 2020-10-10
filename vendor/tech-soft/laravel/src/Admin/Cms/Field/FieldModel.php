<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use TechOnline\Laravel\Dao\ModelUtil;
use Illuminate\Support\Facades\View;

class FieldModel extends BaseField
{
    public $model;
    public $modelId = 'id';
    public $modelField = 'title';
    public $modelConnection = null;

    public function viewHtml(&$data)
    {
        return $this->listHtml($data);
    }

    public function listHtml(&$data)
    {
        $model = ModelUtil::model($this->model);
        if ($this->modelConnection) {
            $model->setConnection($this->modelConnection);
        }
        $item = $model->where([$this->modelId => $data])->first();
        if (empty($item)) {
            return '';
        }
        $item = $item->toArray();
        return $item[$this->modelField];
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.model.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }


    public function editHtml(&$data)
    {

        return View::make('admin::cms.field.model.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data
        ])->render();
    }

    public function searchHtml()
    {
        return View::make('admin::cms.field.model.search', [
            'key' => &$this->key,
            'field' => &$this->field,
            'options' => $this->getOptions(),
        ])->render();
    }

    private function getOptions()
    {
        $ms = ModelUtil::all($this->model);
        $options = [];
        foreach ($ms as $m) {
            $options[$m[$this->modelId]] = $m[$this->modelField];
        }
        return $options;
    }

    public function exportValue(&$data)
    {
        $model = ModelUtil::model($this->model);
        if ($this->modelConnection) {
            $model->setConnection($this->modelConnection);
        }
        $item = $model->where([$this->modelId => $data])->first();
        if (empty($item)) {
            return '';
        }
        $item = $item->toArray();
        return $item[$this->modelField];
    }

}
