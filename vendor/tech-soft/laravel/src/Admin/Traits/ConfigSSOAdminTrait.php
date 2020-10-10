<?php

namespace TechSoft\Laravel\Admin\Traits;

use Illuminate\Support\Facades\Input;
use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;
use TechSoft\Laravel\Config\ConfigUtil;

trait ConfigSSOAdminTrait
{
    public function ssoAdminServer(ConfigCms $configCms)
    {
        if (Request::isPost()) {
            if (ConfigUtil::get('adminSSOClientEnable', false) && Input::get('adminSSOServerEnable')) {
                return Response::send(-1, '一个系统不能同时开启服务端和客户端');
            }
        }
        return $configCms->execute($this, [
            'group' => 'ssoAdminServer',
            'pageTitle' => '同步登录服务端',
            'fields' => [
                'adminSSOServerEnable' => ['type' => FieldSwitch::class, 'title' => '开启同步登录服务端', 'desc' => '本服务端地址为 <code>' . Request::domainUrl() . env('ADMIN_PATH', '/admin/') . '/sso/server</code>'],
                'adminSSOServerSecret' => ['type' => FieldText::class, 'title' => '同步登录通讯秘钥', 'desc' => '长度为32位随机字符串，需要和同步登录客户端通讯秘钥相同。<a href="javascript:;" onclick="$(\'[name=ssoServerSecret]\').val(window.api.util.randomString(32));">点击生成</a>'],
                'adminSSOClientList' => ['type' => FieldTextarea::class, 'title' => '允许的同步登录客户端列表', 'desc' => '每行一个 如 http://www.client.com/admin/sso/client'],
            ]
        ]);
    }

    public function ssoAdminClient(ConfigCms $configCms)
    {
        if (Request::isPost()) {
            if (ConfigUtil::get('adminSSOServerEnable', false) && Input::get('adminSSOClientEnable')) {
                return Response::send(-1, '一个系统不能同时开启服务端和客户端');
            }
        }
        return $configCms->execute($this, [
            'group' => 'ssoAdminClient',
            'pageTitle' => '同步登录客户端',
            'fields' => [
                'adminSSOClientEnable' => ['type' => FieldSwitch::class, 'title' => '开启同步登录客户端', 'desc' => '本客户端地址为 <code>' . Request::domainUrl() . env('ADMIN_PATH', '/admin/') . 'sso/client</code>'],
                'adminSSOClientSecret' => ['type' => FieldText::class, 'title' => '同步登录通讯秘钥', 'desc' => '长度为32位随机字符串，需要和同步登录服务端通讯秘钥相同。 <a href="javascript:;" onclick="$(\'[name=ssoClientSecret]\').val(window.api.util.randomString(32));">点击生成</a>'],
                'adminSSOServer' => ['type' => FieldText::class, 'title' => '同步登录服务端', 'desc' => '每行一个 如 http://www.server.com/admin/sso/server'],
            ]
        ]);
    }
}
