<?php

namespace TechSoft\Laravel\Admin\Util;

use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AdminPowerUtil
{
    public static function permit($action)
    {
        static $adminRules = null;
        static $adminUser = null;
        if (null === $adminRules) {
            $adminRules = Session::get('_adminRules');
            $adminUser = Session::get('_adminUser');
        }
        if ($adminUser && $adminUser['id'] == env('ADMIN_FOUNDER_ID', 1)) {
            return true;
        }
        if (!isset($adminRules[$action])) {
            return true;
        }
        return $adminRules[$action] ? true : false;
    }

    public static function rules($type)
    {
        $rulesLib = config('admin_menus', []);
        switch ($type) {
            case 'powers':
                $powers = [];
                foreach ($rulesLib as $tab => $ruleList) {
                    foreach ($ruleList as $tabOrMenu => $actionOrRuleList) {
                        if (is_string($actionOrRuleList)) {
                            $powers[$actionOrRuleList] = true;
                        } else {
                            foreach ($actionOrRuleList as $title => $rule) {
                                $powers[$rule] = true;
                            }
                        }
                    }
                }
                return array_keys($powers);

            case 'powerList':
                $rulesLibFilter = [];
                foreach ($rulesLib as $tab => $ruleList) {
                    if (Str::startsWith($tab, 'HIDE:')) {
                        $tab = substr($tab, 5);
                    }
                    $index = strpos($tab, ':');
                    if (false !== $index) {
                        $tab = substr($tab, $index + 1);
                    }
                    foreach ($ruleList as $tabOrMenu => $actionOrRuleList) {
                        if (Str::startsWith($tabOrMenu, 'HIDE:')) {
                            $tabOrMenu = substr($tabOrMenu, 5);
                        }
                        if (is_string($actionOrRuleList)) {
                            $rulesLibFilter[$tab][$tabOrMenu] = $actionOrRuleList;
                        } else {
                            foreach ($actionOrRuleList as $title => $rule) {
                                if (Str::startsWith($title, 'HIDE:')) {
                                    $title = substr($title, 5);
                                }
                                $rulesLibFilter[$tab][$tabOrMenu][$title] = $rule;
                            }
                        }
                    }
                }
                foreach ($rulesLibFilter as $tab => $ruleList) {
                    foreach ($ruleList as $titleOrCat => $actionOrRuleList) {
                        if (empty($rulesLibFilter[$tab][$titleOrCat])) {
                            unset($rulesLibFilter[$tab][$titleOrCat]);
                        }
                    }
                    if (empty($rulesLibFilter[$tab])) {
                        unset($rulesLibFilter[$tab]);
                    }
                }
                $nodes = [];
                foreach ($rulesLibFilter as $tab => $ruleList) {
                    $node = [];
                    foreach ($ruleList as $titleOrCat => $actionOrRuleList) {
                        if (is_string($actionOrRuleList)) {
                            $node[] = ['title' => $titleOrCat, 'value' => $actionOrRuleList, 'nodes' => []];
                        } else {
                            $nod = [];
                            foreach ($actionOrRuleList as $title => $action) {
                                $nod[] = ['title' => $title, 'value' => $action, 'nodes' => []];
                            }
                            $node[] = ['title' => $titleOrCat, 'value' => '', 'nodes' => $nod];
                        }
                    }
                    $nodes[] = ['title' => $tab, 'value' => '', 'nodes' => $node];
                }
                return $nodes;

            case 'adminMenu':
                foreach ($rulesLib as $tab => $ruleList) {
                    if (Str::startsWith($tab, 'HIDE:')) {
                        unset($rulesLib[$tab]);
                        continue;
                    }
                    foreach ($ruleList as $tabOrMenu => $actionOrRuleList) {
                        if (Str::startsWith($tabOrMenu, 'HIDE:')) {
                            unset($rulesLib[$tab][$tabOrMenu]);
                            continue;
                        }
                        if (is_string($actionOrRuleList)) {
                            if (!self::permit($actionOrRuleList)) {
                                unset($rulesLib[$tab][$tabOrMenu]);
                                continue;
                            }
                        } else {
                            foreach ($actionOrRuleList as $title => $rule) {
                                if (Str::startsWith($title, 'HIDE:')) {
                                    unset($rulesLib[$tab][$tabOrMenu][$title]);
                                    continue;
                                }
                                if (!self::permit($rule)) {
                                    unset($rulesLib[$tab][$tabOrMenu][$title]);
                                    continue;
                                }
                            }
                        }
                    }
                }
                foreach ($rulesLib as $tab => $ruleList) {
                    foreach ($ruleList as $titleOrCat => $actionOrRuleList) {
                        if (empty($rulesLib[$tab][$titleOrCat])) {
                            unset($rulesLib[$tab][$titleOrCat]);
                        }
                    }
                    if (empty($rulesLib[$tab])) {
                        unset($rulesLib[$tab]);
                    }
                }
                return $rulesLib;
        }
    }

    public static function isDemoAndPost()
    {
        return self::isDemo() && Request::isPost();
    }

    public static function isDemo()
    {
        if (Session::get('_adminUserId', null)
            && env('ADMIN_DEMO_USER_ID', 0)
            && Session::get('_adminUserId') == env('ADMIN_DEMO_USER_ID', 0)
        ) {
            return true;
        }
        return false;
    }

    public static function demoResponse()
    {
        return Response::send(-1, '演示账号禁止修改信息');
    }

    public static function adminUserId()
    {
        return intval(Session::get('_adminUserId', null));
    }
}
