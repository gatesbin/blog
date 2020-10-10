<?php

namespace TechSoft\Laravel\Wechat\Events;

use EasyWeChat\Message\Text;
use TechSoft\Laravel\Wechat\Support\Application;

class ScanEvent
{
    
    public $app;
    
    public $data;

    public $scene;

    public $isSubscribe;
}