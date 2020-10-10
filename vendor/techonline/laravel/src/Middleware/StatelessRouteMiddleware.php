<?php

namespace TechOnline\Laravel\Middleware;

class StatelessRouteMiddleware
{
    
    public function handle($request, \Closure $next)
    {
        config()->set('session.driver', 'array');
        return $next($request);
    }

}