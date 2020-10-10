<?php


namespace TechSoft\Laravel\User;


use TechOnline\Laravel\Dao\ModelUtil;

class UserUtil
{
    public static function get($id)
    {
        return ModelUtil::get('user', ['id' => $id]);
    }

    public static function update($id, $data)
    {
        return ModelUtil::update('user', ['id' => $id], $data);
    }

    public static function add($data)
    {
        return ModelUtil::insert('user', $data);
    }
}