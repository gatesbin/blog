<?php

namespace TechSoft\Laravel\Base;

use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncrypter;

class EncryptCookies extends BaseEncrypter
{
    
    protected $except = [
            ];
}
