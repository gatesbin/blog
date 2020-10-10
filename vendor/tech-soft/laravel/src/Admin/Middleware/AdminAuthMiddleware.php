<?php

namespace TechSoft\Laravel\Admin\Middleware;

use Closure;
use TechOnline\Laravel\Http\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use TechSoft\Laravel\Admin\Support\AdminCheckController;
use TechSoft\Laravel\Admin\Util\AdminPowerUtil;
use TechSoft\Laravel\Admin\Util\AdminUtil;

class AdminAuthMiddleware
{
    
    public function handle($request, Closure $next)
    {
        $routeAction = Route::currentRouteAction();
        $pieces = explode('@', $routeAction);
        if (isset($pieces[0])) {
            $controller = $pieces[0];
        } else {
            $controller = null;
        }
        if (isset($pieces[1])) {
            $action = $pieces[1];
        } else {
            $action = null;
        }
        if (!Str::startsWith($controller, '\\')) {
            $controller = '\\' . $controller;
        }

        $adminUser = null;

        $adminUserId = intval(Session::get('_adminUserId', null));
        if (empty($adminUserId)) {
            $authAdminUserId = intval(Request::header('auth-admin-user-id'));
            $authAdminTimestamp = intval(Request::header('auth-admin-timestamp'));
            $authAdminSign = trim(Request::header('auth-admin-sign'));
            if ($authAdminUserId > 0) {
                if ($authAdminTimestamp < time() - 1800 || $authAdminTimestamp > time() + 1800) {
                    return Response::send(-1, "auth-admin-timestamp error");
                }
                $authAdminUser = AdminUtil::get($authAdminUserId);
                if (empty($authAdminUser)) {
                    return Response::send(-1, "admin user not exists");
                }
                if (empty($authAdminUser['password']) || empty($authAdminUser['passwordSalt'])) {
                    return Response::send(-1, "admin user forbidden");
                }
                $signCalc = md5("$authAdminUserId:$authAdminTimestamp:$authAdminUser[password]$authAdminUser[passwordSalt]");
                if ($signCalc != $authAdminSign) {
                    return Response::send(-1, 'admin user sign error');
                }
                $adminUserId = $authAdminUser['id'];
                $adminUser = $authAdminUser;
            }
        }

        if ($adminUserId) {
            if (empty($adminUser)) {
                $adminUser = AdminUtil::get($adminUserId);
            }
        }

        if ($adminUserId && !$adminUser) {
            Session::forget('_adminUserId');
            return Response::send(-1, '请登录', null, action('\TechSoft\Laravel\Admin\Controller\LoginController@index', ['redirect' => Request::url()]));
        }

        if (is_subclass_of($controller, AdminCheckController::class)) {
            if (empty($adminUser)) {
                return Response::send(-1, null, null, action('\TechSoft\Laravel\Admin\Controller\LoginController@index', ['redirect' => Request::url()]));
            }
        }
        Session::flash('_adminUser', $adminUser);

        
        $rules = [];
        foreach (AdminPowerUtil::rules('powers') as $rule) {
            $rules[$rule] = false;
        }

        if ($adminUser['id'] == 1) {
            foreach ($rules as $rule => $index) {
                $rules[$rule] = true;
            }
        } else {
            $adminHasRules = Session::get('_adminHasRules', []);
            if ((empty($adminHasRules) && $adminUser['id'] > 0) || $adminUser['ruleChanged']) {
                if ($adminUser['ruleChanged']) {
                    AdminUtil::ruleChanged($adminUser['id'], false);
                }
                $adminHasRules = [];
                $ret = AdminUtil::getRolesByUserId($adminUser['id']);
                foreach ($ret['data'] as $role) {
                    foreach ($role['rules'] as $rule) {
                        $adminHasRules[$rule['rule']] = true;
                    }
                }
                Session::put('_adminHasRules', $adminHasRules);
            }
            foreach ($adminHasRules as $rule => $v) {
                $rules[$rule] = true;
            }
        }

        Session::put('_adminRules', $rules);

        if (isset($rules[$controller . '@' . $action]) && !$rules[$controller . '@' . $action]) {
            return Response::send(-1, '没有权限');
        }

        return $next($request);
    }

}
