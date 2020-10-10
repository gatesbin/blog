<?php

namespace TechSoft\Laravel\Forum;

use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Util\TreeUtil;

class ForumUtil
{
    static $categories = null;
    static $treeCategories = null;

    public static function loadCategory($id)
    {
        if (null != self::$categories) {
            foreach (self::$categories as $category) {
                if ($category['id'] == $id) {
                    return $category;
                }
            }
        }
        return ModelUtil::get('forum_category', ['id' => $id]);
    }

    public static function updateCategory($id, $data)
    {
        return ModelUtil::update('forum_category', ['id' => $id], $data);
    }

    public static function getCategories()
    {
        if (null != self::$categories) {
            return self::$categories;
        }
        $categories = ModelUtil::all('forum_category');
        self::$categories = $categories;
        return $categories;
    }

    public static function getChildCategories($categoryId)
    {
        $childCategories = [];
        $categories = self::getCategories();
        foreach ($categories as $category) {
            if ($category['pid'] == $categoryId) {
                $childCategories[] = $category;
            }
        }
        TreeUtil::arraySortByKey($childCategories, 'sort', 'asc');
        return $childCategories;
    }

    public static function getTreeCategories()
    {
        if (null != self::$treeCategories) {
            return self::$treeCategories;
        }
        $categories = self::getCategories();
        self::$treeCategories = TreeUtil::nodeMerge($categories, 0, 'id', 'pid', 'sort');
        return self::$treeCategories;
    }

    public static function getCategoryChildIds($categoryId)
    {
        $categories = self::getCategories();
        return TreeUtil::allChildIds($categories, $categoryId);
    }

    public static function getCategoryTagMap($categoryId)
    {
        $map = [];
        $tags = ModelUtil::all('forum_category_tag', ['categoryId' => $categoryId], ['*'], ['sort', 'asc']);
        foreach ($tags as $tag) {
            $map[$tag['id']] = $tag['title'];
        }
        return $map;
    }

    public static function loadThread($id)
    {
        return ModelUtil::get('forum_thread', ['id' => $id]);
    }

    public static function deleteThread($id)
    {
        $thread = self::loadThread($id);
        if (empty($thread)) {
            return;
        }
        ModelUtil::delete('forum_thread', ['id' => $id]);
        ModelUtil::delete('forum_thread_member_data', ['threadId' => $id]);
        ModelUtil::delete('forum_post', ['threadId' => $id]);

        self::updateCategory($thread['categoryId'], [
            'threadCount' => self::getCategoryThreadCount($thread['categoryId']),
            'postCount' => self::getCategoryPostCount($thread['categoryId'])
        ]);
    }

    public static function addThread($data)
    {
        $thread = ModelUtil::insert('forum_thread', $data);

        self::updateCategory($thread['categoryId'], [
            'threadCount' => self::getCategoryThreadCount($thread['categoryId']),
        ]);

        return $thread;
    }

    public static function updateThread($id, $data)
    {
        return ModelUtil::update('forum_thread', ['id' => $id], $data);
    }

    public static function getLatestThread($limit)
    {
        $list = ModelUtil::model('forum_thread')->limit($limit)->orderBy('id', 'desc')->get()->toArray();
        return $list;
    }

    public static function loadThreadMemberData($threadId, $memberUserId)
    {
        $m = ModelUtil::get('forum_thread_member_data', ['threadId' => $threadId, 'memberUserId' => $memberUserId]);
        if (empty($m)) {
            $m = ModelUtil::insert('forum_thread_member_data', ['threadId' => $threadId, 'memberUserId' => $memberUserId]);
        }
        return $m;
    }

    public static function paginateThreadsByCategory($categoryId, $page, $pageSize, $option)
    {
        $categoryIds = self::getCategoryChildIds($categoryId);
        $categoryIds[] = $categoryId;
        $option['whereIn'] = ['categoryId', $categoryIds];
        return ModelUtil::paginate('forum_thread', $page, $pageSize, $option);
    }

    public static function paginateThreadsByKeywords($keywords, $page, $pageSize, $option)
    {
        $option['whereOperate'] = ['title', 'like', '%' . $keywords . '%'];
        return ModelUtil::paginate('forum_thread', $page, $pageSize, $option);
    }

    public static function paginateThreadsByMemberUserId($memberUserId, $page, $pageSize, $option)
    {
        $option['where']['memberUserId'] = $memberUserId;
        return ModelUtil::paginate('forum_thread', $page, $pageSize, $option);
    }

    public static function paginateMemberFavoriteThreads($memberUserId, $page, $pageSize, $option)
    {
        $option['where']['memberUserId'] = $memberUserId;
        $option['where']['fav'] = 1;
        $paginateData = ModelUtil::paginate('forum_thread_member_data', $page, $pageSize, $option);
        ModelUtil::join($paginateData['records'], 'threadId', '_thread', 'forum_thread', 'id');
        $threads = [];
        foreach ($paginateData['records'] as $record) {
            if (empty($record['_thread'])) {
                continue;
            }
            $threads[] = $record['_thread'];
        }
        return [
            'records' => $threads,
            'total' => $paginateData['total'],
        ];
    }

