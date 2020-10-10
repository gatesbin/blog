<?php

namespace TechSoft\Laravel\MemberFavorite;

use TechOnline\Laravel\Dao\ModelUtil;

class MemberFavoriteUtil
{
    public static function add($userId, $category, $categoryId)
    {
        $m = ModelUtil::get('member_favorite', ['userId' => $userId, 'category' => $category, 'categoryId' => $categoryId]);
        if (empty($m)) {
            ModelUtil::insert('member_favorite', [
                'userId' => $userId, 'category' => $category, 'categoryId' => $categoryId
            ]);
        }
    }

    public static function delete($userId, $category, $categoryId)
    {
        ModelUtil::delete('member_favorite', ['userId' => $userId, 'category' => $category, 'categoryId' => $categoryId]);
    }

    public static function exists($userId, $category, $categoryId)
    {
        return ModelUtil::exists('member_favorite', ['userId' => $userId, 'category' => $category, 'categoryId' => $categoryId]);
    }

    public static function paginate($userId, $category, $page, $pageSize, $option = [])
    {
        $option['where']['userId'] = $userId;
        $option['where']['category'] = $category;
        return ModelUtil::paginate('member_favorite', $page, $pageSize, $option);
    }

}