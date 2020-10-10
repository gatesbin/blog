<?php

namespace TechSoft\Laravel\EXPage;

use Illuminate\Support\Facades\View;

class EXPage
{
    private $viewBases = [];
    private $dynamicGenerators = [];

    public function __construct($viewBases = [])
    {
        if (empty($viewBases)) {
            $viewBases = [];
        } else {
            if (!is_array($viewBases)) {
                $viewBases = [$viewBases];
            }
        }
        if (!in_array('soft::expage', $viewBases)) {
            $viewBases[] = 'soft::expage';
        }
        $this->viewBases = $viewBases;
    }

    public function setDynamicGenerator($modules, $callback)
    {
        if (!is_array($modules)) {
            $modules = [$modules];
        }
        foreach ($modules as $module) {
            $this->dynamicGenerators[$module] = $callback;
        }
    }

    private function renderEditorManage($modules = [])
    {
        $html = [];
        foreach ($modules as $module) {
            foreach ($this->viewBases as $viewBase) {
                if (view()->exists($view = $viewBase . '.manage.' . $module)) {
                    $html[] = View::make($view, [])->render();
                    break;
                }
            }
        }
        return join("", $html);
    }

    private function renderEditorEdit($modules = [])
    {
        $html = [];
        foreach ($modules as $module) {
            foreach ($this->viewBases as $viewBase) {
                if (view()->exists($view = $viewBase . '.edit.' . $module)) {
                    $html[] = View::make($view, [])->render();
                    break;
                }
            }
        }
        return join("", $html);
    }

    private function renderEditorModule($modules = [])
    {
        $html = [];
        foreach ($modules as $module) {
            foreach ($this->viewBases as $viewBase) {
                if (view()->exists($view = $viewBase . '.module.' . $module)) {
                    $html[] = View::make($view, [])->render();
                    break;
                }
            }
        }
        return join("", $html);
    }

    
    public function renderEditor($groupModules = [])
    {
        $modules = [];
        foreach ($groupModules as $g => $ms) {
            $modules = array_merge($modules, $ms);
        }
        foreach ($groupModules as $g => $ms) {
            $groupModules[$g]['_modules'] = $this->renderEditorModule($ms);
        }
        return View::make('soft::expage.view', [
            'manages' => $this->renderEditorManage($modules),
            'edits' => $this->renderEditorEdit($modules),
            'groupModules' => $groupModules,
        ]);
    }

    public function render($template)
    {
        if (empty($template) || !is_array($template)) {
            return null;
        }
        $html = [];
        foreach ($template as $index => $module) {
            if (empty($module['type'])) {
                continue;
            }
            $type = $module['type'];
            $html[] = '<!-- ' . $type . '-' . $index . ' start -->';
            foreach ($this->viewBases as $viewBase) {
                if (view()->exists($view = $viewBase . '.view.' . $type)) {
                    $data = empty($module['data']) ? null : $module['data'];
                    $dynamic = null;
                    if (!empty($this->dynamicGenerators[$module['type']])) {
                        $dynamic = call_user_func($this->dynamicGenerators[$module['type']], $module['type'], $data);
                    }
                    $html[] = View::make($view, [
                        'data' => $data,
                        'dynamic' => $dynamic,
                    ])->render();
                    break;
                }
            }
            $html[] = '<!-- ' . $type . '-' . $index . ' end -->';
        }
        return join("\n", $html);
    }

}