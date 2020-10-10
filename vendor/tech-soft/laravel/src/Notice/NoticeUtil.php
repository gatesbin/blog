<?php

namespace TechSoft\Laravel\Notice;


use TechOnline\Utils\CurlUtil;
use TechSoft\Laravel\Config\ConfigUtil;

class NoticeUtil
{
    public static function sendConfigNotice($key, $msg = [])
    {
        $url = ConfigUtil::get($key);
        if (empty($url)) {
            return;
        }
        try {
            CurlUtil::get($url, ['data' => json_encode($msg)]);
        } catch (\Exception $e) {
        }
    }
}