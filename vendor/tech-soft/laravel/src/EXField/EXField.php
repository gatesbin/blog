<?php

namespace TechSoft\Laravel\EXField;

use Illuminate\Support\Facades\View;

class EXField
{
    private $appModule = '';

    private $viewBases = [];
    public function __construct($viewBases = [])
    {
        if (empty($viewBases)) {
            $viewBases = [];
        } else {
            if (!is_array($viewBases)) {
                $viewBases = [$viewBases];
            }
        }
        if (!in_array('soft::exfield', $viewBases)) {
            $viewBases[] = 'soft::exfield';
        }
        $this->viewBases = $viewBases;
    }

    public function setAppModule($appModule)
    {
        $this->appModule = $appModule;
        return $this;
    }


    private function renderEditorEdit($modules = [])
    {
        $html = [];
        foreach ($modules as $module => $moduleName) {
            foreach ($this->viewBases as $viewBase) {
                if (view()->exists($view = $viewBase . '.edit.' . $module)) {
                    $html[] = View::make($view, [
                        'appModule' => $this->appModule,
                    ])->render();
                    break;
                }
            }
        }
        return join("", $html);
    }

    private function renderEditorManage($modules = [])
    {
        $html = [];
        foreach ($modules as $module => $moduleName) {
            foreach ($this->viewBases as $viewBase) {
                if (view()->exists($view = $viewBase . '.manage.' . $module)) {
                    $html[] = View::make($view, [
                        'appModule' => $this->appModule,
                    ])->render();
                    break;
                }
            }
        }
        return join("", $html);
    }

    
    public function renderEditor($modules = [])
    {
        return View::make('soft::exfield.view', [
            'edits' => $this->renderEditorEdit($modules),
            'manages' => $this->renderEditorManage($modules),
            'modules' => $modules,
            'appModule' => $this->appModule,
        ]);
    }

    public function render($keyModuleMap)
    {
        if (empty($keyModuleMap) || !is_array($keyModuleMap)) {
            return null;
        }
        $html = [];
        foreach ($keyModuleMap as $key => $field) {
            if (empty($field['type'])) {
                continue;
            }
            $type = $field['type'];
            $html[] = '<!-- ' . $type . '-' . $key . ' start -->';
            foreach ($this->viewBases as $viewBase) {
                if (view()->exists($view = $viewBase . '.view.' . $type)) {
                    $data = empty($field['data']) ? null : $field['data'];
                    $title = empty($field['title']) ? null : $field['title'];
                    $html[] = View::make($view, [
                        'title' => $title,
                        'key' => $key,
                        'data' => $data,
                    ])->render();
                    break;
                }
            }
            $html[] = '<!-- ' . $type . '-' . $key . ' end -->';
        }
        return join("\n", $html);
    }

}