<?php


namespace TechSoft\Laravel\Admin\Traits;


use TechOnline\Laravel\Redis\RedisUtil;
use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;
use TechSoft\Laravel\Firewall\FirewallUtil;

trait ConfigVisitLimitTrait
{
    public function visitLimit(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'visitLimit',
            'pageTitle' => '访问控制',
            'fields' => [
                'systemVisitWhiteListEnable' => ['type' => FieldSwitch::class, 'title' => '启用白名单控制', 'desc' => ''],
                'systemVisitWhiteList' => ['type' => FieldTextarea::class, 'title' => '访问白名单', 'desc' => '一行一个'],
                'systemVisitBlackListEnable' => ['type' => FieldSwitch::class, 'title' => '启用黑名单控制', 'desc' => ''],
                'systemVisitBlackList' => ['type' => FieldTextarea::class, 'title' => '访问黑名单', 'desc' => '
<p>一行一个，如</p>
<p>192.168.1.1</p>
<p>192.168.1.0/24</p>
<p><a href="javascript:;" data-dialog-request="visit_ips">查看访问IP</a></p>
'],
            ]
        ]);
    }

    public function visitIps()
    {
        if (!RedisUtil::isEnable()) {
            return Response::send(-1, '访问IP需要依赖Redis');
        }
        return view('soft::admin.config.visitIps', [
            'ips' => FirewallUtil::listVisitIps(),
        ]);
    }
}