<?php

namespace TechSoft\Laravel\User\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Api\ApiSessionUtil;
use TechSoft\Laravel\User\UserUtil;

class UserWebApiAuth
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

        $userId = Session::get('userId', 0);
        if (empty($userId)) {
            $userId = ApiSessionUtil::get('userId', 0);
        }
        if ($userId) {
            $user = UserUtil::get($userId);
        } else {
            $user = null;
        }

        $request->session()->flash('_user', $user);

        $ret = $this->check($controller, $action, $user);
        if ($ret['code']) {
            return $ret['data'];
        }

        return $next($request);
    }

        protected function check($controller, $action, $user)
    {
        return Response::generate(0, 'ok');
    }

}