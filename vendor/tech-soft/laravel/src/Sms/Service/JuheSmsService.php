<?php

namespace TechSoft\Laravel\Sms\Service;

use TechOnline\Laravel\Http\Response;

class JuheSmsService
{
    private $key;

    
    public function __construct($key)
    {
        $this->key = $key;
    }


    
    public function send($phone, $tplId, $param = [])
    {
        $urlParam = [];
        $urlParam['key'] = $this->key;
        $urlParam['tpl_id'] = $tplId;
        $urlParam['mobile'] = $phone;

        $tplValuePair = [];
        foreach ($param as $k => $v) {
            $tplValuePair[] = '#' . urlencode($k) . '#=' . urlencode($v);
        }
        $urlParam['tpl_value'] = join('&', $tplValuePair);
        $ret = $this->get('http://v.juhe.cn/sms/send', $urlParam);
        $ret = @json_decode($ret, true);
        if (empty($ret) || !isset($ret['error_code'])) {
            return Response::generate(-1, '发送发生错误', $ret);
        }
        if ($ret['error_code']) {
            $reason = $ret['reason'];
            unset($ret['reason']);
            return Response::generate(-1, $reason, $ret);
        }
        return Response::generate(0, null, $ret);
    }

    private function get($url, $param = [])
    {
        $split = '?';
        if (strpos($url, '?') !== false) {
            $split = '&';
        }
        $url .= $split . http_build_query($param);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}