<?php

namespace TechSoft\Laravel\Firewall;

use TechOnline\Laravel\Redis\RedisUtil;

class FirewallUtil
{
    public static function listVisitIps()
    {
        $ips = [];
        $redis = RedisUtil::client();
        try {
            $keys = $redis->keys('F:V:*');
            $fix = strlen('F:V:');
            foreach ($keys as $key) {
                try {
                    $ip = substr($key, $fix);
                    $ips[$ip] = intval($redis->get($key));
                } catch (\Exception $e) {
                                    }
            }
        } catch (\Exception $e) {
        }
        arsort($ips, SORT_NUMERIC);
        return $ips;
    }
}