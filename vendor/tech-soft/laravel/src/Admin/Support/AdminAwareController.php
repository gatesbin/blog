<?php

namespace TechSoft\Laravel\Admin\Support;

use Illuminate\Routing\Controller;

class AdminAwareController extends Controller
{
    use AdminUserTrait;

    public function __construct()
    {
        $this->adminUserSetup();
    }
}
