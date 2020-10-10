<?php

namespace TechSoft\Laravel\Admin\Cms;

use TechOnline\Laravel\Dao\DynamicModel;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use TechOnline\Laravel\Util\TreeUtil;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use TechSoft\Laravel\Admin\Util\AdminPowerUtil;
use TechSoft\Laravel\Admin\Util\AdminUtil;

class CategoryCms extends BaseCms
{

    protected function _init()
    {
        $config = [
            'viewAdd' => 'admin::cms.category.add',
            'viewEdit' => 'admin::cms.category.edit',
            'viewList' => 'admin::cms.category.list',
            'viewView' => 'admin::cms.category.view',
            'viewConfigBase' => 'vendor-config.admin.cms.category',
            'actionSort' => '{controller}@{group}Sort',
            'maxLevel' => 0,
            'parentIdKey' => 'pid',
            'sortKey' => 'sort',
            'canSort' => false,
            'singleLevelEdit' => false,
        ];
        $this->defaultConfig = array_merge($this->defaultConfig, $config);
    }


    public function executeAdd(&$controllerContext, $config)
    {
        if (!$config['canAdd']) {
            return Response::send(-1, '不允许增加');
        }

        $config = $this->processConfig($config, BaseCms::TYPE_ADD);

        $_model = new DynamicModel();
        $_model->setTable($config['model']);
        $_primaryKey = $config['primaryKey'];
        $_parentIdKey = $config['parentIdKey'];
        $_sortKey = $config['sortKey'];

        if (Request::isPost()) {

            if (AdminPowerUtil::isDemo()) {
                return AdminPowerUtil::demoResponse();
            }

            $data = [];

            $data[$_parentIdKey] = Input::get($_parentIdKey, 0);
            $data[$_sortKey] = $_model->where([$_parentIdKey => $data[$_parentIdKey]])->max($_sortKey);
            if (empty($data[$_sortKey])) {
                $data[$_sortKey] = 0;
            }
            $data[$_sortKey] = $data[$_sortKey] + 1;

            foreach ($this->runtimeData['fields'] as $key) {
                $field = &$config['fields'][$key];
                $data[$key] = $field['_instance']->inputGet(Input::all());
            }

            if (method_exists($controllerContext, $config['hookPreInputProcess'])) {
                $func = $config['hookPreInputProcess'];
                $controllerContext->$func($data);
            }

            foreach ($this->runtimeData['fields'] as $key) {
                $field = &$config['fields'][$key];
                $ret = $field['_instance']->inputProcess($data[$key]);
                if ($ret['code']) {
                    return $ret;
                }
                $data[$key] = $ret['data'];
            }

            foreach ($this->runtimeData['fields'] as $key) {
                $field = &$config['fields'][$key];
                $data[$key] = $field['_instance']->valueSerialize($data[$key]);
            }

            if (method_exists($controllerContext, $config['hookPreAdd'])) {
                $func = $config['hookPreAdd'];
                $controllerContext->$func($data);
            }

            $data = ModelUtil::insert($config['model'], $data);

            AdminUtil::addInfoLog(AdminPowerUtil::adminUserId(), '增加' . $config['pageTitle'], [
                'ID' => $data[$_primaryKey]
            ]);

            if (method_exists($controllerContext, $config['hookPostAdd'])) {
                $func = $config['hookPostAdd'];
                $controllerContext->$func($data);
            }

            if (method_exists($controllerContext, $config['hookPostChange'])) {
                $func = $config['hookPostChange'];
                $controllerContext->$func('add', $data);
            }

            return Response::send(0, null, null, '[root-reload]');

        }

        return view($config['viewAdd'], [
            'runtimeData' => $this->runtimeData,
            'config' => $config,
            '_pid' => Input::get('_pid', 0),
        ]);
    }

