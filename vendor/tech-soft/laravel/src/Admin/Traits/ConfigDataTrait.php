<?php

namespace TechSoft\Laravel\Admin\Traits;


use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldSelect;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;

trait ConfigDataTrait
{
    public function dataDriver(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'dataDriver',
            'pageTitle' => '存储设置',
            'fields' => [

                'uploadDriver' => ['type' => FieldSelect::class, 'title' => '上传设置', 'desc' => '', 'options' => [
                    'local' => '本地存储',
                    'ossAliyun' => '阿里云存储',
                ],],
                'uploadDriverDomain' => ['type' => FieldText::class, 'title' => '阿里云OSS域名', 'desc' => '如 http://cdn.example.com',],

                'uploadDriverAliyunAccessKeyId' => ['type' => FieldText::class, 'title' => '阿里云OSS AccessKeyId', 'desc' => '',],
                'uploadDriverAliyunAccessKeySecret' => ['type' => FieldText::class, 'title' => '阿里云OSS AccessKeySecret', 'desc' => '',],
                'uploadDriverAliyunEndpoint' => ['type' => FieldText::class, 'title' => '阿里云OSS Endpoint', 'desc' => '',],
                'uploadDriverAliyunBucket' => ['type' => FieldText::class, 'title' => '阿里云OSS Bucket', 'desc' => '',],

            ]
        ]);
    }

}