<?php

namespace TechSoft\Laravel\Admin\Controller;


use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Response;
use TechOnline\Utils\ArrayUtil;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use TechSoft\Laravel\Admin\Support\AdminCheckController;
use TechSoft\Laravel\Admin\Type\AdminLogType;
use TechSoft\Laravel\Admin\Util\AdminPowerUtil;
use TechSoft\Laravel\Admin\Util\AdminUtil;

class SystemController extends AdminCheckController
{
    public function clearCache()
    {
        if (AdminPowerUtil::isDemo()) {
            return AdminPowerUtil::demoResponse();
        }

        AdminUtil::addInfoLog($this->adminUserId(), '清除缓存');

        $exitCode = Artisan::call("cache:clear");
        if (0 != $exitCode) {
            return Response::send(-1, "清除缓存失败 cache exitCode($exitCode)");
        }

        $exitCode = Artisan::call("view:clear");
        if (0 != $exitCode) {
            return Response::send(-1, "清除缓存失败 view exitCode($exitCode)");
        }

        return Response::send(0, '操作成功');
    }

    public function changePwd()
    {
        if (Request::isMethod('post')) {

            if (AdminPowerUtil::isDemo()) {
                return AdminPowerUtil::demoResponse();
            }

            $input = InputPackage::buildFromInput();

            $passwordOld = $input->getTrimString('passwordOld');
            $passwordNew = $input->getTrimString('passwordNew');
            $passwordNewRepeat = $input->getTrimString('passwordNewRepeat');

            if ($passwordNew != $passwordNewRepeat) {
                return Response::send(-1, '两次新密码不一致');
            }

            $ret = AdminUtil::changePwd($this->adminUserId(), $passwordOld, $passwordNew);
            if ($ret['code']) {
                return Response::send(-1, $ret['msg']);
            }

            AdminUtil::addInfoLog($this->adminUserId(), '修改密码');

            return Response::send(0, '密码修改成功', null, '[reload]');
        }

        return view('admin::system.changePwd');
    }

    public function userRoleList()
    {
        if (Request::isMethod('post')) {

            $page = Input::get('page');
            $pageSize = 20;
            $option = [];

            $option['order'] = ['id', 'desc'];

            $head = [];
            $head[] = ['field' => 'name', 'title' => '角色',];
            $head[] = ['field' => 'users', 'title' => '用户',];
            $head[] = ['field' => '_operation', 'title' => '操作'];

            $paginateData = ModelUtil::paginate('admin_role', $page, $pageSize, $option);

            $list = [];
            foreach ($paginateData['records'] as $record) {

                $userRoles = ModelUtil::all('admin_user_role', ['roleId' => $record['id']]);
                $users = [];
                foreach ($userRoles as $userRole) {
                    $user = ModelUtil::get('admin_user', ['id' => $userRole['userId']]);
                    if (empty($user)) {
                        continue;
                    }
                    $users[] = $user['username'];
                }

                $operation = [];
                if (AdminPowerUtil::permit('\TechSoft\Laravel\Admin\Controller\SystemController@userRoleEdit')) {
                    $operation[] = '<a href="#" data-dialog-request="' . action('\TechSoft\Laravel\Admin\Controller\SystemController@userRoleEdit', ['id' => $record['id']]) . '">编辑</a>';
                }
                if (AdminPowerUtil::permit('\TechSoft\Laravel\Admin\Controller\SystemController@userRoleDelete')) {
                    $operation[] = '<a href="#" data-confirm="确认删除" data-ajax-request="' . action('\TechSoft\Laravel\Admin\Controller\SystemController@userRoleDelete', ['id' => $record['id']]) . '">删除</a>';
                }

                $item = [
                    '_id' => $record['id'],
                    'name' => $record['name'],
                    'users' => join(',', $users),
                    '_operation' => join(" - ", $operation)
                ];
                $list[] = $item;
            }

            $data = [];
            $data['head'] = $head;
            $data['list'] = $list;
            $data['total'] = $paginateData['total'];
            $data['pageSize'] = $pageSize;
            $data['page'] = $page;

            return Response::json(0, null, $data);
        }

        return view('admin::system.role.list');
    }

