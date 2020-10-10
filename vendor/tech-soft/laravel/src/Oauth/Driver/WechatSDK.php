<?php

namespace TechSoft\Laravel\Oauth\Driver;

class WechatSDK extends Oauth
{
    
    protected $GetRequestCodeURL = 'https://open.weixin.qq.com/connect/qrconnect';

    
    protected $GetAccessTokenURL = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    
    protected $Authorize = 'scope=snsapi_login';

    
    protected $ApiBase = 'https://api.weixin.qq.com/';

    
    public function call($api, $param = '', $method = 'GET', $multi = false)
    {
        
        $params = array(
            'access_token' => $this->Token['access_token'],
            'openid' => $this->openid(),
            'lang' => 'zh_CN'
        );

        $data = $this->http($this->url($api), $this->param($params, $param), $method);
        return json_decode($data, true);
    }

    
    protected function parseToken($result, $extend)
    {
        $data = @json_decode($result, true);
        if (!empty($data['access_token']) && !empty($data['expires_in'])) {
            $this->Token = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else {
            throw new \Exception("获取微信 ACCESS_TOKEN 出错：{$result}");
        }
    }

    
    public function openid()
    {
        $data = $this->Token;
        if (isset($data['openid'])) {
            return $data['openid'];
        } else {
            throw new \Exception('没有获取到openid！');
        }
    }
}