<?php

namespace TechSoft\Laravel\Shop\Util;


class ShopUtil
{
    public static function unified2KeyValue($spec)
    {
        if (!$spec) {
            return [];
        }
        $pcs = explode('|', $spec);
        $kvs = [];
        foreach ($pcs as $pc) {
            $kv = explode(':', $pc);
            $kvs[$kv[0]] = $kv[1];
        }
        return $kvs;
    }

    public static function keyValue2Unified($spec)
    {
        ksort($spec, SORT_STRING);
        $kvs = [];
        foreach ($spec as $k => $v) {
            $kvs[] = "$k:$v";
        }
        return join('|', $kvs);
    }

    
    public static function generateSN()
    {
        return date('YmdHis', time()) . rand(10000000, 99999999);
    }
    
    
}