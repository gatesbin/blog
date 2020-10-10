<?php

namespace TechSoft\Laravel\Api\Traits;

use Illuminate\Support\Facades\Session;

trait ApiAppTrait
{
    private $apiApp = null;

    protected function apiApp()
    {
        if (null == $this->apiApp) {
            $this->apiApp = Session::get('_api_app');
        }
        return $this->apiApp;
    }
}