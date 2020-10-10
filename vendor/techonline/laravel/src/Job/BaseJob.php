<?php

namespace TechOnline\Laravel\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class BaseJob implements SelfHandling, ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

}