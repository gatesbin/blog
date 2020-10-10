<?php

namespace TechSoft\Laravel\Admin\Cms;

use TechOnline\Laravel\Dao\DynamicModel;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use TechSoft\Laravel\Admin\Util\AdminPowerUtil;
use TechSoft\Laravel\Admin\Util\AdminUtil;

class BasicCms extends BaseCms
{
    const ACTION_QUICK_EDIT = 'actionQuickEdit';

    protected function _init()
    {
        $config = [
            'viewLayout' => 'admin::frame',
            'viewAdd' => 'admin::cms.basic.add',
            'viewEdit' => 'admin::cms.basic.edit',
            'viewList' => 'admin::cms.basic.list',
            'viewView' => 'admin::cms.basic.view',
            'viewImport' => 'admin::cms.basic.import',
            'viewConfigBase' => 'vendor-config.admin.cms.basic',
            'pageSize' => 10,
            
            'listFilter' => [],
            'order' => []
        ];
        $this->defaultConfig = array_merge($this->defaultConfig, $config);
    }


    public function executeList(&$controllerContext, $config)
    {
        $config = $this->processConfig($config, BaseCms::TYPE_LIST);

        $_model = new DynamicModel();
        $_model->setTable($config['model']);
        if (null != $config['model_connection']) {
            $_model->setConnection($config['model_connection']);
        }
        $_primaryKey = $config['primaryKey'];

        if (Request::isMethod('post')) {

            $_page = Input::get('page', 1);
            $_pageSize = $config['pageSize'];
            $_option = [];
            $_option['search'] = Input::get('search', []);

            if (!empty($config['joins'])) {
                $conditionFieldMap = [];
                $select = [];
                $select[] = $config['model'] . '.*';
                if (is_array($config['joins'][0])) {
                    foreach ($config['joins'] as $join) {
                        $_model = $_model->leftJoin($join[0], $join[1], $join[2], $join[3]);
                        foreach ($join[4] as $aliasField => $tableField) {
                            $conditionFieldMap[$aliasField] = $tableField;
                            array_push($select, "$tableField as $aliasField");
                        }
                    }
                } else {
                    $join = $config['joins'];
                    $_model = $_model->leftJoin($join[0], $join[1], $join[2], $join[3]);
                    foreach ($join[4] as $aliasField => $tableField) {
                        $conditionFieldMap[$aliasField] = $tableField;
                        array_push($select, "$tableField as $aliasField");
                    }
                }
                $_model = call_user_func_array(array($_model, 'select'), $select);
                ModelUtil::replaceConditionParamField($_option, $conditionFieldMap);
            }

            if (!empty($config['listFilter'])) {
                ModelUtil::paginateMergeConditionParam($_model, $config['listFilter']);
            }
            ModelUtil::paginateMergeConditionParam($_model, $_option);

            if (empty($config['order'])) {
                $_model = $_model->orderBy($_primaryKey, 'desc');
            } else {
                if (is_array($config['order'][0])) {
                    foreach ($config['order'] as $orderInfo) {
                        $_model = $_model->orderBy($orderInfo[0], $orderInfo[1]);
                    }
                } else {
                    $_model = $_model->orderBy($config['order'][0], $config['order'][1]);
                }
            }

            $paginateData = $_model->paginate($_pageSize, ['*'], 'page', $_page)->toArray();

            $hookProcessView = method_exists($controllerContext, $config['hookProcessView']);
            $hookProcessViewField = method_exists($controllerContext, $config['hookProcessViewField']);

            $operationViewExists = view()->exists($config['viewConfigBase'] . '.operation');

            $list = [];
            foreach ($paginateData['data'] as &$record) {
                $item = [];
                $item['_id'] = $record[$_primaryKey];
                if ($config['batchOperate']) {
                    $item['_checkbox'] = '<input type="checkbox" data-cms-checkbox-item="' . $item['_id'] . '" />';
                }
                if ($config['primaryKeyShow']) {
                    $item[$_primaryKey] = $record[$_primaryKey];
                }
                foreach ($this->runtimeData['fields'] as $key) {
                    $field = &$config['fields'][$key];
                    if (array_key_exists($key, $record)) {
                        $item[$key] = $field['_instance']->valueUnserialize($record[$key]);
                        $item[$key] = $field['_instance']->listHtml($item[$key]);
                    } else {
                        if ($hookProcessViewField) {
                            $func = $config['hookProcessViewField'];
                            $item[$key] = $controllerContext->$func($key, $record);
                        } else {
                            $item[$key] = '[hookProcessViewField=' . $config['hookProcessViewField'] . '($key, &$record) not found]';
                        }
                    }
                }

                $item['_operation'] = '';
                $viewAction = '';
                if ($config['canView']) {
                    $item['_operation'] .= ' {actionView} ';
                    $viewAction = action($config['actionView']) . '?_id=' . $item['_id'];
                }
                $editAction = '';
                if ($config['canEdit']) {
                    $item['_operation'] .= ' {actionEdit} ';
                    $editAction = action($config['actionEdit']) . '?_id=' . $item['_id'];
                }
                if ($config['canDelete']) {
                    $item['_operation'] .= ' {actionDelete} ';
                }

                if ($operationViewExists) {
                    $item['_operation'] = View::make($config['viewConfigBase'] . '.operation', [
                        'item' => $item,
                        'record' => $record
                    ])->render();
                }

                if ($config['editInNewWindow']) {
                    $editActionHtml = '<a href="' . $editAction . '" class="action-btn" data-uk-tooltip title="修改"><i class="uk-icon-edit"></i></a>';
                } else {
                    $editActionHtml = '<a href="javascript:;" class="action-btn h-edit" data-uk-tooltip title="修改"><i class="uk-icon-edit"></i></a>';
                }


                if ($config['viewInNewWindow']) {
                    $viewActionHtml = '<a href="' . $viewAction . '" class="action-btn" data-uk-tooltip title="查看"><i class="uk-icon-eye"></i></a>';
                } else {
                    $viewActionHtml = '<a href="javascript:;" class="action-btn h-view" data-uk-tooltip title="查看"><i class="uk-icon-eye"></i></a>';
                }

                $item['_operation'] = str_replace([
                    '{actionView}',
                    '{actionEdit}',
                    '{actionDelete}'
                ], [
                    $viewActionHtml,
                    $editActionHtml,
                    '<a href="javascript:;" class="action-btn h-delete" data-uk-tooltip title="删除"><i class="uk-icon-trash"></i></a>'
                ], $item['_operation']);

                if ($hookProcessView) {
                    $func = $config['hookProcessView'];
                    $controllerContext->$func($item, $record);
                }

                $list[] = $item;
            }

            $head = [];
            if ($config['batchOperate']) {
                $head[] = ['field' => '_checkbox', 'title' => '<input type="checkbox" data-cms-checkbox-all />',];
            }
            if ($config['primaryKeyShow']) {
                $head[] = ['field' => $_primaryKey, 'title' => 'ID'];
            }
            foreach ($this->runtimeData['fields'] as $key) {
                $field = &$config['fields'][$key];
                $head[] = ['field' => $key, 'title' => $field['title']];
            }
            foreach ($list as &$item) {
                if (!empty($item['_operation'])) {
                    $head[] = ['field' => '_operation', 'title' => ''];
                    break;
                }
            }

            $data = [];
            $data['page'] = $paginateData['current_page'];
            $data['pageSize'] = $_pageSize;
            $data['total'] = $paginateData['total'];
            $data['head'] = $head;
            $data['list'] = $list;

            return Response::json(0, 'ok', $data);
        }
        return view($config['viewList'], ['runtimeData' => $this->runtimeData, 'config' => $config,]);
    }

