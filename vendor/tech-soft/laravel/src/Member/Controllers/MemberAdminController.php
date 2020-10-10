<?php

namespace TechSoft\Laravel\Member\Controllers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Admin\Cms\BasicCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Support\AdminCheckController;
use TechSoft\Laravel\Admin\Util\AdminPowerUtil;
use TechSoft\Laravel\Member\Events\MemberUserRegisteredEvent;
use TechSoft\Laravel\Member\MemberUtil;

class MemberAdminController extends AdminCheckController
{
    protected $cmsConfigData = [
        'model' => 'member_user',
        'pageTitle' => '用户列表',
        'group' => 'data',
        'canView' => true,
        'canAdd' => true,
        'canExport' => true,
        'fields' => [
            'created_at' => ['type' => FieldText::class, 'title' => '加入时间', 'list' => true, 'export' => true,],
            'avatar' => ['type' => FieldImage::class, 'title' => '头像', 'list' => true,],
            'username' => ['type' => FieldText::class, 'title' => '用户名', 'list' => true, 'search' => true, 'add' => true, 'export' => true,],
            'email' => ['type' => FieldText::class, 'title' => '邮箱', 'list' => true, 'search' => true, 'add' => true, 'export' => true,],
            'phone' => ['type' => FieldText::class, 'title' => '手机', 'list' => true, 'search' => true, 'add' => true, 'export' => true,],
            'password' => ['type' => FieldText::class, 'title' => '密码', 'add' => true,],
        ]
    ];

    public function dataList(BasicCms $basicCms)
    {
        return $basicCms->executeList($this, $this->cmsConfigData);
    }

    public function dataAdd(BasicCms $basicCms)
    {
        if (Request::isPost()) {

            if (AdminPowerUtil::isDemo()) {
                return AdminPowerUtil::demoResponse();
            }
            $ret = MemberUtil::register(
                Input::get('username'),
                Input::get('phone'),
                Input::get('email'),
                Input::get('password')
            );

            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }

            $memberUser = $ret['data'];
            if (method_exists($this, 'hookMemberRegistered')) {
                $this->hookMemberRegistered($memberUser);
            }

            Event::fire(new MemberUserRegisteredEvent($memberUser['id']));

            return Response::send(0, null, null, '[js]parent.api.dialog.dialogClose(parent.__dialogData.add);');

        }
        return $basicCms->executeAdd($this, $this->cmsConfigData);
    }

    public function dataEdit(BasicCms $basicCms)
    {
        return $basicCms->executeEdit($this, $this->cmsConfigData);
    }

    public function dataDelete(BasicCms $basicCms)
    {
        return $basicCms->executeDelete($this, $this->cmsConfigData);
    }

    public function dataView($memberUserId = 0)
    {
        if (AdminPowerUtil::isDemo()) {
            return AdminPowerUtil::demoResponse();
        }

        if (empty($memberUserId)) {
            $memberUserId = Input::get('_id');
        }
        if (empty($memberUserId)) {
            $memberUserId = Input::get('id');
        }
        $memberUser = MemberUtil::get($memberUserId);
        if (empty($memberUser)) {
            return Response::send(-1, '用户为空');
        }
        if (Request::isPost()) {

            $update = [];
            $username = trim(Input::get('username'));
            if ($memberUser['username'] != $username) {
                $update['username'] = $username;
            }
            $phone = trim(Input::get('phone'));
            if ($memberUser['phone'] != $phone) {
                $update['phone'] = $phone;
            }
            $email = trim(Input::get('email'));
            if ($memberUser['email'] != $email) {
                $update['email'] = $email;
            }
            if (!empty($update)) {
                MemberUtil::update($memberUser['id'], $update);
            }

            $resetPassword = trim(Input::get('resetPassword'));
            if ($resetPassword) {
                MemberUtil::changePassword($memberUserId, $resetPassword, null, true);
                return Response::send(0, '密码已经成功修改为"' . $resetPassword . '"', null, '[js]$.dialogClose();');
            } else {
                return Response::send(0, null, null, '[js]$.dialogClose();');
            }
        }
        return view('soft::member.admin.view', compact('memberUser', 'memberUserId'));
    }

    public function dataExport(BasicCms $basicCms)
    {
        return $basicCms->executeExport($this, $this->cmsConfigData);
    }

    public function enter($id)
    {
        Session::put('memberUserId', $id);
        return Response::send(0, null, null, '/');
    }

}