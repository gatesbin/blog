<?php

namespace TechSoft\Laravel\MemberMessage;

use TechOnline\Laravel\Dao\ModelUtil;

class MemberMessageUtil
{

    public static function getUnreadMessageCount($userId)
    {
        return ModelUtil::count('member_message', ['userId' => $userId, 'status' => MemberMessageStatus::UNREAD]);
    }

    public static function paginate($userId, $page, $pageSize, $option = [])
    {
        $option['where']['userId'] = $userId;
        $paginateData = ModelUtil::paginate('member_message', $page, $pageSize, $option);
        $records = [];
        foreach ($paginateData['records'] as $record) {
            $item = [];
            $item['id'] = $record['id'];
            $item['status'] = $record['status'];
            $item['fromId'] = $record['fromId'];
            $item['content'] = $record['content'];
            $item['createTime'] = $record['created_at'];
            $records[] = $item;
        }
        return [
            'records' => $records,
            'total' => $paginateData['total'],
        ];
    }

    public static function delete($userId, $ids = [])
    {
        if (empty($ids)) {
            return;
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        ModelUtil::model('member_message')->whereIn('id', $ids)->where(['userId' => $userId])->delete();
    }

    public static function update($userId, $ids = [], $update = [])
    {
        if (empty($ids) || empty($update)) {
            return;
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        ModelUtil::model('member_message')->whereIn('id', $ids)->where(['userId' => $userId])->update($update);
    }

    public static function updateRead($userId, $ids = [])
    {
        self::update($userId, $ids, ['status' => MemberMessageStatus::READ]);
    }

    public static function updateReadAll($userId)
    {
        ModelUtil::model('member_message')->where(['userId' => $userId])->update(['status' => MemberMessageStatus::READ]);
    }

    public static function send($userId, $content, $fromId = 0)
    {
        ModelUtil::add('member_message', [
            'userId' => $userId,
            'fromId' => $fromId,
            'status' => MemberMessageStatus::UNREAD,
            'content' => $content,
        ]);
        return Response::generate(0, null);
    }

}