    public function userRoleEdit($id = 0)
    {
        $role = ModelUtil::get('admin_role', ['id' => $id]);

        if (Request::isMethod('post')) {

            if (AdminPowerUtil::isDemo()) {
                return AdminPowerUtil::demoResponse();
            }

            $data = [];
            $data['name'] = trim(Input::get('name'));

            if (empty($data['name'])) {
                return Response::send(-1, '角色名称不能为空');
            }

            $rules = Input::get('rules');
            if (!is_array($rules)) {
                $rules = [];
            }
            if (empty($rules)) {
                return Response::send(-1, '角色权限不能为空');
            }

            if ($role) {
                $adminRole = ModelUtil::get('admin_role', ['id' => $role['id']]);
                AdminUtil::addInfoLogIfChanged($this->adminUserId(), '修改管理员角色', [
                    '名称' => $adminRole['name'],
                ], [
                    '名称' => $data['name']
                ]);
                $adminRoleRules = ModelUtil::all('admin_role_rule', ['roleId' => $adminRole['id']]);
                $adminRoleRules = ArrayUtil::fetchSpecifiedKeyToArray($adminRoleRules, 'rule');
                if (!ArrayUtil::sequenceEqual($adminRoleRules, $rules)) {
                    AdminUtil::addInfoLog($this->adminUserId(), '修改管理员角色权限');
                }
                $adminRoleId = $adminRole['id'];
                ModelUtil::update('admin_role', ['id' => $role['id']], $data);
            } else {
                $adminRole = ModelUtil::insert('admin_role', $data);
                $adminRoleId = $adminRole['id'];
                AdminUtil::addInfoLog($this->adminUserId(), '增加管理员角色', [
                    '名称' => $data['name']
                ]);
            }
            ModelUtil::delete('admin_role_rule', ['roleId' => $adminRoleId]);
            foreach ($rules as $rule) {
                ModelUtil::insert('admin_role_rule', ['roleId' => $adminRoleId, 'rule' => $rule]);
            }

            ModelUtil::model('admin_user')->whereNotNull('id')->update(['ruleChanged' => true]);

            return Response::send(0, null, null, '[js]$.dialogClose()');
        }

        $rules = [];
        if ($role) {
            $roleRules = ModelUtil::all('admin_role_rule', ['roleId' => $role['id']]);
            foreach ($roleRules as $roleRule) {
                $rules[$roleRule['rule']] = true;
            }
        }

        $viewData = [];
        $viewData['role'] = $role;
        $viewData['rules'] = $rules;
        $viewData['powers'] = AdminPowerUtil::rules('powerList');
        return view('admin::system.role.edit', $viewData);
    }

    public function userRoleDelete($id = 0)
    {
        if (AdminPowerUtil::isDemo()) {
            return AdminPowerUtil::demoResponse();
        }

        $adminUserRole = ModelUtil::get('admin_role', ['id' => $id]);
        if (empty($adminUserRole)) {
            return Response::send(-1, '记录不存在');
        }

        ModelUtil::delete('admin_user_role', ['roleId' => $id]);
        ModelUtil::delete('admin_role_rule', ['roleId' => $id]);
        ModelUtil::delete('admin_role', ['id' => $id]);

        ModelUtil::model('admin_user')->whereNotNull('id')->update(['ruleChanged' => true]);

        AdminUtil::addInfoLog($this->adminUserId(), '删除管理员角色', [
            '名称' => $adminUserRole['name'],
        ]);

        return Response::send(0, null, null, '[js]window.lister.load(false);');
    }

    public function userList()
    {
        if (Request::isMethod('post')) {

            $page = Input::get('page');
            $pageSize = 20;
            $option = [];

            $option['order'] = ['id', 'desc'];

            $head = [];
            $head[] = ['field' => 'username', 'title' => '用户',];
            $head[] = ['field' => 'roles', 'title' => '角色',];
            $head[] = ['field' => '_operation', 'title' => '操作'];

            $paginateData = ModelUtil::paginate('admin_user', $page, $pageSize, $option);

            $list = [];
            foreach ($paginateData['records'] as $record) {

                $adminRoles = AdminUtil::getRolesByUserId($record['id']);

                $roles = [];
                foreach ($adminRoles['data'] as $adminRole) {
                    $roles[] = $adminRole['name'];
                }

                $operation = [];

                if (AdminPowerUtil::permit('\TechSoft\Laravel\Admin\Controller\SystemController@userEdit')) {
                    $operation[] = '<a href="#" data-dialog-request="' . action('\TechSoft\Laravel\Admin\Controller\SystemController@userEdit', ['id' => $record['id']]) . '">编辑</a>';
                }

                if (AdminPowerUtil::permit('\TechSoft\Laravel\Admin\Controller\SystemController@userDelete')) {
                    $operation[] = '<a href="#" data-confirm="确认删除" data-ajax-request="' . action('\TechSoft\Laravel\Admin\Controller\SystemController@userDelete', ['id' => $record['id']]) . '">删除</a>';
                }

                if ($record['id'] == env('ADMIN_FOUNDER_ID', 1)) {
                    $operation = [];
                    $roles = ['创建者'];
                }

                $item = [
                    '_id' => $record['id'],
                    'username' => $record['username'],
                    'roles' => join('，', $roles),
                    '_operation' => join(' - ', $operation),
                ];
                $list[] = $item;
            }

            $data = [];
            $data['head'] = $head;
            $data['list'] = $list;
            $data['total'] = $paginateData['total'];
            $data['pageSize'] = $pageSize;
            $data['page'] = $page;

            return Response::json(0, null, $data);
        }
        return view('admin::system.user.list');
    }

