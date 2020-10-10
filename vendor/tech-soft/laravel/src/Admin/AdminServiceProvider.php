<?php

namespace TechSoft\Laravel\Admin;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../views/admin', 'admin');

        $this->publishes([__DIR__ . '/../../config/admin.php' => config_path('admin.php')], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/admin.php', 'admin');
    }
}
