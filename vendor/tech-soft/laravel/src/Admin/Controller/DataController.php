<?php

namespace TechSoft\Laravel\Admin\Controller;

use TechSoft\Laravel\Admin\Support\AdminCheckController;
use TechSoft\Laravel\Config\ConfigUtil;
use TechSoft\Laravel\Data\DataUploadHandle;
use TechSoft\Laravel\Data\DataUtil;
use TechSoft\Laravel\Data\SelectDialogHandle;
use TechSoft\Laravel\Data\UEditorHandle;

class DataController extends AdminCheckController
{
    public function selectDialog(SelectDialogHandle $selectDialogHandle, $category)
    {
        return $selectDialogHandle->executeForAdmin($category, DataUtil::getOSSOption());
    }
    public function tempDataUpload(DataUploadHandle $dataUploadHandle, $category = '')
    {
        $option = [];
        $option['driver'] = ConfigUtil::getWithEnv('uploadDriver', '');
        $option['domain'] = ConfigUtil::getWithEnv('uploadDriverDomain', '');
        switch ($option['driver']) {
            case 'ossAliyun':
                $option['aliyunAccessKeyId'] = ConfigUtil::getWithEnv('uploadDriverAliyunAccessKeyId', '');
                $option['aliyunAccessKeySecret'] = ConfigUtil::getWithEnv('uploadDriverAliyunAccessKeySecret', '');
                $option['aliyunEndpoint'] = ConfigUtil::getWithEnv('uploadDriverAliyunEndpoint', '');
                $option['aliyunBucket'] = ConfigUtil::getWithEnv('uploadDriverAliyunBucket', '');
                break;
        }
        return $dataUploadHandle->temp($category, $option);
    }

    public function ueditorHandle(UEditorHandle $UEditorHandle)
    {
        return $UEditorHandle->executeForAdmin();
    }
}
