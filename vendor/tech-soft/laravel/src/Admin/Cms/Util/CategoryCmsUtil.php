<?php

namespace TechSoft\Laravel\Admin\Cms\Util;

use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Util\TreeUtil;

class CategoryCmsUtil
{

    public static function loadCategoryWithParents($model, $id, $keyId = 'id', $keyPid = 'pid')
    {
        $data = [];
        do {
            $item = ModelUtil::get($model, [$keyId => $id]);
            if (empty($item)) {
                break;
            }
            $data [] = $item;
            $id = $item[$keyPid];
        } while ($id != 0);
        return array_reverse($data);
    }

    public static function loadCategoryChildIds($model, $id, $keyId = 'id', $keyPid = 'pid')
    {
        $ids = [];
        $items = ModelUtil::all($model, [$keyPid => $id]);
        foreach ($items as &$item) {
            $id = $item[$keyId];
            $ids [] = $id;
            $ids = array_merge($ids, self::loadCategoryChildIds($model, $id, $keyId, $keyPid));
        }
        return $ids;
    }

    public static function loadCategoryChildren($model, $id, $keyId = 'id', $keyPid = 'pid')
    {
        return ModelUtil::all($model, [$keyPid => $id]);
    }

    public static function flatCategoryMap($model, $keyId, $keyName)
    {
        return array_build(TreeUtil::model2Nodes($model, [$keyName => $keyName]),
            function ($key, $value) use ($keyId, $keyName) {
                return [$value[$keyId], $value[$keyName]];
            });
    }

}
