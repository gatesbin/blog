<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use Illuminate\Support\Facades\Input;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Request;
use TechOnline\Utils\PageHtmlUtil;

class IndexController extends BaseController
{
    public function index()
    {
        $page = Input::get('page', 1);
        $pageSize = 20;

        $option = [];
        $option['where'] = [];
        $option['order'] = ['postTime', 'desc'];
        $option['where']['isPublished'] = 1;

        $paginateData = ModelUtil::paginate('blog', $page, $pageSize, $option);

        $pageHtml = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, '?' . Request::mergeQueries(['page' => ['{page}']]));
        ModelUtil::decodeRecordsJson($paginateData['records'], 'images');
        $blogs = $paginateData['records'];

        return $this->_view('index', compact('pageHtml', 'blogs'));
    }

}