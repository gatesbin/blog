<?php

namespace TechSoft\Laravel\View;

use TechOnline\Laravel\Util\AgentUtil;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use TechSoft\Laravel\Config\ConfigUtil;

trait TemplateViewTrait
{

    protected function _view($view, $viewData = [])
    {
        $template = ConfigUtil::get('siteTemplate', 'default');

        $mobileView = 'theme.' . $template . '.m.' . $view;
        $PCView = 'theme.' . $template . '.pc.' . $view;

        $defaultMobileView = 'theme.default.m.' . $view;
        $defaultPCView = 'theme.default.pc.' . $view;

        if ($this->isMobile()) {
            $frameLayoutView = 'theme.' . $template . '.m.frame';
            if (!view()->exists($frameLayoutView)) {
                $frameLayoutView = 'theme.default.m.frame';
            }
        } else {
            $frameLayoutView = 'theme.' . $template . '.pc.frame';
            if (!view()->exists($frameLayoutView)) {
                if (view()->exists('theme.' . $template . '.m.frame')) {
                    $frameLayoutView = 'theme.' . $template . '.m.frame';
                }
            }
        }
        if (!view()->exists($frameLayoutView)) {
            $frameLayoutView = 'theme.default.pc.frame';
        }
        View::share('_frameLayoutView', $frameLayoutView);

        if ($this->isMobile()) {
            if (view()->exists($mobileView)) {
                return view($mobileView, $viewData);
            }
            if (view()->exists($defaultMobileView)) {
                return view($defaultMobileView, $viewData);
            }
        }
        if (view()->exists($PCView)) {
            return view($PCView, $viewData);
        } else {
            if (view()->exists($mobileView)) {
                return view($mobileView, $viewData);
            }
            if (view()->exists($defaultMobileView)) {
                return view($defaultMobileView, $viewData);
            }
        }
        return view($defaultPCView, $viewData);
    }

    protected function _viewRender($view, $viewData = [])
    {
        $template = ConfigUtil::get('siteTemplate', 'default');

        $mobileView = 'theme.' . $template . '.m.' . $view;
        $PCView = 'theme.' . $template . '.pc.' . $view;

        $defaultMobileView = 'theme.default.m.' . $view;
        $defaultPCView = 'theme.default.pc.' . $view;

        if ($this->isMobile()) {
            if (view()->exists($mobileView)) {
                return View::make($mobileView, $viewData)->render();
            }
            if (view()->exists($defaultMobileView)) {
                return View::make($defaultMobileView, $viewData)->render();
            }
        }
        if (view()->exists($PCView)) {
            return View::make($PCView, $viewData)->render();
        } else {
            if (view()->exists($mobileView)) {
                return View::make($mobileView, $viewData)->render();
            }
            if (view()->exists($defaultMobileView)) {
                return View::make($defaultMobileView, $viewData)->render();
            }
        }
        return View::make($defaultPCView, $viewData)->render();
    }

    protected function _viewExists($view)
    {
        $template = ConfigUtil::get('siteTemplate', 'default');

        $mobileView = 'theme.' . $template . '.m.' . $view;
        $PCView = 'theme.' . $template . '.pc.' . $view;

        $defaultMobileView = 'theme.default.m.' . $view;
        $defaultPCView = 'theme.default.pc.' . $view;

        if ($this->isMobile()) {
            if (view()->exists($mobileView)) {
                return true;
            }
            if (view()->exists($defaultMobileView)) {
                return true;
            }
        }
        if (view()->exists($PCView)) {
            return true;
        } else {
            if (view()->exists($mobileView)) {
                return true;
            }
            if (view()->exists($defaultMobileView)) {
                return true;
            }
        }
        if (view()->exists($defaultPCView)) {
            return true;
        }
        return false;
    }

    protected function _viewFile($view)
    {
        $template = ConfigUtil::get('siteTemplate', 'default');

        $mobileView = 'theme.' . $template . '.m.' . $view;
        $PCView = 'theme.' . $template . '.pc.' . $view;

        $defaultMobileView = 'theme.default.m.' . $view;
        $defaultPCView = 'theme.default.pc.' . $view;

        if ($this->isMobile()) {
            if (view()->exists($mobileView)) {
                return $mobileView;
            }
            if (view()->exists($defaultMobileView)) {
                return $defaultMobileView;
            }
        }
        if (view()->exists($PCView)) {
            return $PCView;
        } else {
            if (view()->exists($mobileView)) {
                return $mobileView;
            }
            if (view()->exists($defaultMobileView)) {
                return $defaultMobileView;
            }
        }
        return $defaultPCView;
    }

    protected function _viewFilePath($view)
    {
        $file = $this->_viewFile($view);
        return View::getFinder()->find($file);
    }

    protected function isMobile()
    {
        return AgentUtil::isMobile();
    }

    protected function isPC()
    {
        return AgentUtil::isPC();
    }

}