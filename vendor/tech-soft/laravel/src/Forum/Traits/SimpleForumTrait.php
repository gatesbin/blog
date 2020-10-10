<?php

namespace TechSoft\Laravel\Forum\Traits;

use Carbon\Carbon;
use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use TechOnline\Utils\PageHtmlUtil;
use TechSoft\Laravel\Forum\ForumUtil;
use TechSoft\Laravel\Member\MemberUtil;
use TechSoft\Laravel\Util\HtmlUtil;
use TechOnline\Laravel\Dao\ModelUtil;
use Illuminate\Support\Facades\Input;

trait SimpleForumTrait
{
    public function index($categoryId = 0)
    {
        $categoryId = intval($categoryId);

        $page = Input::get('page', 1);
        $pageSize = 10;
        $option = [];
        $option['order'] = [['isTop', 'desc'], ['updated_at', 'desc']];

        $paginateData = ForumUtil::paginateThreadsByCategory($categoryId, $page, $pageSize, $option);
        $threads = $paginateData['records'];
        $pageHtml = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, "?page={page}");

        ModelUtil::join($threads, 'memberUserId', '_memberUser', 'member_user', 'id');
        ModelUtil::join($threads, 'categoryId', '_category', 'forum_category', 'id');

        $categories = ForumUtil::getCategories();

