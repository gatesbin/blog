<?php

namespace TechSoft\Laravel\Api\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use TechSoft\Laravel\Api\ApiSessionUtil;

class ApiTokenCheckAndGenerateMiddleware
{
    
    public function handle($request, \Closure $next)
    {
        $apiToken = $this->get($request, 'api-token');
        if (empty($apiToken)) {
            $apiToken = ApiSessionUtil::getOrGenerateToken();
            header('api-token:' . $apiToken);
        }
        $request->headers->set('api-token', $apiToken);

        $apiDevice = $this->get($request, 'api-device');
        $request->headers->set('api-device', $apiDevice);

        $apiParam = $this->get($request, 'api-param');
        $request->headers->set('api-param', $apiParam);

        $apiVersion = $this->get($request, 'api-version');
        $request->headers->set('api-version', $apiVersion);

        return $next($request);
    }

                        protected function get(Request &$request, $key)
    {
        $apiToken = Input::get($key, null);
        if (!empty($apiToken)) {
            return $apiToken;
        }
        $apiToken = $request->header(str_replace('_', '-', $key));
        if (!empty($apiToken)) {
            return $apiToken;
        }
        if (!empty($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
        $key = str_replace('_', '-', $key);
        if (!empty($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
        return null;
    }

}
