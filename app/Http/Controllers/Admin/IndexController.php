<?php
namespace App\Http\Controllers\Admin;

use TechSoft\Laravel\Admin\Support\AdminCheckController;

class IndexController extends AdminCheckController
{
    public function index()
    {
        return view('admin.index');
    }
}