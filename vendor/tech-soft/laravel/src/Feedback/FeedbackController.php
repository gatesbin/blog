<?php


namespace TechSoft\Laravel\Feedback;


use Illuminate\Routing\Controller;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Response;

class FeedbackController extends Controller
{
    public function submit()
    {
        $input = InputPackage::buildFromInput();
        $data = [
            'title' => $input->getTrimString('title'),
            'content' => $input->getTrimString('content'),
            'contact' => $input->getTrimString('contact'),
        ];
        $names = [
            'title' => '反馈主题',
            'content' => '反馈内容',
            'contact' => '联系方式',
        ];
        foreach ($data as $k => $v) {
            if (empty($data[$k])) {
                return Response::json(-1, $names[$k] . "为空");
            }
        }
        ModelUtil::insert('feedback', $data);
        return Response::jsonSuccess();
    }
}