    public static function paginateMemberUpThreads($memberUserId, $page, $pageSize, $option)
    {
        $option['where']['memberUserId'] = $memberUserId;
        $option['where']['up'] = 1;
        $paginateData = ModelUtil::paginate('forum_thread_member_data', $page, $pageSize, $option);
        ModelUtil::join($paginateData['records'], 'threadId', '_thread', 'forum_thread', 'id');
        $threads = [];
        foreach ($paginateData['records'] as $record) {
            if (empty($record['_thread'])) {
                continue;
            }
            $threads[] = $record['_thread'];
        }
        return [
            'records' => $threads,
            'total' => $paginateData['total'],
        ];
    }

    public static function updateThreadMemberData($threadId, $memberUserId, $data)
    {
        return ModelUtil::update('forum_thread_member_data', ['threadId' => $threadId, 'memberUserId' => $memberUserId], $data);
    }

    public static function addPost($data)
    {
        $post = ModelUtil::insert('forum_post', $data);

        self::updateThread($post['threadId'], [
            'postCount' => self::getThreadPostCount($post['threadId']),
        ]);
        self::updateCategory($post['categoryId'], [
            'postCount' => self::getCategoryPostCount($post['categoryId'])
        ]);

        return $post;
    }

    public static function updatePost($id, $data)
    {
        $post = ModelUtil::update('forum_post', ['id' => $id], $data);
        return $post;
    }

    public static function loadPost($id)
    {
        return ModelUtil::get('forum_post', ['id' => $id]);
    }

    public static function deletePost($id)
    {
        $post = ModelUtil::get('forum_post', ['id' => $id]);
        if (empty($post)) {
            return;
        }
        ModelUtil::delete('forum_post', ['id' => $id]);
        ModelUtil::update('forum_post', ['replyPostId' => $id], ['replyPostId' => 0]);

        self::updateThread($post['threadId'], [
            'postCount' => self::getThreadPostCount($post['threadId']),
        ]);
        self::updateCategory($post['categoryId'], [
            'postCount' => self::getCategoryPostCount($post['categoryId'])
        ]);

    }

    public static function paginateThreadPost($threadId, $page, $pageSize, $option)
    {
        $option['where']['threadId'] = $threadId;
        return ModelUtil::paginate('forum_post', $page, $pageSize, $option);
    }

    public static function getPostPageInThread($postId, $threadId, $pageSize = 10)
    {
        $posts = ModelUtil::model('forum_post')->select('id')->where(['threadId' => $threadId])->orderBy('id', 'asc')->get();
        foreach ($posts as $i => &$post) {
            if ($postId == $post->id) {
                return ceil(($i + 1) / $pageSize);
            }
        }
        return 1;
    }

    public static function getThreadPostCount($threadId)
    {
        return intval(ModelUtil::count('forum_post', ['threadId' => $threadId]));
    }

    public static function getCategoryPostCount($categoryId)
    {
        $categoryIds = self::getCategoryChildIds($categoryId);
        $categoryIds[] = $categoryId;
        return intval(ModelUtil::model('forum_post')->whereIn('categoryId', $categoryIds)->count());
    }

    public static function getCategoryThreadCount($categoryId)
    {
        $categoryIds = self::getCategoryChildIds($categoryId);
        $categoryIds[] = $categoryId;
        return intval(ModelUtil::model('forum_thread')->whereIn('categoryId', $categoryIds)->count());
    }

    public static function getBanners()
    {
        return ModelUtil::all('forum_banner');
    }

    public static function isCategoryAdmin($memberUserId, $categoryId)
    {
        $categories = self::getCategories();
        $adminCategoryIds = ModelUtil::values('forum_category_admin', 'categoryId', ['memberUserId' => $memberUserId]);
        if (in_array($categoryId, $adminCategoryIds)) {
            return true;
        }
        $currentCategoryId = $categoryId;
        $limit = 0;
        while ($currentCategoryId && $limit++ < 999) {
            foreach ($categories as $category) {
                if ($category['id'] == $currentCategoryId) {
                    $currentCategoryId = $category['pid'];
                    if (empty($currentCategoryId)) {
                        return false;
                    }
                    if (in_array($currentCategoryId, $adminCategoryIds)) {
                        return true;
                    }
                    break;
                }
            }
        }
        return false;
    }

    public static function getMemberThreadCount($memberUserId)
    {
        return ModelUtil::count('forum_thread', ['memberUserId' => $memberUserId]);
    }

    public static function getMemberPostCount($memberUserId)
    {
        return ModelUtil::count('forum_post', ['memberUserId' => $memberUserId]);
    }

    public static function paginateMemberThreads($memberUserId, $page, $pageSize, $option)
    {
        $option['where'] = ['memberUserId' => $memberUserId];
        return ModelUtil::paginate('forum_thread', $page, $pageSize, $option);
    }

    public static function paginateMemberPosts($memberUserId, $page, $pageSize, $option)
    {
        $option['where'] = ['memberUserId' => $memberUserId];
        return ModelUtil::paginate('forum_post', $page, $pageSize, $option);
    }

}