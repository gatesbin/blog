<?php

namespace TechOnline\Laravel\Util;


use TechOnline\Laravel\Dao\ModelUtil;

class TreeUtil
{
    static $CHILD_KEY = '_child';

    public static function setChildKey($key)
    {
        self::$CHILD_KEY = $key;
    }

    
    public static function model2Nodes($model, $fieldsMap = [], $keyId = 'id', $keyPid = 'pid', $keySort = 'sort')
    {
        $models = ModelUtil::all($model);
        $nodes = [];
        foreach ($models as &$model) {
            $node = [];
            $node[$keyId] = $model[$keyId];
            $node[$keyPid] = $model[$keyPid];
            $node[$keySort] = $model[$keySort];
            foreach ($fieldsMap as $k => $v) {
                if (is_numeric($k)) {
                    $node[$v] = $model[$v];
                } else {
                    $node[$k] = $model[$v];
                }
            }
            $nodes[] = $node;
        }
        return self::nodeMerge($nodes, 0, $keyId, $keyPid, $keySort);
    }

    public static function model2NodesByParentId($pid, $model, $fieldsMap = [], $keyId = 'id', $keyPid = 'pid', $keySort = 'sort')
    {
        $models = [];

        $m = ModelUtil::get($model, [$keyId => $pid]);
        if (empty($m)) {
            return [];
        }
        $topPid = $m[$keyPid];
        $models[] = $m;

        $ms = ModelUtil::all($model, [$keyPid => $pid]);
        do {
            $parentIds = [];
            foreach ($ms as &$m) {
                $parentIds[] = $m[$keyId];
                $models[] = $m;
            }
            if (empty($parentIds)) {
                $ms = null;
            } else {
                $ms = ModelUtil::model($model)->whereIn($keyPid, $parentIds)->get()->toArray();
            }
        } while (!empty($ms));

        $nodes = [];
        foreach ($models as &$model) {
            $node = [];
            $node[$keyId] = $model[$keyId];
            $node[$keyPid] = $model[$keyPid];
            $node[$keySort] = $model[$keySort];
            foreach ($fieldsMap as $k => $v) {
                $node[$k] = $model[$v];
            }
            $nodes[] = $node;
        }
        return self::nodeMerge($nodes, $topPid, $keyId, $keyPid, $keySort);
    }

        public static function modelNodeDeleteAble($model, $id, $pidKey = 'pid', $where = [])
    {
        return !ModelUtil::exists($model, array_merge($where, [$pidKey => $id]));
    }

    public static function modelNodeChangeAble($model, $id, $fromPid, $toPid, $idKey = 'id', $pidKey = 'pid', $where = [])
    {
        if ($fromPid == $toPid) {
            return true;
        }

        $_toPid = $toPid;

        while ($m = ModelUtil::get($model, array_merge($where, [$idKey => $_toPid]))) {
            if ($m[$idKey] == $id) {
                return false;
            }
            $_toPid = $m[$pidKey];
        }

        return true;
    }

    public static function nodeMerge(&$node, $pid = 0, $pk_name = 'id', $pid_name = 'pid', $sort_name = 'sort', $sort_direction = 'asc')
    {
                if ($sort_name && $sort_direction) {
            self::arraySortByKey($node, $sort_name, $sort_direction);
        }
        $items = [];
        foreach ($node as $v) {
            $items[$v[$pk_name]] = $v;
        }
        $tree = [];
        foreach ($items as $item) {
            
            if (isset($items[$item[$pid_name]])) {
                $items[$item[$pid_name]][self::$CHILD_KEY][] = &$items[$item[$pk_name]];
            } else {
                $tree[] = &$items[$item[$pk_name]];
            }
        }
        return $tree;
    }

    public static function arraySortByKey(&$arr, $key, $order = 'asc|desc')
    {
        usort($arr, function ($a, $b) use ($key, $order) {
            if ($a[$key] == $b[$key]) return 0;
            if ($order == 'desc') {
                return $a[$key] > $b[$key] ? -1 : 1;
            } else {
                return $a[$key] < $b[$key] ? -1 : 1;
            }
        });
    }

    public static function listIndent(&$list, $keyId, $keyTitle, $level = 0)
    {
        $options = array();
        foreach ($list as &$r) {
            $options[] = array('id' => $r[$keyId], 'title' => str_repeat('|---', $level) . htmlspecialchars($r[$keyTitle]));
            if (!empty($r[self::$CHILD_KEY])) {
                $options = array_merge($options, self::listIndent($r[self::$CHILD_KEY], $keyId, $keyTitle, $level + 1));
            }
        }
        return $options;
    }

    public static function listLevel(&$list, $keyId = 'id', $keyTitle = 'title', $keyPid = 'pid', $level = 0)
    {
        $options = array();
        foreach ($list as &$r) {
            $options[] = array('id' => $r[$keyId], 'title' => $r[$keyTitle], 'level' => $level, 'pid' => $r[$keyPid],);
            if (!empty($r[self::$CHILD_KEY])) {
                $options = array_merge($options, self::listLevel($r[self::$CHILD_KEY], $keyId, $keyTitle, $keyPid, $level + 1));
            }
        }
        return $options;
    }

    public static function allChildIds(&$list, $id, $pk_name = 'id', $pid_name = 'pid')
    {
        $ids = [];
        foreach ($list as &$li) {
            if ($li[$pid_name] == $id) {
                $ids[] = $li[$pk_name];
                $childIds = self::allChildIds($list, $li[$pk_name], $pk_name, $pid_name);
                if (!empty($childIds)) {
                    $ids = array_merge($ids, $childIds);
                }
            }
        }
        return $ids;
    }

    public static function chain(&$list, $id, $pk_name = 'id', $pid_name = 'pid')
    {
        $chain = [];
        $limit = 0;
        $found = true;
        while ($found && $limit++ < 999) {
            $found = false;
            foreach ($list as $li) {
                if ($li[$pk_name] == $id) {
                    $found = true;
                    $id = $li[$pid_name];
                    $chain[] = $li;
                    break;
                }
            }
        }
        return array_reverse($chain);
    }

}
