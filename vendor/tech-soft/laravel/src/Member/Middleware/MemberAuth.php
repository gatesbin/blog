<?php

namespace TechSoft\Laravel\Member\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use TechSoft\Laravel\Api\ApiSessionUtil;
use TechSoft\Laravel\Member\MemberUtil;

class MemberAuth
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

        $memberUserId = intval(Session::get('memberUserId', 0));
        $sessionType = 'session';
        if (empty($memberUserId)) {
            $memberUserId = intval(ApiSessionUtil::get('memberUserId', 0));
            $sessionType = 'api';
        }
        $memberUser = null;
        if ($memberUserId) {
            $memberUser = MemberUtil::get($memberUserId);
        }


        if ($memberUserId && !$memberUser) {
            $memberUserId = 0;
            if ('api' == $sessionType) {
                ApiSessionUtil::forget('memberUserId');
            }
        }
        
        Session::put('memberUserId', $memberUserId);
        Session::flash('_memberUser', $memberUser);

        $ret = $this->check($controller, $action, $memberUser);
        if ($ret['code']) {
            return $ret['data'];
        }

        return $next($request);
    }

        protected function check($controller, $action, $memberUser)
    {
        return Response::generate(0, 'ok');
    }

}