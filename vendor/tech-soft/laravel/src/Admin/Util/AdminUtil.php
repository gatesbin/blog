<?php

namespace TechSoft\Laravel\Admin\Util;

use Carbon\Carbon;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use TechSoft\Laravel\Admin\Type\AdminLogType;

class AdminUtil
{
    public static function passwordEncrypt($password, $passwordSalt)
    {
        return md5(md5($password) . md5($passwordSalt));
    }

    public static function ruleChanged($id, $ruleChanged)
    {
        ModelUtil::update('admin_user', ['id' => $id], ['ruleChanged' => boolval($ruleChanged)]);
    }

    public static function login($username, $password)
    {
        $adminUser = ModelUtil::get('admin_user', ['username' => $username]);
        if (empty($adminUser)) {
            return Response::generate(-1, "用户不存在");
        }
        if ($adminUser['password'] != self::passwordEncrypt($password, $adminUser['passwordSalt'])) {
            return Response::generate(-2, "密码不正确");
        }
        return Response::generate(0, 'ok', $adminUser);
    }

    public static function add($username, $password, $ignorePassword = false)
    {
        $passwordSalt = Str::random(16);
        $data = [];
        $data['username'] = $username;
        if (!$ignorePassword) {
            $data['passwordSalt'] = $passwordSalt;
            $data['password'] = self::passwordEncrypt($password, $passwordSalt);
        }
        return ModelUtil::insert('admin_user', $data);
    }

    public static function getRolesByUserId($userId)
    {
        $adminUser = ModelUtil::get('admin_user', $userId);
        if (empty($adminUser)) {
            return Response::generate(-1, "用户不存在");
        }
        $roles = ModelUtil::all('admin_user_role', ['userId' => $userId], ['roleId']);
        ModelUtil::join($roles, 'roleId', 'role', 'admin_role', 'id');
        foreach ($roles as $k => $role) {
            $roles[$k]['name'] = $role['role']['name'];
        }
        ModelUtil::joinAll($roles, 'roleId', 'rules', 'admin_role_rule', 'roleId');
        return Response::generate(0, null, $roles);
    }

    public static function get($id)
    {
        return ModelUtil::get('admin_user', ['id' => $id]);
    }

    public static function getByUsername($username)
    {
        return ModelUtil::get('admin_user', ['username' => $username]);
    }

    public static function changePwd($id, $old, $new, $ignoreOld = false)
    {
        $adminUser = ModelUtil::get('admin_user', ['id' => $id]);
        if (empty($adminUser)) {
            return Response::generate(-1, '用户不存在');
        }
        if ($adminUser['password'] != self::passwordEncrypt($old, $adminUser['passwordSalt'])) {
            if (!$ignoreOld) {
                return Response::generate(-1, '旧密码不正确');
            }
        }

        $passwordSalt = Str::random(16);

        $data = [];
        $data['password'] = self::passwordEncrypt($new, $passwordSalt);
        $data['passwordSalt'] = $passwordSalt;
        $data['lastChangePwdTime'] = Carbon::now();

        ModelUtil::update('admin_user', ['id' => $adminUser['id']], $data);

        return Response::generate(0, 'ok');
    }

    public static function addInfoLog($adminUserId, $summary, $content = [])
    {
        static $exists = null;
        if (null === $exists) {
            $exists = Schema::hasTable('admin_log');
        }
        if (!$exists) {
            return;
        }
        $adminLog = ModelUtil::insert('admin_log', ['adminUserId' => $adminUserId, 'type' => AdminLogType::INFO, 'summary' => $summary]);
        if (!empty($content)) {
            ModelUtil::insert('admin_log_data', ['id' => $adminLog['id'], 'content' => json_encode($content)]);
        }
    }

    public static function addErrorLog($adminUserId, $summary, $content = [])
    {
        static $exists = null;
        if (null === $exists) {
            $exists = Schema::hasTable('admin_log');
        }
        if (!$exists) {
            return;
        }
        $adminLog = ModelUtil::insert('admin_log', ['adminUserId' => $adminUserId, 'type' => AdminLogType::ERROR, 'summary' => $summary]);
        if (!empty($content)) {
            ModelUtil::insert('admin_log_data', ['id' => $adminLog['id'], 'content' => json_encode($content)]);
        }
    }

    public static function addInfoLogIfChanged($adminUserId, $summary, $old, $new)
    {
        $changed = [];
        if (empty($old) && empty($new)) {
            return;
        }
        foreach ($old as $k => $oldValue) {
            if (!array_key_exists($k, $new)) {
                $changed['删除:' . $k . ':原值'] = $oldValue;
                continue;
            }
            if ($new[$k] != $oldValue) {
                $changed['修改:' . $k . ':原值'] = $oldValue;
                continue;
            }
        }
        foreach ($new as $k => $newValue) {
            if (!array_key_exists($k, $old)) {
                $changed['新增:' . $k . ':新值'] = $newValue;
                continue;
            }
        }
        if (empty($changed)) {
            return;
        }
        self::addInfoLog($adminUserId, $summary, $changed);
    }

}
