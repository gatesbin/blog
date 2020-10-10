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

trait ConfigSSOTrait
{
    public function ssoServer(ConfigCms $configCms)
    {
        if (Request::isPost()) {
            if (ConfigUtil::get('ssoClientEnable', false) && Input::get('ssoServerEnable')) {
                return Response::send(-1, '一个系统不能同时开启服务端和客户端');
            }
        }
        return $configCms->execute($this, [
            'group' => 'ssoServer',
            'pageTitle' => '同步登录服务端',
            'fields' => [
                'ssoServerEnable' => ['type' => FieldSwitch::class, 'title' => '开启同步登录服务端', 'desc' => '本服务端地址为 <code>' . Request::domainUrl() . '/sso/server</code>'],
                'ssoServerSecret' => ['type' => FieldText::class, 'title' => '同步登录通讯秘钥', 'desc' => '长度为32位随机字符串，需要和同步登录客户端通讯秘钥相同。<a href="javascript:;" onclick="$(\'[name=ssoServerSecret]\').val(window.api.util.randomString(32));">点击生成</a>'],
                'ssoServerClientList' => ['type' => FieldTextarea::class, 'title' => '允许的同步登录客户端列表', 'desc' => '每行一个 如 http://www.client.com/sso/client'],
            ]
        ]);
    }

    public function ssoClient(ConfigCms $configCms)
    {
        if (Request::isPost()) {
            if (ConfigUtil::get('ssoServerEnable', false) && Input::get('ssoClientEnable')) {
                return Response::send(-1, '一个系统不能同时开启服务端和客户端');
            }
        }
        return $configCms->execute($this, [
            'group' => 'ssoServer',
            'pageTitle' => '同步登录客户端',
            'fields' => [
                'ssoClientEnable' => ['type' => FieldSwitch::class, 'title' => '开启同步登录客户端', 'desc' => '本客户端地址为 <code>' . Request::domainUrl() . '/sso/client</code>'],
                'ssoClientSecret' => ['type' => FieldText::class, 'title' => '同步登录通讯秘钥', 'desc' => '长度为32位随机字符串，需要和同步登录服务端通讯秘钥相同。 <a href="javascript:;" onclick="$(\'[name=ssoClientSecret]\').val(window.api.util.randomString(32));">点击生成</a>'],
                'ssoClientServer' => ['type' => FieldText::class, 'title' => '同步登录服务端', 'desc' => '每行一个 如 http://www.server.com/sso/server'],
            ]
        ]);
    }
}
