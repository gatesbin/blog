<?php

namespace App\Providers;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use TechSoft\Laravel\Assets\AssetsCDNTrait;

class AppServiceProvider extends ServiceProvider
{
    use AssetsCDNTrait;

    
    public function boot(Dispatcher $events)
    {
        $this->bootAssetsCDN();
    }

    
    public function register()
    {

    }
}
