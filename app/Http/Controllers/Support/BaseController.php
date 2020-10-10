<?php

namespace App\Http\Controllers\Support;

use App\Services\QuestionService;
use Illuminate\Routing\Controller;
use TechSoft\Laravel\View\TemplateViewTrait;

class BaseController extends Controller
{
    use TemplateViewTrait;
}