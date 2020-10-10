<?php

namespace TechSoft\Laravel\Wechat\Events;

use EasyWeChat\Message\Text;
use TechSoft\Laravel\Wechat\Support\Application;

class SubscribeEvent
{
    
    public $app;
    
    public $data;
    
    public $scene;
}