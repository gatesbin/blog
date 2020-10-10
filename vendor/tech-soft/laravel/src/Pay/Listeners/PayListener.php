<?php

namespace TechSoft\Laravel\Pay\Listeners;

use TechSoft\Laravel\Pay\Events\OrderPayedEvent;
use Illuminate\Support\Facades\Log;

class PayListener
{
    public function onOrderPayed(OrderPayedEvent $event)
    {
        Log::info('order pay -> ' . print_r($event, true));
    }

    public function subscribe($events)
    {
        $events->listen(
            OrderPayedEvent::class,
            '\TechSoft\Laravel\Pay\Listeners\PayListener@onOrderPayed'
        );
    }
}