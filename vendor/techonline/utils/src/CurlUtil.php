<?php

namespace TechOnline\Utils;


class CurlUtil
{
    private static function JSONResult($code, $msg = '', $data = null)
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
        ];
        if (null !== $data) {
            $result['data'] = $data;
        }
        return $result;
    }

    private static function removeStringBOF($str)
    {
        if (strlen($str) >= 3) {
            if ('EFBBBF' == sprintf('%X%X%X', ord($str{0}), ord($str{1}), ord($str{2}))) {
                return substr($str, 3);
            }
        }
        return $str;
    }

    public static function getJSONData($url, $param = [], $option = [])
    {
        $ret = self::getJSON($url, $param, $option);
        if ($ret['code'] === 0) {
            return $ret['data'];
        }
        return null;
    }

    public static function getJSON($url, $param = [], $option = [])
    {
        $result = self::get($url, $param, $option);
        if (empty($result['body'])) {
            return self::JSONResult(-1, 'CurlUtil.getJSON result empty');
        }
        $result['body'] = self::removeStringBOF($result['body']);
        $json = @json_decode($result['body'], true);
        if (empty($json)) {
            return self::JSONResult(-1, 'CurlUtil.getJSON json parse error', $result['body']);
        }
        return self::JSONResult(0, '', $json);
    }

    public static function postContent($url, $param = [], $option = [])
    {
        $result = self::post($url, $param, $option);
        if (empty($result['body'])) {
            return null;
        }
        return $result['body'];
    }

    public static function postJSONBody($url, $param = [], $option = [])
    {
        $ret = self::postJSON($url, $param, $option);
        if ($ret['code']) {
            return $ret;
        }
        return $ret['data'];
    }

    public static function postJSON($url, $param = [], $option = [])
    {
        $result = self::post($url, $param, $option);
        if (empty($result['body'])) {
            return self::JSONResult(-1, 'CurlUtil.postJSON result empty');
        }
        $result['body'] = self::removeStringBOF($result['body']);
        $json = @json_decode($result['body'], true);
        if (empty($json)) {
            return self::JSONResult(-1, 'CurlUtil.postJSON json parse error', $result['body']);
        }
        return self::JSONResult(0, '', $json);
    }

    public static function putJSON($url, $param = [], $option = [])
    {
        $result = self::put($url, $param, $option);
        if (empty($result['body'])) {
            return self::JSONResult(-1, 'CurlUtil.putJSON result empty');
        }
        $result['body'] = self::removeStringBOF($result['body']);
        $json = @json_decode($result['body'], true);
        if (empty($json)) {
            return self::JSONResult(-1, 'CurlUtil.putJSON json parse error', $result['body']);
        }
        return self::JSONResult(0, '', $json);
    }

    public static function deleteJSON($url, $param = [], $option = [])
    {
        $result = self::delete($url, $param, $option);
        if (empty($result['body'])) {
            return self::JSONResult(-1, 'CurlUtil.deleteJSON result empty');
        }
        $result['body'] = self::removeStringBOF($result['body']);
        $json = @json_decode($result['body'], true);
        if (empty($json)) {
            return self::JSONResult(-1, 'CurlUtil.deleteJSON json parse error', $result['body']);
        }
        return self::JSONResult(0, '', $json);
    }

    public static function get($url, $param = [], $option = [])
    {
        $option['method'] = 'get';
        return self::request($url, $param, $option);
    }

    public static function post($url, $param = [], $option = [])
    {
        $option['method'] = 'post';
        return self::request($url, $param, $option);
    }

    public static function put($url, $param = [], $option = [])
    {
        $option['method'] = 'put';
        return self::request($url, $param, $option);
    }

    public static function delete($url, $param = [], $option = [])
    {
        $option['method'] = 'delete';
        return self::request($url, $param, $option);
    }

    public static function request($url, $param, $option = [])
    {
        $sendHeaders = [];
        if (!empty($option['header'])) {
            foreach ($option['header'] as $k => $v) {
                $sendHeaders[] = "$k:$v";
            }
        }
        $returnHeader = false;
        if (!empty($option['returnHeader'])) {
            $returnHeader = true;
        }
        if (!isset($option['method'])) {
            $option['method'] = 'get';
        }

        $result = [];
        $result['code'] = 0;
        $result['body'] = null;
        if ($returnHeader) {
            $result['header'] = [];
        }
        if ($option['method'] == 'get') {
            if (!empty($param)) {
                $url = $url . '?' . http_build_query($param);
            }
        }
        $ch = curl_init($url);

        if (!empty($option['debugFile'])) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $fp = fopen($option['debugFile'], 'w');
            curl_setopt($ch, CURLOPT_STDERR, $fp);
        }

        curl_setopt($ch, CURLOPT_HEADER, $returnHeader);
        if (!empty($sendHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        switch ($option['method']) {
            case 'post':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
                break;
            case 'put':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
                break;
        }
        if (strpos($url, 'https://') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (!empty($option['socks5'])) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($ch, CURLOPT_PROXY, $option['socks5']);
        }
        $output = curl_exec($ch);
        if (!empty($option['debugFile'])) {
            file_put_contents($option['debugFile'], "\n\n" . $output, FILE_APPEND);
        }
        $result['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($returnHeader) {
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headerString = substr($output, 0, $headerSize);
            $result['body'] = substr($output, $headerSize);
            foreach (explode("\n", $headerString) as $line) {
                $line = trim($line);
                if (preg_match('/^(.*?):(.*?)$/', $line, $mat)) {
                    $result['header'] [] = [
                        trim($mat[1]) => trim($mat[2])
                    ];
                }
            }
        } else {
            $result['body'] = $output;
        }
        curl_close($ch);
        return $result;
    }

    public static function proxyRequest($proxy, $url, $param = [], $option = [])
    {
        $package = $option;
        $package['url'] = $url;
        $package['param'] = $param;
        $url = "$proxy?package=" . urlencode(base64_encode(json_encode($package)));
        $content = self::getRaw($url);
        $content = @base64_decode($content);
        $content = @unserialize($content);
        return $content;
    }

    public static function proxyCommon($proxy, $package)
    {
        $url = "$proxy?package=" . urlencode(base64_encode(json_encode($package)));
        $content = self::getRaw($url);
        $content = @base64_decode($content);
        $content = @unserialize($content);
        return $content;
    }

    public static function getRaw($url, $param = [], $option = [])
    {
        if (empty($config['timeout'])) {
            $config['timeout'] = 30;
        }
        if (!empty($param)) {
            $url = $url . '?' . http_build_query($param);
        }
        $sendHeaders = [];
        if (!empty($option['header'])) {
            foreach ($option['header'] as $k => $v) {
                $sendHeaders[] = "$k:$v";
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        if (!empty($sendHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout']);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        if (StrUtil::startWith($url, 'https://')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $temp = curl_exec($ch);
        curl_close($ch);
        return $temp;
    }
}