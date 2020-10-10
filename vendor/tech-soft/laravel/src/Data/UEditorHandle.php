<?php

namespace TechSoft\Laravel\Data;


use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Response;
use TechOnline\Utils\CurlUtil;
use Illuminate\Support\Facades\Input;
use TechSoft\Laravel\Admin\Util\AdminPowerUtil;

class UEditorHandle
{
    private function basicConfig()
    {
        $dataUploadConfig = config('data.upload', []);
        $config = [
                        "imageActionName" => "image",
            "imageFieldName" => "file",
            "imageMaxSize" => $dataUploadConfig['image']['maxSize'],
            "imageAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['image']['extensions']),
            "imageCompressEnable" => true,
            "imageCompressBorder" => 5000,
            "imageInsertAlign" => "none",
            "imageUrlPrefix" => "",

                        "scrawlActionName" => "crawl",
            "scrawlFieldName" => "file",
            "scrawlMaxSize" => $dataUploadConfig['image']['maxSize'],
            "scrawlUrlPrefix" => "",
            "scrawlInsertAlign" => "none",

                        "snapscreenActionName" => "snap",
            "snapscreenUrlPrefix" => "",
            "snapscreenInsertAlign" => "none",

                        "catcherLocalDomain" => ["127.0.0.1", "localhost"],
            "catcherActionName" => "catch",
            "catcherFieldName" => "source",
            "catcherUrlPrefix" => "",
            "catcherMaxSize" => $dataUploadConfig['image']['maxSize'],
            "catcherAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['image']['extensions']),

                        "videoActionName" => "video",
            "videoFieldName" => "file",
            "videoUrlPrefix" => "",
            "videoMaxSize" => $dataUploadConfig['video']['maxSize'],
            "videoAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['video']['extensions']),

                        "fileActionName" => "file",
            "fileFieldName" => "file",
            "fileUrlPrefix" => "",
            "fileMaxSize" => $dataUploadConfig['file']['maxSize'],
            "fileAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['file']['extensions']),

                        "imageManagerActionName" => "listImage",
            "imageManagerListSize" => 20,
            "imageManagerUrlPrefix" => "",
            "imageManagerInsertAlign" => "none",
            "imageManagerAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['image']['extensions']),

                        "fileManagerActionName" => "listFile",
            "fileManagerUrlPrefix" => "",
            "fileManagerListSize" => 20,
            "fileManagerAllowFiles" => array_map(function ($v) {
                return '.' . $v;
            }, $dataUploadConfig['file']['extensions'])

        ];
        return $config;
    }

    public function executeForMemberUser()
    {
        $config = $this->basicConfig();

        $action = Input::get('action', '');
        switch ($action) {
            case 'config':
                return Response::jsonRaw($config);

            case 'catch':
                set_time_limit(0);
                $sret = array(
                    'state' => '',
                    'list' => null
                );
                $savelist = array();
                $flist = Input::get($config ['catcherFieldName'], []);
                if (empty ($flist)) {
                    $sret ['state'] = 'ERROR';
                } else {
                    $sret ['state'] = 'SUCCESS';
                    foreach ($flist as $f) {
                        if (preg_match('/^(http|ftp|https):\\/\\//i', $f)) {

                            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                            if (in_array('.' . $ext, $config ['catcherAllowFiles'])) {
                                if ($img = CurlUtil::getRaw($f)) {
                                    $ret = DataUtil::uploadToTempData('image', '图片.' . $ext, $img);
                                    if (!$ret['code']) {
                                        $savelist [] = array(
                                            'state' => 'SUCCESS',
                                            'url' => DataUtil::getTempFullPath(DataUtil::DATA_TEMP . '/' . $ret['data']['category'] . '/' . $ret['data']['path']),
                                            'size' => strlen($img),
                                            'title' => '',
                                            'original' => '',
                                            'source' => htmlspecialchars($f)
                                        );
                                    } else {
                                        $ret ['state'] = 'Save remote file error!';
                                    }
                                } else {
                                    $ret ['state'] = 'Get remote file error';
                                }
                            } else {
                                $ret ['state'] = 'File ext not allowed';
                            }
                        } else {
                            $savelist [] = array(
                                'state' => 'not remote image',
                                'url' => '',
                                'size' => '',
                                'title' => '',
                                'original' => '',
                                'source' => htmlspecialchars($f)
                            );
                        }
                    }
                    $sret ['list'] = $savelist;
                }
                return Response::jsonRaw($sret);


        }
        return Response::generate(-1, '不能识别的请求');
    }


    public function executeForAdmin()
    {
        $config = $this->basicConfig();

        $action = Input::get('action', '');
        switch ($action) {
            case 'config':
                return Response::jsonRaw($config);

            case 'catch':

                set_time_limit(0);
                $sret = array(
                    'state' => '',
                    'list' => null
                );

                if (AdminPowerUtil::isDemo()) {
                    $sret ['state'] = 'ERROR';
                    return Response::jsonRaw($sret);
                }

                $savelist = array();
                $flist = Input::get($config ['catcherFieldName'], []);
                if (empty ($flist)) {
                    $sret ['state'] = 'ERROR';
                } else {
                    $sret ['state'] = 'SUCCESS';
                    foreach ($flist as $f) {
                        if (preg_match('/^(http|ftp|https):\\/\\//i', $f)) {

                            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                            if (in_array('.' . $ext, $config ['catcherAllowFiles'])) {
                                if ($img = CurlUtil::getRaw($f)) {
                                    $ret = DataUtil::upload('image', '图片.' . $ext, $img);
                                    if ($ret['code']) {
                                        $ret ['state'] = $ret['msg'];
                                    } else {
                                        $data = $ret['data']['data'];
                                        $fullPath = $ret['data']['fullPath'];
                                        ModelUtil::insert('admin_upload', ['category' => $data['category'], 'dataId' => $data['id'], 'adminUploadCategoryId' => 0,]);
                                        $savelist [] = array(
                                            'state' => 'SUCCESS',
                                            'url' => $fullPath,
                                            'size' => strlen($img),
                                            'title' => '',
                                            'original' => '',
                                            'source' => htmlspecialchars($f)
                                        );
                                    }
                                } else {
                                    $ret ['state'] = 'Get remote file error';
                                }
                            } else {
                                $ret ['state'] = 'File ext not allowed';
                            }
                        } else {
                            $savelist [] = array(
                                'state' => 'not remote image',
                                'url' => '',
                                'size' => '',
                                'title' => '',
                                'original' => '',
                                'source' => htmlspecialchars($f)
                            );
                        }
                    }
                    $sret ['list'] = $savelist;
                }
                return Response::jsonRaw($sret);


        }
    }
}