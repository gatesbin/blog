<?php

namespace TechSoft\Laravel\Wechat\Support;


use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\Core\Http;
use EasyWeChat\Support\Log;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Wechat\WechatUtil;

class WechatAuthorizationServer
{
    private $request;
    private $cache;

    private $rawContent;

    private $componentAppId;
    private $componentAppSecret;
    private $componentToken;
    private $componentEncodingKey;
    private $componentVerifyTicket;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->cache = WechatUtil::getCacheDriver();

        $this->initializeLogger();
        $this->init();
    }

    
    private function initializeLogger()
    {
        if (Log::hasLogger()) {
            return;
        }

        $logger = new Logger('easywechat');
        $logger->pushHandler(new NullHandler());
        Log::setLogger($logger);
    }

    public function init()
    {
        $useCache = env('WECHAT_AUTHORIZATION_SERVER_USE_CACHE', true);

        $this->componentAppId = ConfigUtil::get('wechatAuthorizationAppId', null, $useCache);
        $this->componentAppSecret = ConfigUtil::get('wechatAuthorizationAppSecret', null, $useCache);
        $this->componentToken = ConfigUtil::get('wechatAuthorizationToken', null, $useCache);
        $this->componentEncodingKey = ConfigUtil::get('wechatAuthorizationEncodingKey', null, $useCache);
        $this->componentVerifyTicket = ConfigUtil::get('wechatAuthorizationComponentVerifyTicket', null, $useCache);

        $this->rawContent = $this->request->getContent(false);
    }

    public function getRawContent()
    {
        return $this->rawContent;
    }

    public function getComponentAccessToken($forceRefresh = false)
    {
        $cacheKey = 'wx.component_access_token';

        $cached = $this->cache->fetch($cacheKey);

        if ($forceRefresh || empty($cached)) {

            $params = [
                'component_appid' => $this->componentAppId,
                'component_appsecret' => $this->componentAppSecret,
                'component_verify_ticket' => $this->componentVerifyTicket,
            ];

            $http = new Http();
            $token = $http->parseJSON($http->json('https://api.weixin.qq.com/cgi-bin/component/api_component_token', $params));

            if (empty($token['component_access_token'])) {
                throw new HttpException('Request AccessToken fail. response 1 : ' . json_encode($token, JSON_UNESCAPED_UNICODE));
            }

            $this->cache->save($cacheKey, $token['component_access_token'], $token['expires_in'] - 60 * 10);

            $cached = $token['component_access_token'];
        }

        return $cached;
    }

    public function getPreAuthCode()
    {
        $params = [
            'component_appid' => $this->componentAppId,
        ];

        $http = new Http();

        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode';
        $queries = ['component_access_token' => $this->getComponentAccessToken()];
        $token = $http->parseJSON($http->json($url, $params, JSON_UNESCAPED_UNICODE, $queries));

        if (empty($token['pre_auth_code'])) {
            throw new HttpException('Request AccessToken fail. response 2 : ' . json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token['pre_auth_code'];
    }

    public function getAuthUrl($redirectUri)
    {
        $preAuthCode = $this->getPreAuthCode();
        $url = "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=$this->componentAppId&pre_auth_code=$preAuthCode&redirect_uri=" . urlencode($redirectUri);
        return $url;
    }


    public function getQueryAuth($authorizationCode)
    {
        

        $params = [
            'component_appid' => $this->componentAppId,
            'authorization_code' => $authorizationCode,
        ];

        $http = new Http();
        
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth';
        $queries = ['component_access_token' => $this->getComponentAccessToken()];
        $info = $http->parseJSON($http->json($url, $params, JSON_UNESCAPED_UNICODE, $queries));

        if (empty($info['authorization_info'])) {
            throw new HttpException('Request AccessToken fail. response 3 : ' . json_encode($info, JSON_UNESCAPED_UNICODE));
        }

        return $info;
    }

    public function getAuthorizerInfo($authorizerAppId)
    {
        

        $params = [
            'component_appid' => $this->componentAppId,
            'authorizer_appid' => $authorizerAppId,
        ];

        $http = new Http();
                $url = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info';
        $queries = ['component_access_token' => $this->getComponentAccessToken()];
        $info = $http->parseJSON($http->json($url, $params, JSON_UNESCAPED_UNICODE, $queries));

        if (empty($info['authorizer_info'])) {
            throw new HttpException('Request AccessToken fail. response 4 : ' . json_encode($info, JSON_UNESCAPED_UNICODE));
        }

        return $info;
    }

    
    
    public function getAuthorizerOption($authorizerAppId, $option)
    {
        

        

        $params = [
            'component_appid' => $this->componentAppId,
            'authorizer_appid' => $authorizerAppId,
            'option_name' => $option,
        ];

        $http = new Http();
                $url = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_option';
        $queries = ['component_access_token' => $this->getComponentAccessToken()];
        $info = $http->parseJSON($http->json($url, $params, JSON_UNESCAPED_UNICODE, $queries));

        if (!isset($info['authorizer_appid'])) {
            throw new HttpException('Request AccessToken fail. response 5 : ' . json_encode($info, JSON_UNESCAPED_UNICODE));
        }

        return $info;

    }

    
    
    public function setAuthorizerOption($authorizerAppId, $option, $value)
    {
        

        

        $params = [
            'component_appid' => $this->componentAppId,
            'authorizer_appid' => $authorizerAppId,
            'option_name' => $option,
            'option_value' => $value,
        ];

        $http = new Http();
                $url = 'https://api.weixin.qq.com/cgi-bin/component/api_set_authorizer_option';
        $queries = ['component_access_token' => $this->getComponentAccessToken()];
        $info = $http->parseJSON($http->json($url, $params, JSON_UNESCAPED_UNICODE, $queries));

        if (isset($info['errcode']) && 0 == $info['errcode']) {
            return;
        }

        throw new HttpException('Request AccessToken fail. response 6 : ' . json_encode($info, JSON_UNESCAPED_UNICODE));

    }


    public function getComponentAppId()
    {
        return $this->componentAppId;
    }

    public function getComponentToken()
    {
        return $this->componentToken;
    }

    public function getComponentEncodingKey()
    {
        return $this->componentEncodingKey;
    }

}