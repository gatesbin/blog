<?php

namespace TechSoft\Laravel\Pay\Events;

class OrderPayedEvent
{
    public $biz;
    public $bizId;
    public $order;
}