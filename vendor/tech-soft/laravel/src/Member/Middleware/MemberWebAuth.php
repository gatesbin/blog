<?php

namespace TechSoft\Laravel\Member\Middleware;

use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Member\Interfaces\MemberLoginCheck;

class MemberWebAuth extends MemberAuth
{
    protected function check($controller, $action, $memberUser)
    {
        if (is_subclass_of($controller, MemberLoginCheck::class)) {
            if (empty($memberUser['id'])) {
                if (property_exists($controller, 'ignoreAction')
                    && is_array($controller::$ignoreAction)
                    && in_array($action, $controller::$ignoreAction)
                ) {
                                    } else {
                    $ret = Response::send(-1, null, null, '/login?redirect=' . urlencode(Request::currentPageUrl()));
                    return Response::generate(-1, null, $ret);
                }
            }
        }
        return Response::generate(0, 'ok');
    }
}