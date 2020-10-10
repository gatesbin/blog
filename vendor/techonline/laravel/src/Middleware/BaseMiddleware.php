<?php

namespace TechOnline\Laravel\Middleware;


use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class BaseMiddleware
{
    
    public function handle($request, \Closure $next)
    {
        $path = Request::path();
        if (empty($path)) {
            $path = '/';
        } else if (!Str::startsWith($path, '/')) {
            $path = '/' . $path;
        }
        View::share('base_request_path', $path);
        Session::flash('base_request_path', $path);

        return $next($request);
    }
}