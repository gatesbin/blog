<?php

namespace TechSoft\Laravel\Wechat\Events;

use EasyWeChat\Message\Text;
use TechSoft\Laravel\Wechat\Support\Application;

class TextRecvEvent
{
    
    public $app;
    
    public $data;
}