    public function executeEdit(&$controllerContext, $config)
    {
        if (!$config['canEdit']) {
            return Response::send(-1, '不允许编辑');
        }

        $config = $this->processConfig($config, BaseCms::TYPE_EDIT);

        $_id = Input::get('_id', 0);
        $_model = ModelUtil::model($config['model']);
        $_primaryKey = $config['primaryKey'];
        $_parentIdKey = $config['parentIdKey'];
        $_sortKey = $config['sortKey'];

        $model = $_model->where([$_primaryKey => $_id])->first();
        if (empty($model)) {
            return Response::send(-1, 'record not found');
        }

        if (Request::isPost()) {

            if (AdminPowerUtil::isDemo()) {
                return AdminPowerUtil::demoResponse();
            }

            $data = [];

            foreach ($this->runtimeData['fields'] as $key) {
                $field = &$config['fields'][$key];
                $data[$key] = $field['_instance']->inputGet(Input::all());
            }

            if (method_exists($controllerContext, $config['hookPreInputProcess'])) {
                $func = $config['hookPreInputProcess'];
                $controllerContext->$func($data);
            }

            foreach ($this->runtimeData['fields'] as $key) {
                $field = &$config['fields'][$key];
                $ret = $field['_instance']->inputProcess($data[$key]);
                if ($ret['code']) {
                    return $ret;
                }
                $data[$key] = $ret['data'];
            }

            foreach ($this->runtimeData['fields'] as $key) {
                $field = &$config['fields'][$key];
                $data[$key] = $field['_instance']->valueSerialize($data[$key]);
            }

            if (method_exists($controllerContext, $config['hookPreEdit'])) {
                $func = $config['hookPreEdit'];
                $controllerContext->$func($data);
            }
            $old = $model->toArray();

            ModelUtil::update($config['model'], [$_primaryKey => $_id], $data);
            $new = $data;
            if (isset($old['updated_at'])) {
                unset($old['updated_at']);
            }
            if (isset($new['updated_at'])) {
                unset($new['updated_at']);
            }

            AdminUtil::addInfoLogIfChanged(AdminPowerUtil::adminUserId(), '修改' . $config['pageTitle'] . '(ID:' . $model->$_primaryKey . ')', $old, $new);

            if (method_exists($controllerContext, $config['hookPostEdit'])) {
                $func = $config['hookPostEdit'];
                $controllerContext->$func($data);
            }

            if (method_exists($controllerContext, $config['hookPostChange'])) {
                $func = $config['hookPostChange'];
                $controllerContext->$func('edit', $data);
            }

            return Response::send(0, null, null, '[root-reload]');

        }

        return view($config['viewEdit'], [
            'runtimeData' => $this->runtimeData,
            'config' => $config,
            '_id' => $_id,
            'data' => $model->toArray()
        ]);

    }

    public function executeView(&$controllerContext, $config)
    {
        if (!$config['canView']) {
            return Response::send(-1, '不允许查看');
        }

        $config = $this->processConfig($config, BaseCms::TYPE_VIEW);

        $_id = Input::get('_id', 0);
        $_model = ModelUtil::model($config['model']);
        $_primaryKey = $config['primaryKey'];
        $_parentIdKey = $config['parentIdKey'];
        $_sortKey = $config['sortKey'];

        $model = $_model->where([$_primaryKey => $_id])->first();
        if (empty($model)) {
            return Response::send(-1, 'record not found');
        }

        $hookProcessViewField = method_exists($controllerContext, $config['hookProcessViewField']);

        $record = $model->toArray();
        $data = [];
        $data[$_sortKey] = $record[$_sortKey];
        foreach ($this->runtimeData['fields'] as $key) {
            $field = &$config['fields'][$key];
            if (array_key_exists($key, $record)) {
                $data[$key] = $field['_instance']->valueUnserialize($record[$key]);
                $data[$key] = $field['_instance']->viewHtml($data[$key]);
            } else {
                if ($hookProcessViewField) {
                    $func = $config['hookProcessViewField'];
                    $data[$key] = $controllerContext->$func($key, $record);
                } else {
                    $data[$key] = '[没找到调用' . $config['hookProcessViewField'] . ']';
                }
            }
        }

        return view($config['viewView'], [
            'runtimeData' => $this->runtimeData,
            'config' => $config,
            '_id' => $_id,
            'data' => $data
        ]);

    }

    private function remove($id, $_model, $_primaryKey, $_parentIdKey, &$controllerContext, &$config)
    {
        $models = $_model->where([$_parentIdKey => $id])->get();
        foreach ($models as &$model) {
            $this->remove($model->$_primaryKey, $_model, $_primaryKey, $_parentIdKey, $controllerContext, $config);
        }
        $m = $_model->where([$_primaryKey => $id])->first();
        if (empty($m)) {
            return;
        }
        $m = $m->toArray();

        if (method_exists($controllerContext, $config['hookPreDelete'])) {
            $func = $config['hookPreDelete'];
            $controllerContext->$func($m);
        }

        $_model->where([$_primaryKey => $id])->delete();

        if (method_exists($controllerContext, $config['hookPostDelete'])) {
            $func = $config['hookPostDelete'];
            $controllerContext->$func($m);
        }

        if (method_exists($controllerContext, $config['hookPostChange'])) {
            $func = $config['hookPostChange'];
            $controllerContext->$func('delete', $m);
        }
    }

    public function executeDelete(&$controllerContext, $config)
    {
        if (!$config['canDelete']) {
            return Response::send(-1, '不允许删除');
        }

        if (AdminPowerUtil::isDemo()) {
            return AdminPowerUtil::demoResponse();
        }

        $config = $this->processConfig($config, BaseCms::TYPE_DELETE);

        $_id = Input::get('_id', 0);
        $_model = ModelUtil::model($config['model']);
        $_primaryKey = $config['primaryKey'];
        $_parentIdKey = $config['parentIdKey'];
        $_sortKey = $config['sortKey'];

        $model = $_model->where([$_primaryKey => $_id])->first();
        if (empty($model)) {
            return Response::send(-1, 'record not found');
        }

        $this->remove($model->$_primaryKey, $_model, $_primaryKey, $_parentIdKey, $controllerContext, $config);

        AdminUtil::addInfoLog(AdminPowerUtil::adminUserId(), '删除' . $config['pageTitle'], $model->toArray());

        return Response::send(0, null, null, '[reload]');

    }

