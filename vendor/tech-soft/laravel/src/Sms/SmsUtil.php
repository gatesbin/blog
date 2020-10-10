<?php

namespace TechSoft\Laravel\Sms;

use TechOnline\Laravel\Exception\TodoException;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Sms\Service\AliyunSmsService;
use TechSoft\Laravel\SoftApi\SoftApi;

class SmsUtil
{
    public static function calcNumber($content)
    {
        return ceil(mb_strlen($content) / 67);
    }

    public static function parseContent($template, $values = array())
    {
        $param1 = [];
        $param2 = [];
        foreach ($values as $k => $v) {
            $param1[] = '{' . $k . '}';
            $param2[] = $v;
        }
        return str_replace($param1, $param2, $template);
    }

    public static function send($phone, $type, $templateData = [])
    {
        if (!ConfigUtil::get('systemSmsEnable')) {
            return Response::generate(-1, '短信发送未开启');
        }
        $senderParam = [];
        $senderFunc = ConfigUtil::get('systemSmsSender') . 'Execute';
        return self::$senderFunc($phone, $type, $templateData);
    }

    private static function softApiExecute($phone, $type, $templateData = [])
    {
        $api = SoftApi::instance(ConfigUtil::get('systemSmsSender_softApi_appId'), ConfigUtil::get('systemSmsSender_softApi_appSecret'));
        switch ($type) {
            case SmsTemplate::VERIFY:
                return $api->smsSend($phone, ConfigUtil::get('systemSmsSender_softApi_verify_templateId'), [
                    'code' => $templateData['code']
                ]);
        }
        return Response::generate(-1, '错误');
    }

    private static function aliyunExecute($phone, $type, $templateData = [])
    {
        $driver = new AliyunSmsService(
            ConfigUtil::get('systemSmsSender_aliyun_accessKeyId'),
            ConfigUtil::get('systemSmsSender_aliyun_accessKeySecret'),
            ConfigUtil::get('systemSmsSender_aliyun_signName')
        );
        switch ($type) {
            case SmsTemplate::VERIFY:
                return $driver->send($phone, ConfigUtil::get('systemSmsSender_aliyun_verify_templateId'), [
                    'code' => $templateData['code']
                ]);
        }

    }

}
