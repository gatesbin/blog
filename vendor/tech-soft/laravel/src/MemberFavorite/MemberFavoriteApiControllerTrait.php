<?php

namespace TechSoft\Laravel\MemberFavorite;

use App\Types\MemberFavoriteCategory;
use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Http\Response;
use TechOnline\Laravel\Type\TypeUtil;

trait MemberFavoriteApiControllerTrait
{
    public function submit()
    {
        $input = InputPackage::buildFromInput();
        $action = $input->getTrimString('action');
        $category = $input->getTrimString('category');
        $categoryId = $input->getInteger('categoryId');
        $name = TypeUtil::name(MemberFavoriteCategory::class, $category);
        if (empty($name)) {
            return Response::json(-1, '数据错误');
        }
        switch ($action) {
            case 'favorite':
                MemberFavoriteUtil::add($this->memberUserId(), $category, $categoryId);
                break;
            case 'unfavorite':
                MemberFavoriteUtil::delete($this->memberUserId(), $category, $categoryId);
                break;
        }
        return Response::json(0, 'ok');
    }


}