    public function userEdit($id = 0)
    {

        if ($id == env('ADMIN_FOUNDER_ID', 1)) {
            return Response::send(-1, '创建者不能修改');
        }

        $adminUser = ModelUtil::get('admin_user', ['id' => $id]);
        $roles = ModelUtil::all('admin_role', []);
        $adminUserRoleIds = [];
        if ($adminUser) {
            $adminUserRoles = ModelUtil::all('admin_user_role', ['userId' => $adminUser['id']]);
            foreach ($adminUserRoles as $adminUserRole) {
                $adminUserRoleIds[] = $adminUserRole['roleId'];
            }
        }

        if (Request::isMethod('post')) {

            if (AdminPowerUtil::isDemo()) {
                return AdminPowerUtil::demoResponse();
            }

            $username = trim(Input::get('username'));
            $password = trim(Input::get('password'));
            $roles = Input::get('roles', []);
            if (!is_array($roles)) {
                $roles = [];
            }

            $data = [];
            $data['name'] = trim(Input::get('name'));

            if (empty($username)) {
                return Response::send(-1, '用户名称不能为空');
            }

            if ($adminUserExists = ModelUtil::get('admin_user', ['username' => $username])) {
                if (empty($adminUser) || $adminUserExists['id'] != $adminUser['id']) {
                    return Response::send(-1, '当前用户已经存在');
                }
            }

            if ($adminUser) {
                $adminUserId = $adminUser['id'];
                ModelUtil::update('admin_user', ['id' => $adminUser['id']], ['username' => $username]);
                AdminUtil::addInfoLogIfChanged($this->adminUserId(), '修改管理员', [
                    '用户名' => $adminUser['username'],
                ], [
                    '用户名' => $username,
                ]);
                if ($password) {
                    AdminUtil::changePwd($adminUser['id'], null, $password, true);
                    AdminUtil::addInfoLog($this->adminUserId(), '修改管理员密码', [
                        '用户名' => $username,
                    ]);
                }
            } else {
                if (empty($password)) {
                    return Response::send(-1, '密码不能为空');
                }
                AdminUtil::addInfoLog($this->adminUserId(), '添加管理员', [
                    '用户名' => $username,
                ]);
                $adminUser = ModelUtil::insert('admin_user', ['username' => $username]);
                $adminUserId = $adminUser['id'];
                AdminUtil::changePwd($adminUser['id'], null, $password, true);
            }

            ModelUtil::delete('admin_user_role', ['userId' => $adminUserId]);
            foreach ($roles as $role) {
                ModelUtil::insert('admin_user_role', ['roleId' => $role, 'userId' => $adminUserId]);
            }

            ModelUtil::model('admin_user')->whereNotNull('id')->update(['ruleChanged' => true]);

            return Response::send(0, null, null, '[js]$.dialogClose()');
        }

        $viewData = [];
        $viewData['adminUser'] = $adminUser;
        $viewData['roles'] = $roles;
        $viewData['adminUserRoleIds'] = $adminUserRoleIds;
        return view('admin::system.user.edit', $viewData);
    }

    public function userDelete($id = 0)
    {
        if (AdminPowerUtil::isDemo()) {
            return AdminPowerUtil::demoResponse();
        }

        if ($id == env('ADMIN_FOUNDER_ID', 1)) {
            return Response::send(-1, '创建者不能删除');
        }

        $adminUser = ModelUtil::get('admin_user', ['id' => $id]);
        if (empty($adminUser)) {
            return Response::send(-1, '记录不存在');
        }

        ModelUtil::delete('admin_user', ['id' => $id]);
        ModelUtil::delete('admin_user_role', ['userId' => $id]);

        ModelUtil::model('admin_user')->whereNotNull('id')->update(['ruleChanged' => true]);

        AdminUtil::addInfoLog($this->adminUserId(), '删除管理员', [
            '用户名' => $adminUser['username'],
        ]);

        return Response::send(0, null, null, '[js]window.lister.load(false);');
    }

