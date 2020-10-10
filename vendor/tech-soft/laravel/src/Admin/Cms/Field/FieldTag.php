<?php

namespace TechSoft\Laravel\Admin\Cms\Field;

use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Util\TagUtil;
use Illuminate\Support\Facades\View;

class FieldTag extends BaseField
{
    public $map = [];
    public $mapModel = null;
    public $mapModelField = 'title';
    public $mapModelId = 'id';

    private function parseTags($data)
    {
        $tags = $data;
        if (empty($tags)) {
            return $tags;
        }
        if (null != $this->mapModel) {
            $tagModels = ModelUtil::allIn($this->mapModel, $this->mapModelId, $tags);
            foreach ($tagModels as $tagModel) {
                foreach ($tags as &$tag) {
                    if ($tag == $tagModel[$this->mapModelId]) {
                        $tag = $tagModel[$this->mapModelField];
                    }
                }
            }
        }
        if (!empty($this->map)) {
            foreach ($tags as &$tag) {
                if (array_key_exists($tag, $this->map)) {
                    $tag = $this->map[$tag];
                }
            }
        }
        return $tags;
    }

    public function viewHtml(&$data)
    {
        if ($data) {
            $tags = $this->parseTags($data);
            foreach ($tags as &$tag) {
                $tag = '<span class="uk-badge uk-badge-success">' . $tag . '</span>';
            }
            return join(' ', $tags);
        }
        return '';
    }

    public function listHtml(&$data)
    {
        if ($data) {
            $tags = $this->parseTags($data);
            foreach ($tags as &$tag) {
                $tag = '<span class="uk-badge uk-badge-success">' . $tag . '</span>';
            }
            return join(' ', $tags);
        }
        return '';
    }

    public function addHtml()
    {
        return View::make('admin::cms.field.tag.add', [
            'key' => &$this->key,
            'field' => &$this->field,
            'default' => $this->default,
        ])->render();
    }

    public function editHtml(&$data)
    {
        return View::make('admin::cms.field.tag.edit', [
            'key' => &$this->key,
            'field' => &$this->field,
            'data' => &$data,
        ])->render();
    }

    public function valueSerialize($value)
    {
        return TagUtil::array2String($value);
    }

    public function valueUnserialize($value)
    {
        return TagUtil::string2Array($value);
    }

    public function inputProcess($value)
    {
        $value = TagUtil::string2Array($value);
        if (empty($value)) {
            $value = [];
        }
        return ['code' => 0, 'msg' => null, 'data' => $value];
    }
}
