<?php

namespace TechSoft\Laravel\Wechat\Events;

use TechSoft\Laravel\Wechat\Support\Application;

class MenuClickEvent
{
    
    public $app;
    
    public $data;
    
    public $key;
}