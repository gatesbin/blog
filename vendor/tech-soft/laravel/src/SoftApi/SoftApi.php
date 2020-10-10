<?php

namespace TechSoft\Laravel\SoftApi;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use TechOnline\Utils\CurlUtil;
use TechOnline\Utils\SignUtil;

class SoftApi
{
    static $API_BASE = null;

    private $appId;
    private $appSecret;

    private $debug = false;

    public function __construct($appId, $appSecret = null)
    {
        self::$API_BASE = 'https://api.' . __BASE_SITE__ . '/open_api';
        if (Str::contains(Request::domainUrl(), 'api.demo.soft.host')) {
            self::$API_BASE = Request::domainUrl() . '/open_api';
        }
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    
    public function signCheck($param)
    {
        if (empty($param['sign']) || empty($param['timestamp']) || empty($param['app_id'])) {
            return false;
        }
        if ($param['app_id'] != $this->appId) {
            return false;
        }
        if (($param['timestamp'] < time() - 1800 || $param['timestamp'] > time() + 1800)) {
            return false;
        }
        $sign = $param['sign'];
        unset($param['sign']);
        $signCalc = SignUtil::common($param, $this->appSecret);
        if ($sign != $signCalc) {
            return false;
        }
        return true;
    }

    
    public static function instance($appId, $appSecret = null)
    {
        static $map = [];
        if (!isset($map[$appId])) {
            $map[$appId] = new self($appId, $appSecret);
        }
        return $map[$appId];
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    private function request($gate, $param = [])
    {
        $param['app_id'] = $this->appId;
        if ($this->appSecret) {
            $param['timestamp'] = time();
            $param['sign'] = SignUtil::common($param, $this->appSecret);
        }
        if ($this->debug) {
            Log::debug('SoftAPI -> ' . self::$API_BASE . $gate . ' -> ' . json_encode($param));
        }
        return CurlUtil::postJSONBody(self::$API_BASE . $gate, $param);
    }

    
    public function ping()
    {
        $ret = $this->request('/ping');
        if ($ret['code']) {
            return Response::generate(-1, 'PING失败');
        }
        return Response::generate(0, 'ok');
    }

    
    public function payOfflineCreate($bizSn, $money, $notifyUrl, $returnUrl)
    {
        return $this->request('/pay_offline/create', [
            'biz_sn' => $bizSn,
            'money' => $money,
            'notify_url' => $notifyUrl,
            'return_url' => $returnUrl,
        ]);
    }

    
    public function captchaVerify($action, $key, $data, $runtime, $types)
    {
        return $this->request('/captcha/verify', [
            'action' => $action,
            'key' => $key,
            'data' => $data,
            'runtime' => $runtime,
            'types' => $types,
        ]);
    }

    
    public function captchaValidate($key)
    {
        return $this->request('/captcha/validate', [
            'key' => $key,
        ]);
    }
    
    
    public function smsSend($phone, $templateId, $params = [])
    {
        $post = [];
        foreach ($params as $k => $v) {
            $post["param_$k"] = $v;
        }
        return $this->request('/sms/send', array_merge([
            'phone' => $phone,
            'template_id' => $templateId,
        ], $post));
    }

}
