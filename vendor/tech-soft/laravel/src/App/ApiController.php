<?php

namespace TechSoft\Laravel\App;

use Illuminate\Routing\Controller;
use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Config\ConfigUtil;

class ApiController extends Controller
{
    public function document()
    {
        $map = [
            'About' => '关于我们',
            'Policy' => '用户协议',
            'Privacy' => '隐私政策',
        ];
        $input = InputPackage::buildFromInput();
        $name = $input->getTrimString('name');
        if (!isset($map[$name])) {
            return Response::json(-1, '请求错误');
        }
        $data = [
            'title' => $map[$name],
            'content' => ConfigUtil::get("appDocument$name", ""),
        ];
        return Response::json(0, 'ok', $data);
    }

    public function downloadUrl()
    {
        return Response::jsonSuccessData([
            'url' => ConfigUtil::get('appDownloadUrl'),
        ]);
    }
}