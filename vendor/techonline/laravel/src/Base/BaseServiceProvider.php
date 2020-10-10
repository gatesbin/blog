<?php

namespace TechOnline\Laravel\Base;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../views', 'base');
    }

    public function register()
    {
        $this->setupMonitor();
    }

    private function setupMonitor()
    {
        static $queryCountPerRequest = 0;
        static $queryCountPerRequestSqls = [];

        Route::after(function () use (&$queryCountPerRequest, &$queryCountPerRequestSqls) {
            $time = round((microtime(true) - LARAVEL_START) * 1000, 2);
            $param = json_encode(Request::input());
            $url = Request::url();
            $method = Request::getMethod();
            if ($time > 1000) {
                $param = json_encode(Request::input());
                $url = Request::url();
                $method = Request::getMethod();
                Log::warning("LONG_REQUEST $method [$url] ${time}ms $param");
            }
            if ($queryCountPerRequest > 10) {
                Log::warning("MASS_REQUEST_SQL $queryCountPerRequest $method [$url] $param -> " . json_encode($queryCountPerRequestSqls));
                            }
        });

        Event::listen('illuminate.query', function ($query, $bindings, $time, $connectionName) use (&$queryCountPerRequest, &$queryCountPerRequestSqls) {
            $queryCountPerRequest++;
            $queryCountPerRequestSqls[] = "$query, " . json_encode($bindings);
                        if ($time > 500) {
                $param = json_encode($bindings);
                Log::warning("LONG_SQL ${time}ms, $query, $param");
            }
        });
    }
}
