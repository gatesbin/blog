<?php

namespace TechSoft\Laravel\Sms\Service;

use TechOnline\Laravel\Http\Response;
use Overtrue\EasySms\EasySms;

class AliyunSmsService
{
    private $accessKeyId = '';
    private $accessKeySecret = '';
    private $signName;

    private $easySms;

    
    public function __construct($accessKeyId, $accessKeySecret, $signName)
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->signName = $signName;
        $this->easySms = new EasySms([
            'timeout' => 30,
            'default' => [
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                'gateways' => [
                    'aliyun',
                ],
            ],
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'aliyun' => [
                    'access_key_id' => $this->accessKeyId,
                    'access_key_secret' => $this->accessKeySecret,
                    'sign_name' => $this->signName,
                ],
            ],
        ]);
    }

    public function send($phone, $templateId, $data)
    {
        try {
            $ret = $this->easySms->send($phone, [
                'template' => $templateId,
                'data' => $data,
            ]);
            if ('success' == $ret['aliyun']['status']) {
                return Response::generate(0, 'ok');
            }
            return Response::generate(-1, '发送错误');
        } catch (\Exception $e) {
            return Response::generate(-1, '发送错误');
        }
    }
}