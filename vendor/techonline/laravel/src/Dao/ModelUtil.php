<?php

namespace TechOnline\Laravel\Dao;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModelUtil
{


    public static function ddlHasTable($table)
    {
        return Schema::hasTable($table);
    }

    
    public static function model($model)
    {
        $m = new DynamicModel();
        $m->setTable($model);
        return $m;
    }

    public static function insert($model, $data)
    {
        $m = self::model($model);
        foreach ($data as $k => $v) {
            $m->$k = $v;
        }
        $m->save();
        return $m->toArray();
    }

    public static function insertAll($model, $datas)
    {
        foreach ($datas as $i => $data) {
            $datas[$i]['created_at'] = date('Y-m-d H:i:s');
            $datas[$i]['updated_at'] = date('Y-m-d H:i:s');
        }
        DB::table($model)->insert($datas);
    }

    public static function delete($model, $where)
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        return self::model($model)->where($where)->delete();
    }

    public static function deleteOperator($model, $field, $operator, $value)
    {
        self::model($model)->where($field, $operator, $value)->delete();
    }

    public static function deleteIn($model, $values, $field = 'id')
    {
        if (empty($values)) {
            return;
        }
        return self::model($model)->whereIn($field, $values)->delete();
    }

    public static function update($model, $where, $data)
    {
        if (empty($where)) {
            return null;
        }
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        if (empty($data)) {
            return null;
        }
        return self::model($model)->where($where)->update($data);
    }

    public static function first($model, $where, $fields = ['*'], $order = null)
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        if ($order) {
            $m = self::model($model)->where($where)->orderBy($order[0], $order[1])->first($fields);
        } else {
            $m = self::model($model)->where($where)->get($fields);
        }
        if (empty($m)) {
            return null;
        }
        return $m->toArray();
    }

    public static function get($model, $where, $fields = ['*'])
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        $m = self::model($model)->where($where)->first($fields);
        if (empty($m)) {
            return null;
        }
        return $m->toArray();
    }

    public static function getOrCreate($model, $where)
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        $m = self::model($model)->where($where)->first();
        if (empty($m)) {
            self::insert($model, $where);
            $m = self::model($model)->where($where)->first();
        }
        return $m->toArray();
    }

    public static function getWithCache($model, $where)
    {
        static $map = [];
        $flag = serialize(['model' => $model, 'where' => $where]);
        if (!array_key_exists($flag, $map)) {
            $map[$flag] = self::get($model, $where);
        }
        return $map[$flag];
    }

    public static function getWithLock($model, $where, $fields = ['*'])
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        $m = self::model($model)->where($where)->lockForUpdate()->first($fields);
        if (empty($m)) {
            return null;
        }
        return $m->toArray();
    }

    public static function count($model, $where = [])
    {
        if (is_string($where) || is_numeric($where)) {
            $where = ['id' => $where];
        }
        return self::model($model)->where($where)->count();
    }

    public static function exists($model, $where)
    {
        return !!self::get($model, $where);
    }

    public static function all($model, $where = [], $fields = ['*'], $order = null)
    {
        if ($order) {
            return self::model($model)->where($where)->orderBy($order[0], $order[1])->get($fields)->toArray();
        }
        return self::model($model)->where($where)->get($fields)->toArray();
    }

    public static function allMap($model, $where = [], $fields = ['*'], $order = null, $mapId = 'id')
    {
        return array_build(self::all($model, $where, $fields, $order), function ($k, $v) use ($mapId) {
            return [$v[$mapId], $v];
        });
    }

    public static function allIn($model, $field, $in, $fields = ['*'])
    {
        return self::model($model)->whereIn($field, $in)->get($fields)->toArray();
    }

    public static function allInMap($model, $field, $in, $fields = ['*'], $mapId = 'id')
    {
        return array_build(self::allIn($model, $field, $in, $fields), function ($k, $v) use ($mapId) {
            return [$v[$mapId], $v];
        });
    }

    public static function values($model, $field, $where = [])
    {
        $flat = false;
        if (!is_array($field)) {
            $fields = [$field];
            $flat = true;
        } else {
            $fields = $field;
        }
        $ms = self::model($model)->where($where)->get($fields)->toArray();
        if ($flat) {
            return array_map(function ($item) use ($field) {
                return $item[$field];
            }, $ms);
        }
        return $ms;
    }

    public static function valueMap($model, $fieldKey, $fieldValue, $where = [])
    {
        $ms = self::model($model)->where($where)->get([$fieldKey, $fieldValue])->toArray();
        return array_build($ms, function ($k, $v) use ($fieldKey, $fieldValue) {
            return [$v[$fieldKey], $v[$fieldValue]];
        });
    }

    
    public static function relationList($model, $sourceField, $sourceValue, $filter = [], $extraFields = [], $idField = 'id')
    {
        return self::all($model, array_merge([$sourceField => $sourceValue], $filter), array_merge([$idField, $sourceField], $extraFields));
    }

    
    public static function relationAssign($model, $sourceField, $sourceValue, $targetField, $targetValues, $filter = [], $idField = 'id')
    {
        $relations = self::all($model, array_merge([$sourceField => $sourceValue], $filter), [$idField, $targetField]);
        $deletes = [];
        $inserts = [];
        $existsTargetMap = [];
        foreach ($relations as $relation) {
            $existsTargetMap[$relation[$targetField]] = true;
            if (!in_array($relation[$targetField], $targetValues)) {
                $deletes[] = $relation[$idField];
                continue;
            }
        }
        foreach ($targetValues as $targetValue) {
            if (!isset($existsTargetMap[$targetValue])) {
                $inserts[] = array_merge([
                    $sourceField => $sourceValue,
                    $targetField => $targetValue,
                ], $filter);
            }
        }
        $changed = false;
        if (!empty($deletes)) {
            self::deleteIn($model, $deletes, $idField);
            $changed = true;
        }
        if (!empty($inserts)) {
            self::insertAll($model, $inserts);
            $changed = true;
        }
        return $changed;
    }

    public static function sortNext($model, $filter = [], $sortField = 'sort')
    {
        return intval(self::model($model)->where($filter)->max($sortField)) + 1;
    }

    public static function sortMove($model, $id, $direction = 'up|down', $filter = [], $idField = 'id', $sortField = 'sort')
    {
        if (!in_array($direction, ['up', 'down'])) {
            return false;
        }
        $exists = self::all($model, $filter, [$idField, $sortField], [$sortField, 'asc']);
        $existsIndex = -1;
        foreach ($exists as $index => $exist) {
            if ($exist[$idField] == $id) {
                $existsIndex = $index;
                break;
            }
        }
        if ($existsIndex < 0) {
            return false;
        }
        switch ($direction) {
            case 'up':
                if ($existsIndex > 0) {
                    self::update($model, $exists[$existsIndex][$idField], [$sortField => $exists[$existsIndex - 1][$sortField]]);
                    self::update($model, $exists[$existsIndex - 1][$idField], [$sortField => $exists[$existsIndex][$sortField]]);
                    return true;
                }
                break;
            case 'down':
                if ($existsIndex < count($exists) - 1) {
                    self::update($model, $exists[$existsIndex][$idField], [$sortField => $exists[$existsIndex + 1][$sortField]]);
                    self::update($model, $exists[$existsIndex + 1][$idField], [$sortField => $exists[$existsIndex][$sortField]]);
                    return true;
                }
                break;
            case 'top':
                if ($existsIndex > 0) {
                    $number = 2;
                    foreach ($exists as $index => $exist) {
                        if ($index == $existsIndex) {
                            self::update($model, $exist[$idField], [$sortField => 1]);
                        } else {
                            self::update($model, $exist[$idField], [$sortField => $number]);
                            $number++;
                        }
                    }
                    return true;
                }
                break;
            case 'bottom':
                if ($existsIndex < count($exists) - 1) {
                    $number = 1;
                    foreach ($exists as $index => $exist) {
                        if ($index == $existsIndex) {
                            continue;
                        }
                        self::update($model, $exist[$idField], [$sortField => $number]);
                        $number++;
                    }
                    self::update($model, $exists[$existsIndex][$idField], [$sortField => $number]);
                    return true;
                }
                break;
        }
        return false;
    }

    public static function join(&$data, $dataModelKey = 'userId', $dataMergedKey = '_user', $model = 'join_model', $modelPrimaryKey = 'id')
    {
        if (empty($data)) {
            return;
        }

        $ids = array_map(function ($item) use ($dataModelKey) {
            return $item[$dataModelKey];
        }, $data);

        $joinData = self::model($model)->whereIn($modelPrimaryKey, $ids)->get()->toArray();

        $joinDataMap = array_build($joinData, function ($k, $v) use ($modelPrimaryKey) {
            return [$v[$modelPrimaryKey], $v];
        });

        foreach ($data as &$item) {
            $key = $item[$dataModelKey];
            if (isset($joinDataMap[$key])) {
                $item[$dataMergedKey] = $joinDataMap[$key];
            } else {
                $item[$dataMergedKey] = null;
            }
        }
    }


    public static function joinAll(&$data, $dataModelKey = 'userId', $dataMergedKey = '_user', $model = 'join_model', $modelPrimaryKey = 'id')
    {
        if (empty($data)) {
            return;
        }

        $ids = array_map(function ($item) use ($dataModelKey) {
            return $item[$dataModelKey];
        }, $data);

        $joinData = self::model($model)->whereIn($modelPrimaryKey, $ids)->get()->toArray();
        $joinDataMap = [];
        foreach ($joinData as $item) {
            if (array_key_exists($item[$modelPrimaryKey], $joinDataMap)) {
                $joinDataMap[$item[$modelPrimaryKey]][] = $item;
            } else {
                $joinDataMap[$item[$modelPrimaryKey]] = [$item];
            }
        }

        foreach ($data as &$item) {
            $key = $item[$dataModelKey];
            if (isset($joinDataMap[$key])) {
                $item[$dataMergedKey] = $joinDataMap[$key];
            } else {
                $item[$dataMergedKey] = [];
            }
        }
    }

    public static function paginateMergeConditionParam(&$o, $option)
    {

        if (!empty($option['whereIn'])) {
            if (is_array($option['whereIn'][0])) {
                foreach ($option['whereIn'] as &$whereIn) {
                    $o = $o->whereIn($whereIn[0], $whereIn[1]);
                }
            } else {
                $o = $o->whereIn($option['whereIn'][0], $option['whereIn'][1]);
            }
        }

        if (!empty($option['whereOperate'])) {
            if (is_array($option['whereOperate'][0])) {
                foreach ($option['whereOperate'] as &$whereOperate) {
                    $o = $o->where($whereOperate[0], $whereOperate[1], $whereOperate[2]);
                }
            } else {
                $o = $o->where($option['whereOperate'][0], $option['whereOperate'][1], $option['whereOperate'][2]);
            }
        }

        if (!empty($option['where'])) {
            if (is_array($option['where'])) {
                $o = $o->where($option['where']);
            } else {
                $o = $o->whereRaw($option['where']);
            }
        }

        
        if (!empty($option['search']) && is_array($option['search'])) {
            foreach ($option['search'] as $searchItem) {

                if (!isset($searchItem['__exp'])) {
                    $searchItem['__exp'] = 'and';
                } else {
                    $searchItem['__exp'] = strtolower($searchItem['__exp']);
                }

                $whereExpFirst = true;
                $whereExp = 'where';
                if ($searchItem['__exp'] == 'or') {
                    $whereExp = 'orWhere';
                }

                $o = $o->where(function ($queryBase) use (&$searchItem, $whereExpFirst, $whereExp) {

                    foreach ($searchItem as $field => $searchInfo) {
                        if (in_array($field, ['__exp'])) {
                            continue;
                        }
                        if (!isset($searchInfo['exp'])) {
                            $searchInfo['exp'] = 'and';
                        }
                        $searchInfo['exp'] = strtolower($searchInfo['exp']);

                        if ($whereExpFirst) {
                            $where = 'where';
                            $whereExpFirst = false;
                        } else {
                            $where = $whereExp;
                        }

                        $queryBase = $queryBase->$where(function ($query) use (&$field, &$searchInfo) {
                            $first = true;
                            foreach ($searchInfo as $k => $v) {
                                switch ($k) {
                                    case 'like':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, 'like', '%' . $v . '%');
                                        } else {
                                            $query->orWhere($field, 'like', '%' . $v . '%');
                                        }
                                        break;
                                    case 'leftLike':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, 'like', $v . '%');
                                        } else {
                                            $query->orWhere($field, 'like', $v . '%');
                                        }
                                        break;
                                    case 'rightLike':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, 'like', '%' . $v);
                                        } else {
                                            $query->orWhere($field, 'like', '%' . $v);
                                        }
                                        break;
                                    case 'equal':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, '=', $v);
                                        } else {
                                            $query->orWhere($field, '=', $v);
                                        }
                                        break;
                                    case 'min':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, '>=', $v);
                                        } else {
                                            $query->orWhere($field, '>=', $v);
                                        }
                                        break;
                                    case 'max':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, '<=', $v);
                                        } else {
                                            $query->orWhere($field, '<=', $v);
                                        }
                                        break;
                                    case 'eq':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->where($field, '=', $v);
                                        } else {
                                            $query->orWhere($field, '=', $v);
                                        }
                                        break;
                                    case 'in':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->whereIn($field, $v);
                                        } else {
                                            $query->whereIn($field, $v, 'or');
                                        }
                                        break;
                                    case 'is':
                                        if (null === $v) {
                                            if ($first || $searchInfo['exp'] == 'and') {
                                                $first = false;
                                                $query->whereNull($field);
                                            } else {
                                                $query->orWhereNull($field);
                                            }
                                        } else {
                                            exit('TODO');
                                        }
                                        break;
                                    case 'raw':
                                        if ($first || $searchInfo['exp'] == 'and') {
                                            $first = false;
                                            $query->whereRaw($v);
                                        } else {
                                            $query->orWhereRaw($v);
                                        }
                                        break;
                                }
                            }
                        });

                    }

                });
            }
        }

        
        if (!empty($option['filter']) && is_array($option['filter'])) {
            $o = $o->where(function ($queryBase) use (&$option) {
                foreach ($option['filter'] as $oneFilter) {
                    switch ($oneFilter['condition']) {
                        case 'is':
                            $queryBase = $queryBase->where([$oneFilter['field'] => $oneFilter['value']]);
                            break;
                        case 'is_not':
                            $queryBase = $queryBase->where($oneFilter['field'], '<>', $oneFilter['value']);
                            break;
                        case 'contains':
                            $queryBase = $queryBase->where($oneFilter['field'], 'like', '%' . $oneFilter['value'] . '%');
                            break;
                        case 'not_contains':
                            $queryBase = $queryBase->where($oneFilter['field'], 'not like', '%' . $oneFilter['value'] . '%');
                            break;
                        case 'range':
                            if (!empty($oneFilter['value'][0])) {
                                $queryBase = $queryBase->where($oneFilter['field'], '>=', $oneFilter['value'][0]);
                            }
                            if (!empty($oneFilter['value'][1])) {
                                $queryBase = $queryBase->where($oneFilter['field'], '<=', $oneFilter['value'][1]);
                            }
                            break;
                        case 'is_empty':
                            $queryBase = $queryBase->where($oneFilter['field'], '=', '');
                            break;
                        case 'is_not_empty':
                            $queryBase = $queryBase->where($oneFilter['field'], '<>', '');
                            break;
                        case 'gt':
                            $queryBase = $queryBase->where($oneFilter['field'], '>', $oneFilter['value']);
                            break;
                        case 'egt':
                            $queryBase = $queryBase->where($oneFilter['field'], '>=', $oneFilter['value']);
                            break;
                        case 'lt':
                            $queryBase = $queryBase->where($oneFilter['field'], '<', $oneFilter['value']);
                            break;
                        case 'elt':
                            $queryBase = $queryBase->where($oneFilter['field'], '<=', $oneFilter['value']);
                            break;
                    }
                }
            });
        }
    }


    public static function paginate($model, $page, $pageSize, $option = [])
    {
        $m = self::model($model);

        if (!empty($option['joins'])) {
            $select = [];
            $select[] = $model . '.*';
            foreach ($option['joins'] as $join) {
                if (!empty($join['table']) && !empty($join['fields'])) {
                    $m = $m->leftJoin($join['table'][0], $join['table'][1], $join['table'][2], $join['table'][3]);
                    foreach ($join['fields'] as $fieldAlias => $fieldTable) {
                        array_push($select, "$fieldTable as $fieldAlias");
                    }
                }
            }
            $m = call_user_func_array(array($m, 'select'), $select);
        }

        self::paginateMergeConditionParam($m, $option);

        if (!empty($option['order'])) {
            if (is_array($option['order'][0])) {
                foreach ($option['order'] as &$order) {
                    $m = $m->orderBy($order[0], $order[1]);
                }
            } else {
                $m = $m->orderBy($option['order'][0], $option['order'][1]);
            }
        }

        if (!empty($option['fields'])) {
            $m = $m->select($option['fields']);
        }

        $m = $m->paginate($pageSize, ['*'], 'page', $page)->toArray();

        return [
            'total' => $m['total'],
            'records' => $m['data']
        ];
    }


    public static function transactionBegin()
    {
        DB::beginTransaction();
    }

    public static function transactionRollback()
    {
        DB::rollback();
    }

    public static function transactionCommit()
    {
        DB::commit();
    }

    public static function isFieldUniqieForInsertOrUpdate($model, $id, $field, $value)
    {
        $exists = self::all($model, [$field => $value]);
        if (empty($exists)) {
            return true;
        }
        if (count($exists) == 1 && $id > 0 && $id == $exists[0]['id']) {
            return true;
        }
        return false;
    }

    public static function replaceConditionParamField(&$option, $fieldMap = [])
    {
        if (empty($fieldMap)) {
            return;
        }
        if (!empty($option['search']) && is_array($option['search'])) {
            foreach ($option['search'] as &$searchItem) {
                foreach ($searchItem as $field => $searchInfo) {
                    if (array_key_exists($field, $fieldMap)) {
                        unset($searchItem[$field]);
                        $searchItem[$fieldMap[$field]] = $searchInfo;
                    }
                }
            }
        }

        if (!empty($option['whereIn'])) {
            if (is_array($option['whereIn'][0])) {
                foreach ($option['whereIn'] as &$whereIn) {
                    if (array_key_exists($whereIn[0], $fieldMap)) {
                        $whereIn[0] = $fieldMap[$whereIn[0]];
                    }
                }
            } else {
                if (array_key_exists($option['whereIn'][0], $fieldMap)) {
                    $option['whereIn'][0] = $fieldMap[$option['whereIn'][0]];
                }
            }
        }

        if (!empty($option['whereOperate'])) {
            if (is_array($option['whereOperate'][0])) {
                foreach ($option['whereOperate'] as &$whereOperate) {
                    if (array_key_exists($whereOperate[0], $fieldMap)) {
                        $whereOperate[0] = $fieldMap[$whereOperate[0]];
                    }
                }
            } else {
                if (array_key_exists($option['whereOperate'][0], $fieldMap)) {
                    $option['whereOperate'][0] = $fieldMap[$option['whereOperate'][0]];
                }
            }
        }

        if (!empty($option['where'])) {
            foreach ($option['where'] as $k => $item) {
                if (array_key_exists($k, $fieldMap)) {
                    unset($option['where'][$k]);
                    $option['where'][$fieldMap[$k]] = $item;
                }
            }
        }
    }

    public static function decodeRecordBoolean(&$record, $keyArray)
    {
        if (empty($record)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($keyArray as $key) {
            $record[$key] = $record[$key] ? true : false;
        }
    }

    public static function decodeRecordsBoolean(&$records, $keyArray)
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                $record[$key] = $record[$key] ? true : false;
            }
        }
    }

    public static function decodeRecordJson(&$record, $keyArray, $default = [])
    {
        if (empty($record)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($keyArray as $key) {
            $record[$key] = @json_decode($record[$key], true);
            if (empty($record[$key])) {
                $record[$key] = $default;
            }
        }
    }

    public static function decodeRecordsJson(&$records, $keyArray, $default = [])
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                $record[$key] = @json_decode($record[$key], true);
                if (empty($record[$key])) {
                    $record[$key] = $default;
                }
            }
        }
    }

    public static function encodeRecordJson(&$record, $keyArray, $default = [])
    {
        if (empty($record)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($keyArray as $key) {
            if (empty($record[$key])) {
                $record[$key] = $default;
            }
            $record[$key] = @json_encode($record[$key]);
        }
    }

    public static function encodeRecordsJson(&$records, $keyArray, $default = [])
    {
        if (empty($records)) {
            return;
        }
        if (is_string($keyArray)) {
            $keyArray = [$keyArray];
        }
        foreach ($records as &$record) {
            foreach ($keyArray as $key) {
                if (empty($record[$key])) {
                    $record[$key] = $default;
                }
                $record[$key] = @json_encode($record[$key]);
            }
        }
    }






    public static function sum($model, $field, $where = [])
    {
        return self::model($model)->where($where)->sum($field);
    }




}