    public function logList()
    {
        if (Request::isMethod('post')) {

            $input = InputPackage::buildFromInput();
            $page = $input->getInteger('page', 10);

            $pageSize = 20;
            $option = [];

            $option['order'] = ['id', 'desc'];
            $option['search'] = $input->getArray('search');

            $head = [];
            $head[] = ['field' => 'created_at', 'title' => '时间', 'attr' => 'width="140"'];
            $head[] = ['field' => 'type', 'title' => '类型', 'attr' => 'width="80"'];
            $head[] = ['field' => 'adminUserName', 'title' => '用户',];
            $head[] = ['field' => 'summary', 'title' => '操作',];
            $head[] = ['field' => 'content', 'title' => '数据',];
            if (AdminPowerUtil::permit('\TechSoft\Laravel\Admin\Controller\SystemController@logDelete')) {
                $head[] = ['field' => '_operation', 'title' => '-'];
            }

            $paginateData = ModelUtil::paginate('admin_log', $page, $pageSize, $option);
            ModelUtil::join($paginateData['records'], 'adminUserId', '_adminUser', 'admin_user', 'id');
            ModelUtil::join($paginateData['records'], 'id', '_data', 'admin_log_data', 'id');

            $list = [];
            foreach ($paginateData['records'] as $record) {
                $item = ['_id' => $record['id']];
                switch ($record['type']) {
                    case AdminLogType::INFO:
                        $item['type'] = '<span class="uk-text-success">信息</span>';
                        break;
                    case AdminLogType::ERROR:
                        $item['type'] = '<span class="uk-text-danger">错误</span>';
                        break;
                }
                $item['created_at'] = $record['created_at'];
                if (empty($record['_adminUser'])) {
                    $item['adminUserName'] = '-';
                } else {
                    $item['adminUserName'] = $record['_adminUser']['username'];
                }
                $item['summary'] = htmlspecialchars($record['summary']);
                $item['content'] = '';
                if (!empty($record['_data']['content'])) {
                    $content = @json_decode($record['_data']['content'], true);
                    if (empty($content)) {
                        $content = $record['_data']['content'];
                    }
                    if (!empty($content)) {
                        $contentLines = [];
                        if (is_array($content)) {
                            foreach ($content as $k => $v) {
                                if (is_array($v)) {
                                    $v = json_encode($v, JSON_UNESCAPED_UNICODE);
                                }
                                $contentLines[] = "<span class='uk-text-muted'>" . htmlspecialchars($k) . ":</span> " . htmlspecialchars($v);
                            }
                        } else {
                            $contentLines[] = htmlspecialchars($content);
                        }
                        $item['content'] = '<div style="max-height:50px;width:700px;display:inline-block;white-space:normal;line-height:15px;overflow:hidden;font-size:12px;border:1px solid #CCC;border-radius:3px;background:#EEE;padding:3px;box-sizing:content-box;cursor:pointer;" data-uk-tooltip title="点击展开/缩小" onclick="$(this).css(\'max-height\',$(this).css(\'max-height\')==\'50px\'?\'\':\'50px\');">' . join('<br />', $contentLines) . '</div>';
                    }
                }
                if (AdminPowerUtil::permit('\TechSoft\Laravel\Admin\Controller\SystemController@logDelete')) {
                    $item['_operation'] = '<a href="javascript:;" data-ajax-request-loading data-ajax-request="delete/' . $record['id'] . '" data-uk-tooltip title="删除"><i class="uk-icon-trash"></i></a>';
                }
                $list[] = $item;
            }

            $data = [];
            $data['head'] = $head;
            $data['list'] = $list;
            $data['total'] = $paginateData['total'];
            $data['pageSize'] = $pageSize;
            $data['page'] = $page;

            return Response::json(0, null, $data);
        }
        return view('admin::system.log.list');
    }


    public function logDelete($id = 0)
    {
        if (AdminPowerUtil::isDemo()) {
            return AdminPowerUtil::demoResponse();
        }

        ModelUtil::delete('admin_log', ['id' => $id]);
        ModelUtil::delete('admin_log_data', ['id' => $id]);

        return Response::send(0, null, null, '[js]window.lister.load(false);');
    }

}
