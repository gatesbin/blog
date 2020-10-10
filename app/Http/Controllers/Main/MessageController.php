<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Mews\Captcha\Facades\Captcha;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use TechOnline\Utils\PageHtmlUtil;

class MessageController extends BaseController
{
    public function index()
    {
        $page = Input::get('page', 1);
        $pageSize = 20;

        $option = [];
        $option['where'] = [];
        $option['order'] = ['id', 'desc'];

        $paginateData = ModelUtil::paginate('message', $page, $pageSize, $option);

        $pageHtml = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, '?' . Request::mergeQueries(['page' => ['{page}']]));
        $messages = $paginateData['records'];

        return $this->_view('message.index', compact('pageHtml', 'messages'));
    }

    public function submit()
    {
        $data = [];

        $data['username'] = trim(Input::get('username'));
        $data['content'] = trim(Input::get('content'));
        $data['email'] = trim(Input::get('email'));
        $data['url'] = trim(Input::get('url'));

        $captcha = Input::get('captcha');
        if (!Captcha::check($captcha)) {
            return Response::send(-1, '图片验证码错误', null, '[js]$(\'[data-captcha]\').click();');
        }
        if (empty($data['content'])) {
            return Response::send(-1, '留言内容为空');
        }
        ModelUtil::insert('message', $data);

        return Response::send(0, null, null, '[js]__app.success();');
    }

    public function up($id)
    {
        $message = ModelUtil::get('message', ['id' => $id]);
        if (empty($message)) {
            return Response::send(-1, 'message not found');
        }
        if (Session::has('message-up-' . $message['id'])) {
            return Response::send(0, null, ['count' => $message['upCount']]);
        }
        Session::put('message-up-' . $message['id'], true);
        $message = ModelUtil::update('message', ['id' => $message['id']], ['upCount' => $message['upCount'] + 1]);
        return Response::send(0, null, ['count' => $message['upCount']]);
    }


    public function down($id)
    {
        $message = ModelUtil::get('message', ['id' => $id]);
        if (empty($message)) {
            return Response::send(-1, 'message not found');
        }
        if (Session::has('message-down-' . $message['id'])) {
            return Response::send(0, null, ['count' => $message['downCount']]);
        }
        Session::put('message-down-' . $message['id'], true);
        $message = ModelUtil::update('message', ['id' => $message['id']], ['downCount' => $message['downCount'] + 1]);
        return Response::send(0, null, ['count' => $message['downCount']]);
    }

    public function submitCaptcha()
    {
        return Captcha::create('formula');
    }

}
