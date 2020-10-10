<?php

namespace TechSoft\Laravel\Api\Middleware;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use TechOnline\Laravel\Http\Response;
use TechOnline\Utils\SignUtil;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use TechSoft\Laravel\Api\ApiAppUtil;

class ApiAppSignCheckMiddleware
{

    
    public function handle($request, \Closure $next)
    {
        $sign = Input::get('sign');
        $timestamp = Input::get('timestamp');
        $appId = Input::get('app_id');

        if (empty($appId)) {
            return Response::json(-1, 'app_id empty');
        }

        $apiApp = ApiAppUtil::loadByAppId($appId);
        if (empty($apiApp)) {
            return Response::json(-1, 'invalid app_id');
        }

        if (empty($timestamp)) {
            return Response::json(-1, 'timestamp empty');
        }

        if (($timestamp < time() - 1800 || $timestamp > time() + 1800) && empty($apiApp['isDemo'])) {
            return Response::json(-1, 'timestamp not valid (' . time() . ')');
        }

        if (empty($sign)) {
            return Response::json(-1, 'sign empty');
        }

        $params = Input::all();
        unset($params['sign']);
        if (isset($params['_input'])) {
            unset($params['_input']);
        }

        $signCalc = SignUtil::common($params, $apiApp['appSecret']);
        if ($sign != $signCalc && empty($apiApp['isDemo'])) {
            Log::info('sign not match : ' . $signCalc);
            $ret = $this->signNotMatch();
            if ($ret['code']) {
                return $ret['data'];
            }
        }

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

        $ret = $this->moduleCheck($apiApp, $controller, $action);
        if ($ret['code']) {
            return $ret['data'];
        }

        Session::flash('_api_app', $apiApp);

        return $next($request);
    }

    protected function signNotMatch()
    {
        return Response::generate(-1, 'sign error', Response::json(-1, 'sign error'));
    }

    protected function moduleCheck($apiApp, $controller, $action)
    {
        return Response::generate(0, null);
    }
}
