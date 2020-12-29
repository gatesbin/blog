<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Support\BaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Mews\Captcha\Facades\Captcha;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Response;
use TechOnline\Utils\PageHtmlUtil;

class BlogController extends BaseController
{
    public function index($id)
    {
        $blog = ModelUtil::get('blog', ['id' => $id]);
        if (empty($blog)) {
            return Response::send(0, null, null, '/');
        }
        ModelUtil::decodeRecordJson($blog, ['tag', 'images']);
        ModelUtil::update('blog', ['id' => $blog['id']], ['clickCount' => $blog['clickCount'] + 1]);

        $commentPage = InputPackage::buildFromInput()->getInteger('commentPage', 1);
        $commentPageSize = 10;
        $option = [];
        $option['where']['blogId'] = $blog['id'];
        $option['order'] = ['id', 'desc'];
        $commentPaginate = ModelUtil::paginate('blog_comment', $commentPage, $commentPageSize, $option);
        $comments = $commentPaginate['records'];
        $commentPageHtml = PageHtmlUtil::render($commentPaginate['total'], $commentPageSize, $commentPage, '?commentPage={page}');

        $nextBlog = ModelUtil::model('blog')->where('postTime', '<', $blog['postTime'])->orderBy('postTime', 'desc')->limit(1)->first();
        if ($nextBlog) {
            $nextBlog = $nextBlog->toArray();
        }

        $prevBlog = ModelUtil::model('blog')->where('postTime', '>', $blog['postTime'])->orderBy('postTime', 'desc')->limit(1)->first();
        if ($prevBlog) {
            $prevBlog = $prevBlog->toArray();
        }

        return $this->_view('blog.index', compact('blog', 'comments', 'nextBlog', 'prevBlog', 'commentPageHtml'));
    }

    public function comment($id)
    {
        $blog = ModelUtil::get('blog', ['id' => $id]);
        if (empty($blog)) {
            return Response::send(-1, '博客丢了!');
        }

        $data = [];

        $data['blogId'] = $blog['id'];

        $data['username'] = trim(Input::get('username'));
        $data['content'] = trim(Input::get('content'));
        $data['email'] = trim(Input::get('email'));
        $data['url'] = trim(Input::get('url'));

        $captcha = Input::get('captcha');
        if (!Captcha::check($captcha)) {
            return Response::send(-1, '图片验证码错误', null, '[js]$(\'[data-captcha]\').click();');
        }

        if (empty($data['content'])) {
            return Response::send(-1, '留言内容为空!');
        }
        ModelUtil::insert('blog_comment', $data);
        return Response::send(0, null, null, '[reload]');
    }

    public function commentCaptcha()
    {
        return Captcha::create('formula');
    }
}
