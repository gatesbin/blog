<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use TechOnline\Laravel\Util\TreeUtil;
use Illuminate\Support\Facades\View;
use TechSoft\Laravel\Admin\Cms\Util\CategoryCmsUtil;

class FieldCategory extends BaseField
{
    public $model;
    public $modelId = 'id';
    public $modelPid = 'pid';
    public $modelSort = 'sort';
    public $modelTitle = 'title';

    public function viewHtml(&$data)
    {
        return $this->listHtml($data);
    }


    public function listHtml(&$data)
    {
        $parents = CategoryCmsUtil::loadCategoryWithParents($this->model, $data, $this->modelId, $this->modelPid);
        if (empty($parents)) {
            return '无';
        }
        $cats = [];
        foreach ($parents as &$parent) {
            $cats[] = $parent[$this->modelTitle];
        }
        return join(' &gt; ', $cats);
    }

    public function addHtml()
    {
        $options = TreeUtil::model2Nodes($this->model, ['title' => $this->modelTitle]);
        $options = TreeUtil::listIndent($options, 'id', 'title');

        return View::make('admin::cms.field.category.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'options' => &$options,
            'default' => $this->default,
        ])->render();
    }


    public function editHtml(&$data)
    {
        $options = TreeUtil::model2Nodes($this->model, ['title' => $this->modelTitle]);
        $options = TreeUtil::listIndent($options, 'id', 'title');

        return View::make('admin::cms.field.category.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'options' => &$options,
            'data' => &$data
        ])->render();
    }

}
