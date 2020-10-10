<?php

namespace TechOnline\Laravel\Type;

use TechOnline\Utils\ConstantUtil;

class TypeUtil
{
    public static function name($typeCls, $value)
    {
        $list = $typeCls::getList();
        foreach ($list as $k => $v) {
            if ($k == $value) {
                return $v;
            }
        }
        return null;
    }

    public static function dump($cls)
    {
        $keys = ConstantUtil::dump($cls);
        $map = $cls::getList();
        foreach ($keys as $key => $value) {
            $values[$key]['key'] = $key;
            $values[$key]['value'] = $value;
            $values[$key]['name'] = (isset($map[$value]) ? $map[$value] : null);
        }
        return $values;
    }
}