    public function executeList(&$controllerContext, $config)
    {
        $config = $this->processConfig($config, BaseCms::TYPE_LIST);

        $_model = new DynamicModel();
        $_model->setTable($config['model']);
        $_primaryKey = $config['primaryKey'];
        $_parentIdKey = $config['parentIdKey'];
        $_sortKey = $config['sortKey'];
        $_pid = Input::get('_pid', 0);

        $data = [];
        $o = $_model->where([]);
        if ($config['singleLevelEdit']) {
            $o->where([$_parentIdKey => $_pid]);
        }
        $datas = $o->get()->toArray();

        $hookProcessViewField = method_exists($controllerContext, $config['hookProcessViewField']);

        foreach ($datas as &$record) {

            $item = [];
            $item[$_primaryKey] = $record[$_primaryKey];
            $item[$_parentIdKey] = $record[$_parentIdKey];
            $item[$_sortKey] = $record[$_sortKey];

            foreach ($this->runtimeData['fields'] as $key) {
                $field = &$config['fields'][$key];
                if (array_key_exists($key, $record)) {
                    $item[$key] = $field['_instance']->valueUnserialize($record[$key]);
                } else {
                    if ($hookProcessViewField) {
                        $func = $config['hookProcessViewField'];
                        $item[$key] = $controllerContext->$func($key, $record);
                    } else {
                        $item[$key] = '[没找到调用' . $config['hookProcessViewField'] . ']';
                    }
                }
            }
            $data[] = $item;
        }

        $data = TreeUtil::nodeMerge($data, $_pid, $_primaryKey, $_parentIdKey, $_sortKey, 'asc');

        $currentLevel = 0;
        $_pidPid = 0;
        if ($_pid) {
            $findPid = $_pid;
            while ($currentLevel < 100) {
                $p = $_model->where([$_primaryKey => $findPid])->first();
                if (empty($p)) {
                    break;
                }
                $currentLevel++;
                $findPid = $p->$_parentIdKey;
                if ($_pidPid == 0) {
                    $_pidPid = $findPid;
                }
            }
        }

        return view($config['viewList'], [
            'runtimeData' => $this->runtimeData,
            'config' => $config,
            'data' => $data,
            '_pid' => $_pid,
            '_pidPid' => $_pidPid,
            'currentLevel' => $currentLevel,
        ]);
    }

    public function executeSort(&$controllerContext, $config)
    {
        if (AdminPowerUtil::isDemo()) {
            return AdminPowerUtil::demoResponse();
        }

        $config = $this->processConfig($config, BaseCms::TYPE_NONE);

        $_primaryKey = $config['primaryKey'];
        $_parentIdKey = $config['parentIdKey'];
        $_sortKey = $config['sortKey'];

        $_id = Input::get('_id', 0);

        $oneM = ModelUtil::model($config['model'])->where([$_primaryKey => $_id])->first();
        if (empty($oneM)) {
            return Response::send(-1, 'record not found');
        }

        $allM = ModelUtil::model($config['model'])->where([$_parentIdKey => $oneM->$_parentIdKey])->orderBy($_sortKey, 'asc')->get();
        $oldIndex = null;
        foreach ($allM as $index => &$m) {
            $m->setTable($config['model']);
            $m->$_sortKey = $index;
            $m->save();
            if ($m->$_primaryKey == $_id) {
                $oldIndex = $index;
            }
        }

        $direction = Input::get('direction');

        if (null !== $oldIndex) {
            switch ($direction) {
                case 'up':
                    if ($oldIndex > 0) {
                        $oldSort = $allM->get($oldIndex)->$_sortKey;
                        $allM->get($oldIndex)->$_sortKey = $allM->get($oldIndex - 1)->$_sortKey;
                        $allM->get($oldIndex)->save();
                        $allM->get($oldIndex - 1)->$_sortKey = $oldSort;
                        $allM->get($oldIndex - 1)->save();
                    }
                    break;
                case 'down':
                    if ($oldIndex < $allM->count() - 1) {
                        $oldSort = $allM->get($oldIndex)->$_sortKey;
                        $allM->get($oldIndex)->$_sortKey = $allM->get($oldIndex + 1)->$_sortKey;
                        $allM->get($oldIndex)->save();
                        $allM->get($oldIndex + 1)->$_sortKey = $oldSort;
                        $allM->get($oldIndex + 1)->save();
                    }
                    break;
            }
        }

        if (method_exists($controllerContext, $config['hookPostChange'])) {
            $func = $config['hookPostChange'];
            $controllerContext->$func('sort', []);
        }

        AdminUtil::addInfoLog(AdminPowerUtil::adminUserId(), '修改排序' . $config['pageTitle']);

        return redirect(action($config['actionList'], ['_pid' => $oneM->$_parentIdKey]));

    }

}