    public function executeView(&$controllerContext, $config)
    {
        if (!$config['canView']) {
            return Response::send(-1, '不允许');
        }

        $config = $this->processConfig($config, self::TYPE_VIEW);

        $_id = Input::get('_id', 0);
        $_model = new DynamicModel();
        $_model->setTable($config['model']);
        $_primaryKey = $config['primaryKey'];

        $model = $_model->where([$_primaryKey => $_id])->first();
        if (empty($model)) {
            return Response::send(-1, 'record not found');
        }

        $record = $model->toArray();

        $hookProcessViewField = method_exists($controllerContext, $config['hookProcessViewField']);

        if (method_exists($controllerContext, $config['hookPostRead'])) {
            $func = $config['hookPostRead'];
            $controllerContext->$func($record);
        }

        $data = [];
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
                    $data[$key] = '[hookProcessViewField=' . $config['hookProcessViewField'] . '($key, &$record) not found]';
                }
            }
        }

        $hookProcessView = method_exists($controllerContext, $config['hookProcessView']);
        if ($hookProcessView) {
            $func = $config['hookProcessView'];
            $controllerContext->$func($data, $record);
        }

        return view($config['viewView'], [
            'runtimeData' => $this->runtimeData,
            'config' => $config,
            '_id' => $_id,
            'data' => $data
        ]);

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

        $_id = Input::get('_id');

        if (!is_array($_id)) {
            $ids = explode(',', $_id);
            $_id = [];
            foreach ($ids as $id) {
                if (empty($id)) {
                    continue;
                }
                $_id[] = $id;
            }
        }

        if (empty($_id)) {
            return Response::send(-1, '_id empty');
        }

        $_model = new DynamicModel();
        $_model->setTable($config['model']);

        $_primaryKey = $config['primaryKey'];

        $ms = $_model->whereIn($_primaryKey, $_id)->get()->toArray();

        foreach ($ms as &$m) {

            if (method_exists($controllerContext, $config['hookBeforeDeleteCheck'])) {
                $func = $config['hookBeforeDeleteCheck'];
                $ret = $controllerContext->$func($m);
                if ($ret['code']) {
                    return Response::send(-1, $ret['msg']);
                }
            }

            if (method_exists($controllerContext, $config['hookPreDelete'])) {
                $func = $config['hookPreDelete'];
                $controllerContext->$func($m);
            }

            $_model->where([$_primaryKey => $m['id']])->delete();

            AdminUtil::addInfoLog(AdminPowerUtil::adminUserId(), '删除' . $config['pageTitle'], $m);

            if (method_exists($controllerContext, $config['hookPostDelete'])) {
                $func = $config['hookPostDelete'];
                $controllerContext->$func($m);
            }
        }

        return Response::send(0, 'ok');

    }

    public function executeEdit(&$controllerContext, $config)
    {
        $config = $this->processConfig($config, BaseCms::TYPE_EDIT);

        if (!$config['canEdit']) {
            return Response::send(-1, '不允许编辑');
        }

        $_model = new DynamicModel();
        $_model->setTable($config['model']);
        $_primaryKey = $config['primaryKey'];

        $_id = Input::get('_id', 0);

        if (!empty($config['joins'])) {
            $conditionFieldMap = [];
            $select = [];
            $select[] = $config['model'] . '.*';
            if (is_array($config['joins'][0])) {
                foreach ($config['joins'] as $join) {
                    $_model = $_model->leftJoin($join[0], $join[1], $join[2], $join[3]);
                    foreach ($join[4] as $aliasField => $tableField) {
                        $conditionFieldMap[$aliasField] = $tableField;
                        array_push($select, "$tableField as $aliasField");
                    }
                }
            } else {
                $join = $config['joins'];
                $_model = $_model->leftJoin($join[0], $join[1], $join[2], $join[3]);
                foreach ($join[4] as $aliasField => $tableField) {
                    $conditionFieldMap[$aliasField] = $tableField;
                    array_push($select, "$tableField as $aliasField");
                }
            }
            $_model = call_user_func_array(array($_model, 'select'), $select);
            ModelUtil::replaceConditionParamField($_option, $conditionFieldMap);
        }

        $model = $_model->where([$config['model'] . '.' . $_primaryKey => $_id])->first();
        if (empty($model)) {
            return Response::send(-1, 'record not found');
        }

        $action = Input::get('_action', null);
        if ($action) {

            if (AdminPowerUtil::isDemo()) {
                return AdminPowerUtil::demoResponse();
            }

            $data = Input::all();
            if (empty($_id)) {
                return Response::send(-1, '_id empty');
            }
            unset($data['_id'], $data['_action']);
            if (empty($data)) {
                return Response::send(-1, 'quick edit data empty');
            }
            switch ($action) {
                case self::ACTION_QUICK_EDIT:
                    foreach ($data as $k => $v) {
                        $model->$k = $v;
                    }
                    $model->save();
                    return Response::send(0, null, null, '[js]window.__cms.action.refresh();');
            }
            return Response::send(-1, 'error action');
        }

        if (Request::isMethod('post')) {

            if (Session::get('_adminUserId', null) && env('ADMIN_DEMO_USER_ID', 0) && Session::get('_adminUserId', null) == env('ADMIN_DEMO_USER_ID', 0)) {
                return Response::send(-1, '演示账号禁止该操作');
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
                $data = $field['_instance']->valuesSerialize($data);
            }

            if (method_exists($controllerContext, $config['hookBeforeEditResolve'])) {
                $func = $config['hookBeforeEditResolve'];
                $controllerContext->$func($data);
            }

            if (method_exists($controllerContext, $config['hookBeforeEditCheck'])) {
                $func = $config['hookBeforeEditCheck'];
                $data['id'] = $model->$_primaryKey;
                $ret = $controllerContext->$func($data);
                if ($ret['code']) {
                    return Response::send(-1, $ret['msg']);
                }
            }

            if (method_exists($controllerContext, $config['hookPreEdit'])) {
                $func = $config['hookPreEdit'];
                $controllerContext->$func($data);
            }

            $old = $model->toArray();

            $new = $old;
            foreach ($data as $k => $v) {
                $new[$k] = $v;
            }
            ModelUtil::update($config['model'], [$_primaryKey => $old[$_primaryKey]], $new);

            if (isset($old['updated_at'])) {
                unset($old['updated_at']);
            }
            if (isset($new['updated_at'])) {
                unset($new['updated_at']);
            }

            AdminUtil::addInfoLogIfChanged(AdminPowerUtil::adminUserId(), '修改' . $config['pageTitle'] . '(ID:' . $model->$_primaryKey . ')', $old, $new);

            if (method_exists($controllerContext, $config['hookPostEdit'])) {
                $hookPostEdit = $config['hookPostEdit'];
                $controllerContext->$hookPostEdit($data);
            }

            if (method_exists($controllerContext, $config['hookPostChange'])) {
                $func = $config['hookPostChange'];
                $controllerContext->$func('edit', $data);
            }

            return Response::send(0, null, null, '[js]parent.api.dialog.dialogClose(parent.__dialogData.edit);');
        }

        $data = $model->toArray();

        foreach ($this->runtimeData['fields'] as $key) {
            $field = &$config['fields'][$key];
            if (array_key_exists($key, $data)) {
                $data[$key] = $field['_instance']->valueUnserialize($data[$key]);
            } else {
                $data[$key] = $data;
            }
        }

        if (method_exists($controllerContext, $config['hookPostRead'])) {
            $func = $config['hookPostRead'];
            $controllerContext->$func($data);
        }

        return view($config['viewEdit'], [
            'runtimeData' => $this->runtimeData,
            'config' => $config,
            '_id' => $_id,
            'data' => $data
        ]);

    }

    public function executeAdd(&$controllerContext, $config)
    {
        $config = $this->processConfig($config, BaseCms::TYPE_ADD);

        if (!$config['canAdd']) {
            return Response::send(-1, '不允许编辑');
        }

        $_model = new DynamicModel();
        $_model->setTable($config['model']);
        $_primaryKey = $config['primaryKey'];

        if (Request::isMethod('post')) {

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
                    return Response::send(-1, $ret['msg']);
                }
                $data[$key] = $ret['data'];
            }

            foreach ($this->runtimeData['fields'] as $key) {
                $field = &$config['fields'][$key];
                $data[$key] = $field['_instance']->valueSerialize($data[$key]);
                $data = $field['_instance']->valuesSerialize($data);
            }

            if (method_exists($controllerContext, $config['hookBeforeAddResolve'])) {
                $func = $config['hookBeforeAddResolve'];
                $controllerContext->$func($data);
            }
            if (method_exists($controllerContext, $config['hookBeforeAddCheck'])) {
                $func = $config['hookBeforeAddCheck'];
                $ret = $controllerContext->$func($data);
                if ($ret['code']) {
                    return Response::send(-1, $ret['msg']);
                }
            }

            if (method_exists($controllerContext, $config['hookPreAdd'])) {
                $func = $config['hookPreAdd'];
                $controllerContext->$func($data);
            }

            foreach ($data as $k => $v) {
                $_model->$k = $v;
            }
            $_model->save();

            $data[$_primaryKey] = $_model->$_primaryKey;

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

            return Response::send(0, null, null, '[js]parent.api.dialog.dialogClose(parent.__dialogData.add);');

        }

        return view($config['viewAdd'], [
            'runtimeData' => $this->runtimeData,
            'config' => $config,
        ]);
    }

    public function executeExportData(&$controllerContext, $config)
    {
        $config = $this->processConfig($config, BasicCms::TYPE_EXPORT);

        $_model = new DynamicModel();
        $_model->setTable($config['model']);
        $_primaryKey = $config['primaryKey'];

        $_model = $_model->orderBy('id', 'asc');
        $_option = @json_decode(Input::get('option', []), true);

        if (!empty($config['joins'])) {
            $conditionFieldMap = [];
            $select = [];
            $select[] = $config['model'] . '.*';
            if (is_array($config['joins'][0])) {
                foreach ($config['joins'] as $join) {
                    $_model = $_model->leftJoin($join[0], $join[1], $join[2], $join[3]);
                    foreach ($join[4] as $aliasField => $tableField) {
                        $conditionFieldMap[$aliasField] = $tableField;
                        array_push($select, "$tableField as $aliasField");
                    }
                }
            } else {
                $join = $config['joins'];
                $_model = $_model->leftJoin($join[0], $join[1], $join[2], $join[3]);
                foreach ($join[4] as $aliasField => $tableField) {
                    $conditionFieldMap[$aliasField] = $tableField;
                    array_push($select, "$tableField as $aliasField");
                }
            }
            $_model = call_user_func_array(array($_model, 'select'), $select);
            ModelUtil::replaceConditionParamField($_option, $conditionFieldMap);
        }

        if (isset($_option['search']['page'])) {
            unset($_option['search']['page']);
        }
        if (!empty($config['listFilter'])) {
            ModelUtil::paginateMergeConditionParam($_model, $config['listFilter']);
        }
        ModelUtil::paginateMergeConditionParam($_model, $_option);

        $list = [];

        $head = [];
        $head[] = 'ID';
        foreach ($this->runtimeData['fields'] as $key) {
            $field = &$config['fields'][$key];
            $head[] = $field['title'];
        }

        $list[] = $head;

        $hookProcessExport = method_exists($controllerContext, $config['hookProcessExport']);
        $hookProcessExportField = method_exists($controllerContext, $config['hookProcessExportField']);

        $pageSize = 1000;
        $page = 1;
        do {
            $paginateData = $_model->paginate($pageSize, ['*'], 'page', $page)->toArray();
            $page++;

            foreach ($paginateData['data'] as &$record) {
                $item = [];
                $item[$_primaryKey] = $record[$_primaryKey];
                foreach ($this->runtimeData['fields'] as $key) {
                    $field = &$config['fields'][$key];
                    if (array_key_exists($key, $record)) {
                        $item[$key] = $field['_instance']->valueUnserialize($record[$key]);
                        $item[$key] = $field['_instance']->exportValue($item[$key]);
                    } else {
                        if ($hookProcessExportField) {
                            $func = $config['hookProcessExportField'];
                            $item[$key] = $controllerContext->$func($key, $record);
                        } else {
                            $item[$key] = '[没找到调用' . $config['hookProcessExportField'] . ']';
                        }
                    }
                }

                if ($hookProcessExport) {
                    $func = $config['hookProcessExport'];
                    $controllerContext->$func($item, $record);
                }

                $list[] = array_values($item);
            }

        } while (!empty($paginateData['data']));

        return $list;
    }

    public function executeExport(&$controllerContext, $config)
    {
        if (!$config['canExport']) {
            return Response::send(-1, '不允许导出');
        }

        if (AdminPowerUtil::isDemo()) {
            return AdminPowerUtil::demoResponse();
        }

        $list = $this->executeExportData($controllerContext, $config);

        return Excel::create($config['pageTitle'] . '_' . date('Ymd_His', time()), function ($excel) use (&$list) {
            $excel->sheet('data', function ($sheet) use (&$list) {
                $formats = [];
                for ($i = 0; $i < count($list[0]); $i++) {
                    $formats[\PHPExcel_Cell::stringFromColumnIndex($i)] = '@';
                }
                $sheet->setColumnFormat($formats);
                $sheet->setAutoSize(true);
                $sheet->rows($list, true);
            });
        })->export('xlsx');

    }

    public function executeImport(&$controllerContext, $config)
    {
        if (!$config['canImport']) {
            return Response::send(-1, '不允许导出');
        }

        if (AdminPowerUtil::isDemo()) {
            return AdminPowerUtil::demoResponse();
        }

        $config = $this->processConfig($config, BasicCms::TYPE_IMPORT);

        $_action = Input::get('_action');

        switch ($_action) {
            case 'template':
                $hookTemplateDataImport = method_exists($controllerContext, $config['hookTemplateDataImport']);
                if (!$hookTemplateDataImport) {
                    return Response::send(-1, 'hookTemplateDataImport not exists');
                }
                $func = $config['hookTemplateDataImport'];
                $list = $controllerContext->$func();
                if (empty($list)) {
                    return Response::send(-1, 'hookTemplateDataImport should return array');
                }
                return Excel::create($config['pageTitle'] . '_' . date('Ymd_His', time()), function ($excel) use (&$list) {
                    $excel->sheet('data', function ($sheet) use (&$list) {
                        $formats = [];
                        for ($i = 0; $i < count($list[0]); $i++) {
                            $formats[\PHPExcel_Cell::stringFromColumnIndex($i)] = '@';
                        }
                        $sheet->setColumnFormat($formats);
                        $sheet->setAutoSize(true);
                        $sheet->rows($list, true);
                    });
                })->export('xlsx');
        }

        if (Request::isMethod('post')) {
            $excelFile = Input::get('excelFile');
            if (empty($excelFile)) {
                return Response::send(-1, 'Excel文件为空');
            }

            if (!DataFacade::isTempDataPath($excelFile)) {
                return Response::send(-1, 'excel file path incorrect');
            }

            $ret = DataFacade::prepareTempDataForLocalUse($excelFile);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }
            $excelFile = $ret['data']['path'];

            if (!file_exists($excelFile)) {
                return Response::send(-1, 'Excel文件不存在');
            }

            $data = [];

            Excel::load($excelFile, function ($reader) use (&$data) {
                $reader->noHeading();
                $data = $reader->toArray();
            });

                        if (empty($data) || !is_array($data)) {
                return Response::send(-1, 'Excel数据为空');
            }

            if (count($data) <= 1) {
                return Response::send(-1, 'Excel数据为空!');
            }

            $hookProcessDataImport = method_exists($controllerContext, $config['hookProcessDataImport']);
            if (!$hookProcessDataImport) {
                return Response::send(-1, 'hookProcessDataImport not exists');
            }
            $func = $config['hookProcessDataImport'];

            return $controllerContext->$func($data);
                    }

        return view($config['viewImport'], [
            'runtimeData' => $this->runtimeData,
            'config' => $config,
        ]);

    }

}
