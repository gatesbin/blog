<?php

namespace TechSoft\Laravel\Api\Command;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use TechOnline\Laravel\Dao\ModelUtil;

class ApiTokenDBCleanerCommand extends Command
{
    
    protected $signature = 'ApiTokenDBCleanerCommand';

    
    protected $description = 'ApiTokenDBCleanerCommand';

    
    public function handle()
    {
        ModelUtil::model('api_token')->where('expireTime', '<', Carbon::now())->delete();
    }
}