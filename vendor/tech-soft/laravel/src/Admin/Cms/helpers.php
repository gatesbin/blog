<?php

function _CmsCacheRegister($tag, $key)
{
    $tagKey = "cms.keys.$tag";
    if (\TechSoft\Laravel\Lock\DBLockUtil::acquire('CmsCache.' . $tagKey)) {
        $tagValues = \TechOnline\Laravel\Cache\CacheUtil::get($tagKey);
        if (!is_array($tagValues)) {
            $tagValues = [];
        }
        $tagValues[$key] = true;
        \TechOnline\Laravel\Cache\CacheUtil::forever($tagKey, $tagValues);
        \TechSoft\Laravel\Lock\DBLockUtil::release('CmsCache.' . $tagKey);
    }
}

function _CmsCacheFlush($tag)
{
    $tagKey = "cms.keys.$tag";
    $tagValues = \TechOnline\Laravel\Cache\CacheUtil::get($tagKey);
    if (!is_array($tagValues)) {
        return;
    }
    foreach ($tagValues as $k => $_) {
        \TechOnline\Laravel\Cache\CacheUtil::forget($k);
    }
}

function CmsCacheClear($tag)
{
    _CmsCacheFlush($tag);
}

function ConfigGet($name, $defaultValue = null)
{
    return \TechSoft\Laravel\Config\ConfigUtil::get($name, $defaultValue);
}

function AssetsFix($path)
{
    return \TechSoft\Laravel\Assets\AssetsUtil::fix($path);
}

function AssetsFixOrDefault($path, $default)
{
    return \TechSoft\Laravel\Assets\AssetsUtil::fixOrDefault($path, $default);
}

function CmsCategoryAll($table, $where = [], $fields = ['*'], $order = ['sort', 'asc'], $cacheSeconds = 3600)
{
    $key = "cms.category.$table.all." . md5(serialize([$where, $fields, $order]));
    return \TechOnline\Laravel\Cache\CacheUtil::remember(
        $key,
        $cacheSeconds,
        function () use ($table, $where, $fields, $order, $key) {
            _CmsCacheRegister($table, $key);
            $all = \TechOnline\Laravel\Dao\ModelUtil::all($table, $where, $fields, $order);
            return $all;
        }
    );
}

function CmsCategoryTree($table, $fieldsMap = ['name'], $keyId = 'id', $keyPid = 'pid', $keySort = 'sort', $cacheSeconds = 3600)
{
    $key = "cms.category.$table.tree." . md5(serialize([$fieldsMap, $keyId, $keyPid, $keySort]));
    return \TechOnline\Laravel\Cache\CacheUtil::remember(
        $key,
        $cacheSeconds,
        function () use ($table, $fieldsMap, $keyId, $keyPid, $keySort, $key) {
            _CmsCacheRegister($table, $key);
            $tree = \TechOnline\Laravel\Util\TreeUtil::model2Nodes($table, $fieldsMap, $keyId, $keyPid, $keySort);
            return $tree;
        }
    );
}

function CmsCategoryChildrenIds($table, $id, $fieldsMap = ['name'], $keyId = 'id', $keyPid = 'pid', $keySort = 'sort', $cacheSeconds = 3600)
{
    $key = "cms.category.$table.childrenIds." . md5(serialize([$id, $fieldsMap, $keyId, $keyPid, $keySort]));
    return \TechOnline\Laravel\Cache\CacheUtil::remember(
        $key,
        $cacheSeconds,
        function () use ($table, $id, $fieldsMap, $keyId, $keyPid, $keySort, $key) {
            _CmsCacheRegister($table, $key);
            $all = \TechOnline\Laravel\Dao\ModelUtil::all($table, [], ['*'], [$keySort, 'asc']);
            $ids = \TechOnline\Laravel\Util\TreeUtil::allChildIds($all, $id, $keyId, $keyPid);
            return $ids;
        }
    );
}

function CmsCategoryGet($table, $where = [], $fields = ['*'], $cacheSeconds = 3600)
{
    $key = "cms.category.$table.get." . md5(serialize([$where, $fields]));
    return \TechOnline\Laravel\Cache\CacheUtil::remember(
        $key,
        $cacheSeconds,
        function () use ($table, $where, $fields, $key) {
            _CmsCacheRegister($table, $key);
            $one = \TechOnline\Laravel\Dao\ModelUtil::get($table, $where, $fields);
            return $one;
        }
    );
}


function CmsBasicAll($table, $where = [], $fields = ['*'], $order = null, $cacheSeconds = 3600)
{
    $key = "cms.basic.$table.all." . md5(serialize([$where, $fields, $order]));
    return \TechOnline\Laravel\Cache\CacheUtil::remember(
        $key,
        $cacheSeconds,
        function () use ($table, $where, $fields, $order, $key) {
            _CmsCacheRegister($table, $key);
            $all = \TechOnline\Laravel\Dao\ModelUtil::all($table, $where, $fields, $order);
            return $all;
        }
    );
}

function CmsBasicLimit($table, $where = [], $fields = ['*'], $order = null, $limit = 10, $cacheSeconds = 3600, $option = [])
{
    $key = "cms.basic.$table.limit." . md5(serialize([$where, $fields, $order, $limit, $option]));
    return \TechOnline\Laravel\Cache\CacheUtil::remember(
        $key,
        $cacheSeconds,
        function () use ($table, $where, $fields, $order, $limit, $key, $option) {
            _CmsCacheRegister($table, $key);
            $m = \TechOnline\Laravel\Dao\ModelUtil::model($table);
            $m = $m->where($where)->limit($limit);
            if ($order !== null) {
                $m = $m->orderBy($order[0], $order[1]);
            }
            if (!empty($option['whereIn'])) {
                $m = $m->whereIn($option['whereIn'][0], $option['whereIn'][1]);
            }
            $limits = $m->get($fields)->toArray();
            return $limits;
        }
    );
}

function CmsBasicGet($table, $where = [], $fields = ['*'], $cacheSeconds = 3600)
{
    $key = "cms.basic.$table.get." . md5(serialize([$where, $fields]));
    return \TechOnline\Laravel\Cache\CacheUtil::remember(
        $key,
        $cacheSeconds,
        function () use ($table, $where, $fields, $key) {
            _CmsCacheRegister($table, $key);
            $one = \TechOnline\Laravel\Dao\ModelUtil::get($table, $where, $fields);
            return $one;
        }
    );
}

function CmsBasicGetField($table, $field, $where = [], $fields = ['*'], $cacheSeconds = 3600)
{
    $one = CmsBasicGet($table, $where, $fields, $cacheSeconds);
    if (empty($one[$field])) {
        return null;
    }
    return $one[$field];
}