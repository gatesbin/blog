<?php

namespace TechSoft\Laravel\Security;

trait HttpsForceCheckTrait
{
    public function bootHttpsForceCheck()
    {
        
        if (env('FORCE_HTTPS', false)) {

            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                    $this->app['request']->server->set('HTTPS', true);
                } else {
                    if (isset($_SERVER['HTTP_HOST'])) {
                        header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                        exit();
                    }
                }
            }

        }

    }
}