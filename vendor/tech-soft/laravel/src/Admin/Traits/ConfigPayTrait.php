<?php

namespace TechSoft\Laravel\Admin\Traits;


use TechOnline\Laravel\Http\Request;
use TechSoft\Laravel\Admin\Cms\ConfigCms;
use TechSoft\Laravel\Admin\Cms\Field\FieldSwitch;
use TechSoft\Laravel\Admin\Cms\Field\FieldText;
use TechSoft\Laravel\Admin\Cms\Field\FieldTextarea;

trait ConfigPayTrait
{
    public function payPayOffline(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'payPayOffline',
            'pageTitle' => '自助结算',
            'fields' => [
                'payPayOfflineOn' => ['type' => FieldSwitch::class, 'title' => '开启', 'desc' => ''],
                'payPayOfflineAppId' => ['type' => FieldText::class, 'title' => 'AppId', 'desc' => ''],
                'payPayOfflineAppSecret' => ['type' => FieldText::class, 'title' => 'AppSecret', 'desc' => ''],
            ]
        ]);
    }

    public function payAlipay(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'payAlipay',
            'pageTitle' => '支付宝',
            'fields' => [
                'payAlipayOn' => ['type' => FieldSwitch::class, 'title' => '开启支付宝付款', 'desc' => ''],
                'payAlipayPartnerId' => ['type' => FieldText::class, 'title' => '卖家ID(PartnerId)', 'desc' => '如 2085364735263489'],
                'payAlipaySellerId' => ['type' => FieldText::class, 'title' => 'ID(SellerId)', 'desc' => '如 seller@example.com'],
                'payAlipayKey' => ['type' => FieldText::class, 'title' => '安全Key', 'desc' => '如 pdehgmdjeubnghtyddktjm174hdj'],
            ]
        ]);
    }

    public function payAlipayWeb(ConfigCms $configCms)
    {
        return $configCms->execute($this, [
            'group' => 'payAlipayWeb',
            'pageTitle' => '支付宝-Web',
            'fields' => [
                'payAlipayWebOn' => ['type' => FieldSwitch::class, 'title' => '开启', 'desc' => ''],
                'payAlipayWebAppId' => ['type' => FieldText::class, 'title' => 'AppId', 'desc' => ''],
                'payAlipayWebAliPublicKey' => ['type' => FieldText::class, 'title' => '支付宝公钥', 'desc' => ''],
                'payAlipayWebRSAPrivateKey' => ['type' => FieldTextarea::class, 'title' => 'RSA2(SHA256)密钥(推荐)', 'desc' => '复制 -----BEGIN RSA PRIVATE KEY----- 和 -----END RSA PRIVATE KEY----- 中间的部分'],
            ]
        ]);
    }

    public function payWechat(ConfigCms $configCms)
    {
        if (Request::isPost()) {
            file_exists($file = base_path('storage/cache/pay/wechat_cert.pem')) && @unlink($file);
            file_exists($file = base_path('storage/cache/pay/wechat_key.pem')) && @unlink($file);
        }

        return $configCms->execute($this, [
            'group' => 'payWechat',
            'pageTitle' => '微信扫码支付',
            'fields' => [
                'payWechatOn' => ['type' => FieldSwitch::class, 'title' => '开启微信扫码支付', 'desc' => '只能在PC端使用微信支付'],
                'payWechatAppId' => ['type' => FieldText::class, 'title' => '微信扫码支付AppId', 'desc' => ''],
                'payWechatAppSecret' => ['type' => FieldText::class, 'title' => '微信扫码支付AppSecret', 'desc' => ''],
                'payWechatAppToken' => ['type' => FieldText::class, 'title' => '微信扫码支付AppToken', 'desc' => ''],
                'payWechatMerchantId' => ['type' => FieldText::class, 'title' => '微信扫码支付商家ID(MerchantId)', 'desc' => '如 136XXXXXXX'],
                'payWechatKey' => ['type' => FieldText::class, 'title' => '微信扫码支付API密钥(Key)', 'desc' => '长度32位，在微信支付平台中的 账户中心 > API安全 > API密钥 中获取。'],
                'payWechatFileCert' => ['type' => FieldTextarea::class, 'title' => '微信扫码支付证书密钥文件内容', 'desc' => '从微信支付平台下载到的 apiclient_cert.pem 文件内容。 <br />以 -----BEGIN CERTIFICATE----- 开头，以 -----END CERTIFICATE----- 结尾。'],
                'payWechatFileKey' => ['type' => FieldTextarea::class, 'title' => '微信扫码支付CA证书文件内容', 'desc' => '从微信支付平台下载到的 apiclient_key.pem 文件内容。 <br />以 -----BEGIN PRIVATE KEY----- 开头，以 -----END PRIVATE KEY----- 结尾。'],
            ]
        ]);
    }

    public function payWechatMobile(ConfigCms $configCms)
    {
        if (Request::isPost()) {
            file_exists($file = base_path('storage/cache/pay/wechat_mobile_cert.pem')) && @unlink($file);
            file_exists($file = base_path('storage/cache/pay/wechat_mobile_key.pem')) && @unlink($file);
        }

        return $configCms->execute($this, [
            'group' => 'payWechat',
            'pageTitle' => '微信手机支付',
            'fields' => [
                'payWechatMobileOn' => ['type' => FieldSwitch::class, 'title' => '开启微信手机支付', 'desc' => '只能在微信中支付'],
                'payWechatMobileAppId' => ['type' => FieldText::class, 'title' => '微信手机支付AppId', 'desc' => ''],
                'payWechatMobileAppSecret' => ['type' => FieldText::class, 'title' => '微信手机支付AppSecret', 'desc' => ''],
                'payWechatMobileMerchantId' => ['type' => FieldText::class, 'title' => '微信手机支付商家ID(MerchantId)', 'desc' => '如 136XXXXXXX'],
                'payWechatMobileKey' => ['type' => FieldText::class, 'title' => '微信手机支付API密钥(Key)', 'desc' => '长度32位，在微信支付平台中的 账户中心 > API安全 > API密钥 中获取。'],
                'payWechatMobileFileCert' => ['type' => FieldTextarea::class, 'title' => '微信手机支付证书密钥文件内容', 'desc' => '从微信支付平台下载到的 apiclient_cert.pem 文件内容。 <br />以 -----BEGIN CERTIFICATE----- 开头，以 -----END CERTIFICATE----- 结尾。'],
                'payWechatMobileFileKey' => ['type' => FieldTextarea::class, 'title' => '微信手机支付CA证书文件内容', 'desc' => '从微信支付平台下载到的 apiclient_key.pem 文件内容。 <br />以 -----BEGIN PRIVATE KEY----- 开头，以 -----END PRIVATE KEY----- 结尾。'],
            ]
        ]);
    }

    public function payWechatMiniProgram(ConfigCms $configCms)
    {
        if (Request::isPost()) {
            file_exists($file = base_path('storage/cache/pay/wechat_mini_program_cert.pem')) && @unlink($file);
            file_exists($file = base_path('storage/cache/pay/wechat_mini_program_key.pem')) && @unlink($file);
        }

        return $configCms->execute($this, [
            'group' => 'payWechatMiniProgram',
            'pageTitle' => '微信小程序支付',
            'fields' => [
                'payWechatMiniProgramOn' => ['type' => FieldSwitch::class, 'title' => '开启微信小程序支付', 'desc' => '只能在微信中支付'],
                'payWechatMiniProgramAppId' => ['type' => FieldText::class, 'title' => '微信小程序支付AppId', 'desc' => ''],
                'payWechatMiniProgramAppSecret' => ['type' => FieldText::class, 'title' => '微信小程序支付AppSecret', 'desc' => ''],
                'payWechatMiniProgramMerchantId' => ['type' => FieldText::class, 'title' => '微信小程序支付商家ID(MerchantId)', 'desc' => '如 136XXXXXXX'],
                'payWechatMiniProgramKey' => ['type' => FieldText::class, 'title' => '微信小程序支付API密钥(Key)', 'desc' => '长度32位，在微信支付平台中的 账户中心 > API安全 > API密钥 中获取。'],
                'payWechatMiniProgramFileCert' => ['type' => FieldTextarea::class, 'title' => '微信小程序支付证书密钥文件内容', 'desc' => '从微信支付平台下载到的 apiclient_cert.pem 文件内容。 <br />以 -----BEGIN CERTIFICATE----- 开头，以 -----END CERTIFICATE----- 结尾。'],
                'payWechatMiniProgramFileKey' => ['type' => FieldTextarea::class, 'title' => '微信小程序支付CA证书文件内容', 'desc' => '从微信支付平台下载到的 apiclient_key.pem 文件内容。 <br />以 -----BEGIN PRIVATE KEY----- 开头，以 -----END PRIVATE KEY----- 结尾。'],
            ]
        ]);
    }
}