<?php

namespace TechSoft\Laravel\Member\Traits;


use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\MemberMessage\MemberMessageUtil;


trait MemberMessageApiControllerTrait
{
    public function lists()
    {
        $input = InputPackage::buildFromInput();
        $page = $input->getInteger('page');
        $pageSize = 10;
        $option = [
            'search' => [],
            'order' => ['id', 'desc'],
        ];
        $search = $input->getJson('search');
        if (!empty($search['status'])) {
            $option['search'][] = ['status' => ['equal' => intval($search['status'])]];
        }
        $paginateData = MemberMessageUtil::paginate($this->memberUserId(), $page, $pageSize, $option);

        return Response::json(0, 'ok', [
            'records' => $paginateData['records'],
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $paginateData['total'],
        ]);
    }

    public function delete()
    {
        $input = InputPackage::buildFromInput();
        MemberMessageUtil::delete($this->memberUserId(), $input->getStringSeparatedArray('ids'));
        return Response::json(0, 'ok');
    }

    public function read()
    {
        $input = InputPackage::buildFromInput();
        MemberMessageUtil::updateRead($this->memberUserId(), $input->getStringSeparatedArray('ids'));
        return Response::json(0, 'ok');
    }

    public function readAll()
    {
        $input = InputPackage::buildFromInput();
        MemberMessageUtil::updateReadAll($this->memberUserId());
        return Response::json(0, 'ok');
    }
}