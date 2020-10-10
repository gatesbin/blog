<?php

namespace TechSoft\Laravel\Firewall;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use TechOnline\Laravel\Redis\RedisUtil;
use TechSoft\Laravel\Config\ConfigUtil;

class FirewallMiddleware
{
    
    public function handle($request, \Closure $next)
    {
        if (RedisUtil::isEnable()) {
            $ip = Request::ip();
            if (ConfigUtil::get('systemVisitBlackListEnable', false)) {
                $list = ConfigUtil::get('systemVisitBlackList');
                foreach (explode("\n", $list) as $ipRange) {
                    $ipRange = trim($ipRange);
                    if (empty($ipRange)) {
                        continue;
                    }
                    if (IpUtils::checkIp4($ip, $ipRange)) {
                        Log::warning('Firewall.BlackList.Forbidden -> ' . $ipRange . ' - ' . $ip);
                        return 'Access Forbidden (B) !';
                    }
                }
            }
            if (ConfigUtil::get('systemVisitWhiteListEnable', false)) {
                $list = ConfigUtil::get('systemVisitWhiteList');
                $pass = false;
                foreach (explode("\n", $list) as $ipRange) {
                    $ipRange = trim($ipRange);
                    if (empty($ipRange)) {
                        continue;
                    }
                    if (IpUtils::checkIp4($ip, $ipRange)) {
                        $pass = true;
                        break;
                    }
                }
                if (!$pass) {
                    Log::warning('Firewall.WhiteList.Forbidden -> ' . $ipRange . ' - ' . $ip);
                    return 'Access Forbidden (W) !';
                }
            }
            $key = 'F:V:' . $ip;
            RedisUtil::incr($key);
            RedisUtil::expire($key, 3600 * 24);
        }
        return $next($request);
    }

}