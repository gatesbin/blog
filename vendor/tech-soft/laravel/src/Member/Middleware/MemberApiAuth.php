<?php

namespace TechSoft\Laravel\Member\Middleware;

use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Api\ResponseCodes;
use TechSoft\Laravel\Member\Interfaces\MemberLoginCheck;

class MemberApiAuth extends MemberAuth
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
                    $ret = Response::json(ResponseCodes::LOGIN_REQUIRED, 'login required');
                    return Response::generate(-1, null, $ret);
                }
            }
        }
        return Response::generate(0, 'ok');
    }
}