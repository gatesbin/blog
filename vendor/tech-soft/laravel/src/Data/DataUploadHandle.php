<?php

namespace TechSoft\Laravel\Data;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use TechOnline\Laravel\Http\Response;
use TechSoft\Laravel\Admin\Util\AdminPowerUtil;

class DataUploadHandle
{
    public function temp($category = '', $option = [])
    {

        if (AdminPowerUtil::isDemo()) {
            return Response::send(-1, '演示账号禁止该操作');
        }

        $categoryInfo = config('data.upload.' . $category, null);
        if (empty($categoryInfo)) {
            return Response::send(-1, 'category error');
        }

        $file = Input::file('file');
        if (empty($file) || Input::get('chunks', null)) {
            $inputAll = Input::all();
            $inputAll['lastModifiedDate'] = 'no-modified-date';
            return DataUtil::uploadHandle($category, $inputAll, [], $option);
        } else {
                        $input = [
                'file' => $file,
                'name' => $file->getClientOriginalName(),
                'type' => $file->getClientMimeType(),
                'lastModifiedDate' => 'no-modified-date',
                'size' => $file->getClientSize()
            ];
            return DataUtil::uploadHandle($category, $input, [], $option);
        }
    }
}