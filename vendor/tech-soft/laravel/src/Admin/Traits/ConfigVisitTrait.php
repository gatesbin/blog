<?php

namespace TechSoft\Laravel\Admin\Traits;


use TechOnline\Laravel\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;

trait ConfigVisitTrait
{
    public function visit(ConfigCms $configCms)
    {
        if (Request::isMethod('post')) {
            $systemCdnUrl = Input::get('systemCdnUrl');
            if ($systemCdnUrl && !Str::endsWith($systemCdnUrl, '/')) {
                return Response::send(-1, '网站加速CDN必须以/结尾');
            }
        }

        return $configCms->execute($this, [
            'group' => 'visit',
            'pageTitle' => '访问设置',
            'fields' => [
                'systemCounter' => ['type' => FieldTextarea::class, 'title' => 'head访问统计代码', 'desc' => ''],
                'systemCounterBody' => ['type' => FieldTextarea::class, 'title' => 'body访问统计代码', 'desc' => ''],
                'systemCdnUrl' => ['type' => FieldText::class, 'title' => '网站加速CDN', 'desc' => '如 http://cdn.example.com/'],
            ]
        ]);
    }
}
