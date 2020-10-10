<?php

namespace TechSoft\Laravel\Member;

use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Response;
use TechOnline\Utils\EncodeUtil;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use TechSoft\Laravel\Data\DataUtil;

class MemberUtil
{

    public static function get($id)
    {
        return ModelUtil::get('member_user', ['id' => $id]);
    }

    public static function getViewName($id)
    {
        $one = self::get($id);
        if (!empty($one['nickname'])) {
            return $one['nickname'];
        }
        if (!empty($one['username'])) {
            return $one['username'];
        }
        return "ID-$one[id]";
    }

    public static function viewName($memberUser)
    {
        if (!empty($memberUser['nickname'])) {
            return $memberUser['nickname'];
        }
        if (!empty($memberUser['username'])) {
            return $memberUser['username'];
        }
        return "ID-$memberUser[id]";
    }

    public static function update($id, $data)
    {
        return ModelUtil::update('member_user', ['id' => $id], $data);
    }

    public static function updateBasicWithUniqueCheck($id, $data)
    {
        if (empty($data)) {
            return Response::generate(0, 'ok');
        }
        foreach (['username' => '用户名', 'phone' => '手机', 'email' => '邮箱',] as $field => $fieldTitle) {
            if (isset($data[$field])) {
                $exists = ModelUtil::all('member_user', [$field => $data[$field]]);
                if (count($exists) > 1) {
                    return Response::generate(-1, $fieldTitle . '重复');
                }
                if (count($exists) == 1) {
                    if ($exists[0]['id'] != $id) {
                        return Response::generate(-1, $fieldTitle . '重复');
                    }
                }
            }
        }
        self::update($id, $data);
        return Response::generate(0, 'ok');
    }

    
    public static function login($username = '', $phone = '', $email = '', $password = '')
    {
        $email = trim($email);
        $phone = trim($phone);
        $username = trim($username);

        if (!($email || $phone || $username)) {
            return Response::generate(-1, '所有登录字段均为空');
        }
        if (!$password) {
            return Response::generate(-2, '密码为空');
        }

        if ($email) {
            if (!preg_match('/(^[\w-.]+@[\w-]+\.[\w-.]+$)/', $email)) {
                return Response::generate(-3, '邮箱格式不正确');
            }
            $where = array(
                'email' => $email
            );
        } else if ($phone) {
            if (!preg_match('/(^1[0-9]{10}$)/', $phone)) {
                return Response::generate(-4, '手机格式不正确');
            }
            $where = array(
                'phone' => $phone
            );
        } else if ($username) {
            if (strpos($username, '@') !== false) {
                return Response::generate(-5, '用户名格式不正确');
            }
            $where = array(
                'username' => $username
            );
        }

        $memberUser = ModelUtil::get('member_user', $where);
        if (empty($memberUser)) {
            return Response::generate(-6, '登录失败:用户名或密码错误');
        }

        if ($memberUser['password'] != EncodeUtil::md5WithSalt($password, $memberUser['passwordSalt'])) {
            return Response::generate(-7, '登录失败:用户名或密码错误');
        }

        return Response::generate(0, 'ok', $memberUser);
    }

    
    public static function register($username = '', $phone = '', $email = '', $password = '', $ignorePassword = false)
    {
        $email = trim($email);
        $phone = trim($phone);
        $username = trim($username);

        if (!($email || $phone || $username)) {
            return Response::generate(-1, '所有注册字段均为空');
        }

        if ($email) {
            $ret = self::uniqueCheck('email', $email);
            if ($ret['code']) {
                return $ret;
            }
        } else {
            $email = null;
        }
        if ($phone) {
            $ret = self::uniqueCheck('phone', $phone);
            if ($ret['code']) {
                return $ret;
            }
        } else {
            $phone = null;
        }
        if ($username) {
            $ret = self::uniqueCheck('username', $username);
            if ($ret['code']) {
                return $ret;
            }
                        if (Str::contains($username, '@')) {
                return Response::generate(-1, '用户名不能包含特殊字符');
            }
                        if (preg_match('/^[0-9]{11}$/', $username)) {
                return Response::generate(-1, '用户名不能为纯数字');
            }
        } else {
            $username = null;
        }
        if (!$ignorePassword) {
            if (empty($password) || strlen($password) < 6) {
                return Response::generate(-3, '密码不合法');
            }
        }

        $passwordSalt = Str::random(16);

        $memberUser = ModelUtil::insert('member_user', [
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'password' => $ignorePassword ? null : EncodeUtil::md5WithSalt($password, $passwordSalt),
            'passwordSalt' => $ignorePassword ? null : $passwordSalt,
        ]);

        return Response::generate(0, 'ok', $memberUser);
    }

    
    public static function uniqueCheck($type, $value, $ignoreUserId = 0)
    {
        $value = trim($value);
        switch ($type) {
            case 'email' :
                if (!preg_match('/(^[\w-.]+@[\w-]+\.[\w-.]+$)/', $value)) {
                    return Response::generate(-1, '邮箱格式不正确');
                }
                break;
            case 'phone' :
                if (!preg_match('/(^1[0-9]{10}$)/', $value)) {
                    return Response::generate(-1, '手机格式不正确');
                }
                break;
            case 'username' :
                if (strpos($value, '@') !== false) {
                    return Response::generate(-1, '用户名格式不正确');
                }
                break;
            default :
                return Response::generate(-1, '未能识别的类型' . $type);
        }

        $memberUser = ModelUtil::get('member_user', [$type => $value]);
        if (empty ($memberUser)) {
            return Response::generate(0, 'ok');
        }

        $lang = array(
            'username' => '用户名',
            'email' => '邮箱',
            'phone' => '手机号'
        );
        if ($ignoreUserId == $memberUser['id']) {
            return Response::generate(0, 'ok');
        }
        return Response::generate(-2, $lang [$type] . '已经被占用');
    }


