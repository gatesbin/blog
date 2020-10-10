<?php

namespace TechSoft\Laravel\Wechat\Support;

use Doctrine\Common\Cache\Cache;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Exceptions\HttpException;
use Illuminate\Support\Facades\Log;
use TechSoft\Laravel\Wechat\Facades\WechatAuthorizationServerFacade;


class AuthorizationAccessToken extends AccessToken
{

    protected $account;

    
    protected $prefix = 'easywechat.authorization.access_token.';

    
    public function __construct(&$account, Cache $cache = null)
    {
        $this->account = &$account;
        $this->cache = $cache;
    }

    
    public function getToken($forceRefresh = false)
    {
        $cacheKey = $this->prefix . '-' . $this->account['appId'] . '-' . $this->account['authType'];

        $cached = $this->getCache()->fetch($cacheKey);

        if ($forceRefresh || empty($cached)) {
            $token = $this->getTokenFromServer();

                        $this->getCache()->save($cacheKey, $token['authorizer_access_token'], $token['expires_in'] - 1500);

                        WechatServiceFacade::update($this->account['id'], ['authorizerRefreshToken' => $token['authorizer_refresh_token']]);

            return $token['authorizer_access_token'];
        }
        return $cached;
    }

    
    public function getAppId()
    {
        return $this->account['appId'];
    }

    
    public function getSecret()
    {
        return null;
    }

    
    public function getTokenFromServer()
    {
        $params = [
            'component_appid' => WechatAuthorizationServerFacade::getComponentAppId(),
            'authorizer_appid' => $this->account['appId'],
            'authorizer_refresh_token' => $this->account['authorizerRefreshToken'],
        ];

        $http = $this->getHttp();
                
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token';
        $queries = ['component_access_token' => WechatAuthorizationServerFacade::getComponentAccessToken()];
        $token = $http->parseJSON($http->json($url, $params, JSON_UNESCAPED_UNICODE, $queries));

        if (empty($token['authorizer_access_token'])) {
            throw new HttpException('Request AccessToken fail. response: ' . json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
    }

}
