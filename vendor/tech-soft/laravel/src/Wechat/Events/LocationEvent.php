<?php

namespace TechSoft\Laravel\Wechat\Events;

use TechSoft\Laravel\Wechat\Support\Application;

class LocationEvent
{
    
    public $app;

    public $data;
    
    public $latitude;
    public $longitude;
    public $precision;
}