    public static function getByUsername($username)
    {
        return ModelUtil::get('member_user', ['username' => $username]);
    }

    public static function getByEmail($email)
    {
        return ModelUtil::get('member_user', ['email' => $email]);
    }

    public static function getByPhone($phone)
    {
        return ModelUtil::get('member_user', ['phone' => $phone]);
    }

    
    public static function changePassword($memberUserId, $new, $old = null, $ignoreOld = false)
    {
        if (!$ignoreOld && empty($old)) {
            return Response::generate(-1, '旧密码不能为空');
        }

        $memberUser = ModelUtil::get('member_user', ['id' => $memberUserId]);
        if (empty($memberUser)) {
            return Response::generate(-1, "用户不存在");
        }
        if (empty ($new)) {
            return Response::generate(-1, '新密码为空');
        }
        if (!$ignoreOld && EncodeUtil::md5WithSalt($old, $memberUser['passwordSalt']) != $memberUser['password']) {
            return Response::generate(-1, '旧密码不正确');
        }

        $passwordSalt = Str::random(16);

        ModelUtil::update('member_user', ['id' => $memberUser['id']], [
            'passwordSalt' => $passwordSalt,
            'password' => EncodeUtil::md5WithSalt($new, $passwordSalt)
        ]);

        return Response::generate(0, 'ok');
    }

    
    public static function setAvatar($userId, $avatarData, $avatarExt = 'jpg')
    {
        $memberUser = self::get($userId);
        if (empty($memberUser)) {
            return Response::generate(-1, '用户不存在');
        }
        if (empty($avatarData)) {
            return Response::generate(-1, '图片数据为空');
        }
        $imageBig = (string)Image::make($avatarData)->resize(400, 400)->encode($avatarExt, 75);
        $imageMedium = (string)Image::make($avatarData)->resize(200, 200)->encode($avatarExt, 75);
        $image = (string)Image::make($avatarData)->resize(50, 50)->encode($avatarExt, 75);

        $retBig = DataUtil::upload('image', 'uid_' . $userId . '_avatar_big.' . $avatarExt, $imageBig);
        if ($retBig['code']) {
            return Response::generate(-1, '头像存储失败（' . $retBig['msg'] . '）');
        }
        $retMedium = DataUtil::upload('image', 'uid_' . $userId . '_avatar_middle.' . $avatarExt, $imageMedium);
        if ($retMedium['code']) {
            DataUtil::deleteById($retBig['data']['id']);
            if ($retBig['code']) {
                return Response::generate(-1, '头像存储失败（' . $retMedium['msg'] . '）');
            }
        }
        $ret = DataUtil::upload('image', 'uid_' . $userId . '_avatar.' . $avatarExt, $image);
        if ($ret['code']) {
            DataUtil::deleteById($retBig['data']['id']);
            DataUtil::deleteById($retMedium['data']['id']);
            if ($retBig['code']) {
                return Response::generate(-1, '头像存储失败（' . $ret['msg'] . '）');
            }
        }

        $avatarBig = $retBig['data']['fullPath'];
        $avatarMedium = $retMedium['data']['fullPath'];
        $avatar = $ret['data']['fullPath'];

        self::update($memberUser['id'], [
            'avatarBig' => $avatarBig,
            'avatarMedium' => $avatarMedium,
            'avatar' => $avatar
        ]);

        return Response::generate(0, 'ok');
    }







    public static function getIdByOauth($oauthType, $openId)
    {
        $m = ModelUtil::get('member_oauth', ['type' => $oauthType, 'openId' => $openId]);
        if (empty($m)) {
            return 0;
        }
        return intval($m['memberUserId']);
    }

    public static function getOauthOpenId($memberUserId, $oauthType)
    {
        $where = ['memberUserId' => $memberUserId, 'type' => $oauthType];
        $m = ModelUtil::get('member_oauth', $where);
        if (empty($m)) {
            return null;
        }
        return $m['openId'];
    }

    public static function putOauth($memberUserId, $oauthType, $openId)
    {
        $where = ['memberUserId' => $memberUserId, 'type' => $oauthType];
        $m = ModelUtil::get('member_oauth', $where);
        if (empty($m)) {
            ModelUtil::insert('member_oauth', array_merge($where, ['openId' => $openId]));
        } else if ($m['openId'] != $openId) {
            ModelUtil::update('member_oauth', ['id' => $m['id']], ['openId' => $openId]);
        }
    }

    public static function forgetOauth($oauthType, $openId)
    {
        ModelUtil::delete('member_oauth', ['type' => $oauthType, 'openId' => $openId]);
    }


}
