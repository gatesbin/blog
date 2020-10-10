<?php

namespace TechSoft\Laravel\Admin\Support;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

trait AdminUserTrait
{
    private $adminUser = null;

    private function adminUserSetup()
    {
        View::share('_adminUser', $this->adminUser());
        View::share('_adminUserId', $this->adminUserId());
    }

    protected function adminUser()
    {
        if (null == $this->adminUser) {
            $this->adminUser = Session::get('_adminUser');
        }
        return $this->adminUser;
    }

    protected function adminUserId()
    {
        $this->adminUser();
        return $this->adminUser['id'];
    }
}
