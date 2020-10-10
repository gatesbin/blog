<?php

namespace TechSoft\Laravel\Admin\Cms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

abstract class BaseCms
{
    const TYPE_NONE = 0;
    const TYPE_LIST = 1;
    const TYPE_ADD = 2;
    const TYPE_EDIT = 3;
    const TYPE_VIEW = 4;
    const TYPE_EXPORT = 5;
    const TYPE_DELETE = 6;
    const TYPE_IMPORT = 7;

    protected $defaultConfig = [

        'group' => 'cms',

                'model_connection' => null,
        'model' => Model::class,

        'joins' => [],


        'primaryKey' => 'id',
        'primaryKeyShow' => true,

        'canView' => false,
        'canAdd' => false,
        'canDelete' => false,
        'canEdit' => false,
        'canExport' => false,
        'canImport' => false,

        'batchOperate' => false,
        'batchDelete' => false,
        'addInNewWindow' => false,
        'editInNewWindow' => false,
        'viewInNewWindow' => false,

        'viewAdd' => 'admin::cms.base.add',
        'viewEdit' => 'admin::cms.base.edit',
        'viewList' => 'admin::cms.base.list',
        'viewView' => 'admin::cms.base.view',
        'viewConfigBase' => 'vendor-config.admin.cms.base',

        'actionList' => '{controller}@{group}List',
        'actionAdd' => '{controller}@{group}Add',
        'actionEdit' => '{controller}@{group}Edit',
        'actionDelete' => '{controller}@{group}Delete',
        'actionView' => '{controller}@{group}View',
        'actionExport' => '{controller}@{group}Export',
        'actionImport' => '{controller}@{group}Import',

                'permitCheck' => null,

        'pageTitle' => 'CMS',

        'pageTitleList' => '{pageTitle}查看',
        'pageTitleAdd' => '{pageTitle}添加',
        'pageTitleEdit' => '{pageTitle}编辑',
        'pageTitleView' => '{pageTitle}查看',
        'pageTitleImport' => '{pageTitle}导入',

                'hookProcessView' => '{group}ProcessView',
                'hookProcessViewField' => '{group}ProcessViewField',

                'hookProcessExport' => '{group}ProcessExport',
                'hookProcessExportField' => '{group}ProcessExportField',

                                                                
                                                                
                        
                'hookPreInputProcess' => '{group}PreInputProcess',

                'hookPostRead' => '{group}PostRead',

                'hookBeforeAddResolve' => '{group}BeforeAddResolve',
                'hookBeforeAddCheck' => '{group}BeforeAddCheck',
                'hookPreAdd' => '{group}PreAdd',
                'hookPostAdd' => '{group}PostAdd',

                'hookBeforeEditResolve' => '{group}BeforeEditResolve',
                'hookBeforeEditCheck' => '{group}BeforeEditCheck',
                'hookPreEdit' => '{group}PreEdit',
                'hookPostEdit' => '{group}PostEdit',

                'hookBeforeDeleteCheck' => '{group}BeforeDeleteCheck',
                'hookPreDelete' => '{group}PreDelete',
                'hookPostDelete' => '{group}PostDelete',

                'hookTemplateDataImport' => '{group}TemplateDataImport',
                'hookProcessDataImport' => '{group}ProcessDataImport',

                        'hookPostChange' => '{group}PostChange',

        'fields' => []
    ];

    protected $runtimeData = [
                'controller' => '',
                'action' => '',
                'fields' => [],
                'fieldsSearch' => [],
                'listAppend' => '',
                'listMenuAppend' => '',
                'addAppend' => '',
                'editAppend' => '',
                'batchOperation' => '',
    ];

    function __construct()
    {
        $this->_init();
    }


    protected function fetchFields($type, &$config)
    {
        $map = [
            self::TYPE_LIST => 'list',
            self::TYPE_ADD => 'add',
            self::TYPE_EDIT => 'edit',
            self::TYPE_VIEW => 'view',
            self::TYPE_EXPORT => 'export',
        ];

        if (!isset($map[$type])) {
            return [];
        }
        $fields = [];
        foreach ($config['fields'] as $fieldName => $fieldInfo) {
            if (array_key_exists($map[$type], $fieldInfo) && $fieldInfo[$map[$type]]) {
                $fields[] = $fieldName;
            }
        }
        return $fields;
    }

