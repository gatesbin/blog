<?php

namespace TechSoft\Laravel\Misc\Util;


class CrossDomainUtil
{
    public static function fix($url)
    {
        return AssetsHelper::fix('/cross_domain/base64/' . base64_encode($url));
    }

}