        return $this->_view('forum.index', compact('categoryId', 'threads', 'categories', 'pageHtml'));
    }

    public function threadMy()
    {
        if (!$this->memberUserId()) {
            return Response::send(-1, null, null, '/login?redirect=' . urlencode(Request::currentPageUrl()));
        }

        $page = Input::get('page', 1);
        $pageSize = 10;
        $option = [];
        $option['order'] = [['isTop', 'desc'], ['updated_at', 'desc']];

        $paginateData = ForumUtil::paginateMemberThreads($this->memberUserId(), $page, $pageSize, $option);
        $threads = $paginateData['records'];
        $pageHtml = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, "?page={page}");

        $categories = ForumUtil::getCategories();

        return $this->_view('forum.threadMy', compact('threads', 'categories', 'pageHtml'));
    }

    public function thread($threadId = 0)
    {

        $thread = ForumUtil::loadThread($threadId);
        if (empty($thread)) {
            return Response::send(-1, 'thread not found');
        }
        $thread['_memberUser'] = MemberUtil::get($thread['memberUserId']);
        $thread['_category'] = ForumUtil::loadCategory($thread['categoryId']);

        $page = Input::get('page', 1);
        $pageSize = 10;
        $option = [];
        $option['order'] = ['id', 'asc'];
        $paginateData = ForumUtil::paginateThreadPost($threadId, $page, $pageSize, $option);
        $pageHtml = PageHtmlUtil::render($paginateData['total'], $pageSize, $page, "?page={page}");

        $posts = $paginateData['records'];
        ModelUtil::join($posts, 'memberUserId', '_memberUser', 'member_user', 'id');

        $isCategoryAdmin = false;
        if ($this->memberUserId()) {
            if (ForumUtil::isCategoryAdmin($this->memberUserId(), $thread['_category']['id'])) {
                $isCategoryAdmin = true;
            }
        }

        return $this->_view('forum.thread', compact('thread', 'posts', 'pageHtml', 'isCategoryAdmin'));
    }

    public function threadDelete($threadId)
    {
        if (!$this->memberUserId()) {
            return Response::send(-1, null, null, '/login?redirect=' . urlencode(Request::currentPageUrl()));
        }

        $thread = ForumUtil::loadThread($threadId);
        if (empty($thread)) {
            return Response::send(-1, 'thread not found');
        }
        if ($thread['memberUserId'] != $this->memberUserId() && !ForumUtil::isCategoryAdmin($this->memberUserId(), $thread['categoryId'])) {
            return Response::send(-1, 'thread not yours');
        }

        ForumUtil::deleteThread($threadId);

        return Response::send(0, null, null, '/forum/' . $thread['categoryId']);
    }

    public function threadEdit($id = 0)
    {
        if (!$this->memberUserId()) {
            return Response::send(-1, null, null, '/login?redirect=' . urlencode(Request::currentPageUrl()));
        }

        $thread = null;
        if ($id) {
            $thread = ForumUtil::loadThread($id);
            if (empty($thread)) {
                return Response::send(-1, 'thread not found');
            }
            if ($thread['memberUserId'] != $this->memberUserId()) {
                return Response::send(-1, 'thread edit forbidden');
            }
        }

        if (Request::isPost()) {

            $data = [];
            $data['title'] = Input::get('title');
            $data['categoryId'] = Input::get('categoryId');
            $data['content'] = Input::get('content');
            $data['content'] = HtmlUtil::filter($data['content']);

            if (empty($data['categoryId'])) {
                return Response::send(-1, '分类不能为空');
            }

            if (empty($data['title'])) {
                return Response::send(-1, '标题不能为空');
            }
            if (empty($data['content'])) {
                return Response::send(-1, '内容不能为空');
            }

            if ($thread) {
                $thread = ForumUtil::updateThread($thread['id'], $data);
                return Response::send(0, null, null, '/forum/thread/' . $thread['id']);
            } else {
                $data['memberUserId'] = $this->memberUserId();
                $thread = ForumUtil::addThread($data);
                return Response::send(0, null, null, '/forum/thread/' . $thread['id']);
            }
        }

        $categories = ForumUtil::getCategories();

        return $this->_view('forum.threadEdit', compact('categories', 'thread'));
    }


    public function postEdit($threadId, $postId = 0)
    {
        if (!$this->memberUserId()) {
            return Response::send(-1, null, null, '/login?redirect=' . urlencode(Request::currentPageUrl()));
        }

        $thread = ForumUtil::loadThread($threadId);
        if (empty($thread)) {
            return Response::send(-1, 'thread not found');
        }

        if ($postId) {
            $post = ForumUtil::loadPost($postId);
            if (empty($post) || $post['memberUserId'] != $this->memberUserId()) {
                return Response::send(-1, 'post not found');
            }
        }

        if (Request::isPost()) {
            $data = [];
            $data['content'] = Input::get('content');
            $data['content'] = HtmlUtil::filter($data['content']);
            if (empty($data['content'])) {
                return Response::send(-1, '内容不能为空');
            }

            $data['categoryId'] = $thread['categoryId'];
            $data['threadId'] = $thread['id'];
            $data['memberUserId'] = $this->memberUserId();
            $data['replyPostId'] = intval(Input::get('replyPostId'));
            if (!preg_match('/@(.*?):/', $data['content'])) {
                $data['replyPostId'] = 0;
            }

            if ($postId) {
                $post = ForumUtil::updatePost($postId, $data);
            } else {
                $post = ForumUtil::addPost($data);
            }
            $page = ForumUtil::getPostPageInThread($post['id'], $thread['id'], 20);
            ForumUtil::updateThread($thread['id'], ['lastReplyTime' => Carbon::now(), 'lastReplyMemberUserId' => $this->memberUserId()]);

            return Response::send(0, null, null, '/forum/thread/' . $thread['id'] . '?page=' . $page . 'postId=' . $post['id']);
        }

        return $this->_view('forum.postEdit', compact('thread', 'post'));
    }


    public function postDelete($postId)
    {
        if (!$this->memberUserId()) {
            return Response::send(-1, null, null, '/login?redirect=' . urlencode(Request::currentPageUrl()));
        }

        $post = ForumUtil::loadPost($postId);
        if (empty($post)) {
            return Response::send(-1, 'post not found');
        }

        ForumUtil::deletePost($post['id']);

        return Response::send(0, null, null, '/forum/thread/' . $post['threadId']);
    }

}