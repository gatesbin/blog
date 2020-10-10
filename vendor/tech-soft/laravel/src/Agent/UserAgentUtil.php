<?php

namespace TechSoft\Laravel\Agent;

use Illuminate\Support\Facades\Request;

class UserAgentUtil
{
    const SOURCE_OTHER = 0;
    const SOURCE_APP = 1;

    private static $_info = [
        'type' => null,
        'version' => null,
        'channel' => null,
        
        'os' => null,
        'osVersion' => null,
        'device' => null,

    ];

    public static function source($type = null, $channel = null)
    {
        $agentClient = Request::header('Soft-Client');
        if (empty($agentClient)) {
            $agentClient = Request::header('User-Agent');
        }
                if (null === $type) {
            $type = '[a-z]+';
        }
        if (null === $channel) {
            $channel = '[a-z]+';
        }
                if (preg_match('/^com\\.soft\\.(' . $type . ')\\/(\\d+\\.\\d+\\.\\d+) (' . $channel . ') \\((ios|android) (.*?); (.*?)\\)/', $agentClient, $mat)) {
            self::$_info['type'] = $mat[1];
            self::$_info['version'] = $mat[2];
            self::$_info['channel'] = $mat[3];
            self::$_info['os'] = $mat[4];
            self::$_info['osVersion'] = $mat[5];
            self::$_info['device'] = $mat[6];

            switch (self::$_info['os']) {
                case 'ios':
                    self::$_info['os'] = Platform::IOS;
                    break;
                case 'android':
                    self::$_info['os'] = Platform::ANDROID;
                    break;
            }
            return self::SOURCE_APP;
        }
        return self::SOURCE_OTHER;
    }
}