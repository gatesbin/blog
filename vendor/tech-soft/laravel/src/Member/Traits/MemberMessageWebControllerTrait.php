<?php

namespace TechSoft\Laravel\Member\Traits;


use TechOnline\Laravel\Http\Request;
use TechOnline\Laravel\Http\Response;


trait MemberMessageWebControllerTrait
{
    use MemberMessageApiControllerTrait;

    public function listsView()
    {
        $ret = $this->lists()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return $this->_view('memberMessage.list', $ret['data']);
    }

    public function deleteView()
    {
        $ret = $this->delete()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return Response::send(0, $ret['msg']);
    }

    public function readView()
    {
        $ret = $this->read()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return Response::send(0, $ret['msg']);
    }

    public function readAllView()
    {
        $ret = $this->readAll()->getData(true);
        if ($ret['code']) {
            return Response::send(-1, $ret['msg']);
        }
        return Response::send(0, $ret['msg']);
    }
}