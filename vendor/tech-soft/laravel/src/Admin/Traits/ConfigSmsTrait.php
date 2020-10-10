<?php

namespace TechSoft\Laravel\Admin\Traits;


use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldImage;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;
use TechSoft\Laravel\Sms\SmsSender;
use TechSoft\Laravel\Sms\SmsTemplate;
use TechSoft\Laravel\Sms\SmsUtil;

trait ConfigSmsTrait
{
    public function sms(ConfigCms $configCms, $senders = null)
    {
        $html = '
<script>
$(function(){
    var change = function(){
        $("[data-cms-config-field^=systemSmsSender_]").hide();
        var sender = $("[name=systemSmsSender]").val();
        $("[data-cms-config-field^=systemSmsSender_"+sender+"_]").show();
    };
    $("[name=systemSmsSender]").on("change",change);
    change();
});
</script>
';
        if (null === $senders) {
            $senders = SmsSender::getList();
        }
        $senderHtml = [];
        $senderHtml[] = '
<div data-cms-config-field="systemSmsSender_softApi_Desc">
    访问 <a href="http://api.' . __BASE_SITE__ . '" target="_blank">https://api.' . __BASE_SITE__ . '</a> 申请短信服务
</div>
';
        return $configCms->execute($this, [
            'group' => 'sms',
            'pageTitle' => '短信发送',
            'bodyAppendHtml' => $html,
            'fields' => [
                'systemSmsEnable' => ['type' => FieldSwitch::class, 'title' => '开启短信发送', 'desc' => ''],
                'systemSmsSender' => ['type' => FieldSelect::class, 'title' => '发送类型', 'desc' => join("", $senderHtml), 'options' => $senders],
                'systemSmsSender_softApi_appId' => ['type' => FieldText::class, 'title' => '短信接口-AppId', 'desc' => ''],
                'systemSmsSender_softApi_appSecret' => ['type' => FieldText::class, 'title' => '短信接口-AppSecret', 'desc' => ''],
                'systemSmsSender_softApi_verify_templateId' => ['type' => FieldText::class, 'title' => '短信接口-验证码模板ID', 'desc' => '验证码模板变量为 code'],
                'systemSmsSender_aliyun_accessKeyId' => ['type' => FieldText::class, 'title' => '阿里云-AccessKeyId', 'desc' => ''],
                'systemSmsSender_aliyun_accessKeySecret' => ['type' => FieldText::class, 'title' => '阿里云-accessKeySecret', 'desc' => ''],
                'systemSmsSender_aliyun_signName' => ['type' => FieldText::class, 'title' => '阿里云-短信签名', 'desc' => ''],
                'systemSmsSender_aliyun_verify_templateId' => ['type' => FieldText::class, 'title' => '阿里云-验证码模板ID', 'desc' => '验证码模板变量为 code'],
            ],
        ]);
    }

    public function smsTest(ConfigCms $configCms)
    {
        if (Request::isPost()) {
            $phone = InputPackage::buildFromInput()->getPhone('phone');
            if (empty($phone)) {
                return Response::send(-1, '手机为空或格式不正确');
            }
            $ret = SmsUtil::send($phone, SmsTemplate::VERIFY, ['code' => '11111']);
            if ($ret['code']) {
                return Response::send(-1, '邮件发送失败:(' . $ret['msg'] . ')');
            }
            return Response::send(0, '测试邮件成功发送到' . $phone);
        }
        return $configCms->execute($this, [
            'group' => 'smsTest',
            'pageTitle' => '短信发送测试',
            'fields' => [
                'phone' => ['type' => FieldText::class, 'title' => '测试接收手机', 'desc' => ''],
            ]
        ]);
    }

}