    protected function processConfig($config, $type)
    {
        $routeAction = Route::currentRouteAction();
        if (!Str::startsWith($routeAction, '\\')) {
            $routeAction = '\\' . $routeAction;
        }
        list($controller, $action) = explode('@', $routeAction);

        $this->runtimeData['controller'] = $controller;
        $this->runtimeData['action'] = $action;

        $mergedConfig = $this->defaultConfig;
        foreach ($config as $k => $v) {
            $mergedConfig[$k] = $v;
        }
        foreach ($mergedConfig as $k => $v) {
            $mergedConfig[$k] = str_replace([
                '{controller}', '{group}', '{pageTitle}'
            ], [
                $controller, $mergedConfig['group'], $mergedConfig['pageTitle']
            ], $v);
        }

        $controller = str_replace(['\\'], ['.'], $this->runtimeData['controller']);
        $controller = preg_replace_callback('/(.)([A-Z])/', function ($matches) {
            $prefix = $matches[1];
            if ($matches[1] != '.') {
                $prefix .= '-';
            }
            return $prefix . strtolower($matches[2]);
        }, $controller);

        $mergedConfig['viewConfigBase'] = $mergedConfig['viewConfigBase'] . $controller . '.' . $mergedConfig['group'];

                switch ($type) {
            case self::TYPE_LIST:
                if (view()->exists($mergedConfig['viewConfigBase'] . '.listAppend')) {
                    $this->runtimeData['listAppend'] = View::make($mergedConfig['viewConfigBase'] . '.listAppend', [
                        'config' => $mergedConfig
                    ])->render();
                }
                if (view()->exists($mergedConfig['viewConfigBase'] . '.batchOperation')) {
                    $this->runtimeData['batchOperation'] = View::make($mergedConfig['viewConfigBase'] . '.batchOperation', [
                        'config' => $mergedConfig
                    ])->render();
                }
                if (view()->exists($mergedConfig['viewConfigBase'] . '.listMenuAppend')) {
                    $this->runtimeData['listMenuAppend'] = View::make($mergedConfig['viewConfigBase'] . '.listMenuAppend', [
                        'config' => $mergedConfig
                    ])->render();
                }
                break;
            case self::TYPE_ADD:
                if (view()->exists($mergedConfig['viewConfigBase'] . '.addAppend')) {
                    $this->runtimeData['addAppend'] = View::make($mergedConfig['viewConfigBase'] . '.addAppend', [
                        'config' => $mergedConfig
                    ])->render();
                }
                break;
            case self::TYPE_EDIT:
                if (view()->exists($mergedConfig['viewConfigBase'] . '.editAppend')) {
                    $this->runtimeData['editAppend'] = View::make($mergedConfig['viewConfigBase'] . '.editAppend', [
                        'config' => $mergedConfig
                    ])->render();
                }
                break;
        }

        $this->runtimeData['fields'] = $this->fetchFields($type, $mergedConfig);
        $this->runtimeData['fieldsSearch'] = [];
        switch ($type) {
            case self::TYPE_LIST:
                foreach ($config['fields'] as $fieldName => $field) {
                    if (array_key_exists('search', $field) && $field['search']) {
                        $this->runtimeData['fieldsSearch'][] = $fieldName;
                    }
                }
                break;
        }

        foreach (array_merge($this->runtimeData['fields'], $this->runtimeData['fieldsSearch']) as $key) {
            $field = &$mergedConfig['fields'][$key];
            $field['_instance'] = new $field['type']($this);
            $field['_instance']->key = $key;
            $field['_instance']->field = &$field;
            foreach ($field as $k => &$v) {
                if (!in_array($k, ['type', '_instance'])) {
                    $field['_instance']->$k = $v;
                }
            }
        }

                if ($mergedConfig['permitCheck']) {
            if ($mergedConfig['canView']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionView'])) {
                    $mergedConfig['canView'] = false;
                }
            }
            if ($mergedConfig['canAdd']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionAdd'])) {
                    $mergedConfig['canAdd'] = false;
                }
            }
            if ($mergedConfig['canEdit']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionEdit'])) {
                    $mergedConfig['canEdit'] = false;
                }
            }
            if ($mergedConfig['canDelete']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionDelete'])) {
                    $mergedConfig['canDelete'] = false;
                }
            }
            if ($mergedConfig['canExport']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionExport'])) {
                    $mergedConfig['canExport'] = false;
                }
            }
            if ($mergedConfig['canImport']) {
                if (!$mergedConfig['permitCheck']($mergedConfig['actionImport'])) {
                    $mergedConfig['canImport'] = false;
                }
            }
        }

        return $mergedConfig;
    }

    public function executeList(&$controllerContext, $config)
    {
        return '[You should override the executeList method in implement class]';
    }

    public function executeAdd(&$controllerContext, $config)
    {
        return '[You should override the executeAdd method in implement class]';
    }

    public function executeEdit(&$controllerContext, $config)
    {
        return '[You should override the executeEdit method in implement class]';
    }

    public function executeDelete(&$controllerContext, $config)
    {
        return '[You should override the executeDelete method in implement class]';
    }

    public function executeView(&$controllerContext, $config)
    {
        return '[You should override the executeView method in implement class]';
    }

    public function executeExport(&$controllerContext, $config)
    {
        return '[You should override the executeExport method in implement class]';
    }

    public function executeImport(&$controllerContext, $config)
    {
        return '[You should override the executeImport method in implement class]';
    }


    protected function _init()
    {
    }
}
