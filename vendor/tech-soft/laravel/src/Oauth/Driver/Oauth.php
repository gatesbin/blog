<?php


namespace TechSoft\Laravel\Oauth\Driver;


abstract class Oauth
{
    
    protected $Version = '2.0';

    
    protected $AppKey = '';

    
    protected $AppSecret = '';

    
    protected $ResponseType = 'code';

    
    protected $GrantType = 'authorization_code';

    
    protected $Callback = '';

    
    protected $Authorize = '';

    
    protected $GetRequestCodeURL = '';

    
    protected $GetAccessTokenURL = '';

    
    protected $ApiBase = '';

    
    protected $Token = null;

    
    private $Type = '';

    private $config;

    
    public function __construct($type, $config, $token = null)
    {
                $this->config = $config;
        $this->Type = strtoupper($type);

                if (empty($config['APP_KEY'])) {
            throw new \Exception('请配置您申请的APP_KEY');
        } else {
            $this->AppKey = $config['APP_KEY'];
            $this->AppSecret = $config['APP_SECRET'];
            $this->Token = $token;         }
    }

    
    public static function getInstance($type, $config, $token = null)
    {
        $name = ucfirst(strtolower($type)) . 'SDK';
        $name = 'TechSoft\\Laravel\\Oauth\\Driver\\' . $name;
        return new $name($type, $config, $token);
    }

    
    private function config()
    {
        if (!empty($this->config['AUTHORIZE']))
            $this->Authorize = $this->config['AUTHORIZE'];
        if (!empty($this->config['CALLBACK']))
            $this->Callback = $this->config['CALLBACK'];
        else
            throw new \Exception('请配置回调页面地址');
    }

    
    public function getRequestCodeURL()
    {
        $this->config();
                switch ($this->Type) {
            case 'WECHATMOBILEAUTHORIZATION':
                $params = array(
                    'appid' => $this->AppKey,
                    'redirect_uri' => $this->Callback,
                    'response_type' => $this->ResponseType,
                );
                break;
            case 'WECHAT':
            case 'WECHATMOBILE':
                $params = array(
                    'appid' => $this->AppKey,
                    'redirect_uri' => $this->Callback,
                    'response_type' => $this->ResponseType,
                );
                break;
            default:
                $params = array(
                    'client_id' => $this->AppKey,
                    'redirect_uri' => $this->Callback,
                    'response_type' => $this->ResponseType,
                );
        }

                if ($this->Authorize) {
            parse_str($this->Authorize, $_param);
            if (is_array($_param)) {
                $params = array_merge($params, $_param);
            } else {
                throw new \Exception('AUTHORIZE配置不正确！');
            }
        }
        return $this->GetRequestCodeURL . '?' . http_build_query($params);
    }

    
    public function getAccessToken($code, $extend = null, $paramExtend = array())
    {
        $this->config();
        switch ($this->Type) {
            case 'WECHATMOBILEAUTHORIZATION':
                $params = array(
                    'appid' => $this->AppKey,
                    'code' => $code,
                    'grant_type' => $this->GrantType,
                );
                break;
            case 'WECHAT':
            case 'WECHATMOBILE':
                $params = array(
                    'appid' => $this->AppKey,
                    'secret' => $this->AppSecret,
                    'grant_type' => $this->GrantType,
                    'code' => $code,
                );
                break;
            default:
                $params = array(
                    'client_id' => $this->AppKey,
                    'client_secret' => $this->AppSecret,
                    'grant_type' => $this->GrantType,
                    'code' => $code,
                    'redirect_uri' => $this->Callback,
                );
                break;
        }

        $params = array_merge($params, $paramExtend);

        $data = $this->http($this->GetAccessTokenURL, $params, 'POST');
        $this->Token = $this->parseToken($data, $extend);
        return $this->Token;
    }

    
    protected function param($params, $param)
    {
        if (is_string($param))
            parse_str($param, $param);
        return array_merge($params, $param);
    }

    
    protected function url($api, $fix = '')
    {
        return $this->ApiBase . $api . $fix;
    }

    
    protected function http($url, $params, $method = 'GET', $header = array(), $multi = false)
    {
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTPHEADER => $header
        );

        
        switch (strtoupper($method)) {
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new \Exception('不支持的请求方式！');
        }

        
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) throw new \Exception('请求发生错误：' . $error);
        return $data;
    }

    
    abstract protected function call($api, $param = '', $method = 'GET', $multi = false);

    
    abstract protected function parseToken($result, $extend);

    
    abstract public function openid();
}