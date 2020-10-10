<?php

namespace TechSoft\Laravel\Member\Traits;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

trait MemberUserTrait
{
    protected $memberUser = null;

    protected function memberUserSetup()
    {
        View::share('_memberUserId', $this->memberUserId());
        View::share('_memberUser', $this->memberUser());
    }

    protected function memberUser($key = null)
    {
        if (null == $this->memberUser) {
            $this->memberUser = Session::get('_memberUser');
        }
        if (null !== $key) {
            return $this->memberUser[$key];
        }
        return $this->memberUser;
    }

    protected function memberUserId()
    {
        $this->memberUser();
        return intval($this->memberUser['id']);
    }
}