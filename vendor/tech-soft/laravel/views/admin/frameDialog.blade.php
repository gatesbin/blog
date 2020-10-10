<!doctype html>
<html class="dialog no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <meta name="description" content="@yield('pageDescription')">
    <meta name="keywords" content="@yield('pageKeywords')">
    <script>var __env={"token":"{{csrf_token()}}","adminPath":"{{env('ADMIN_PATH', '/')}}"};</script>
    <script src="@assets('assets/init.js')"></script>
    <script>
        <?php
            $uploadButtonConfig = [];
            foreach (config('data.upload') as $k=>$v){
                $uploadButtonConfig[$k] = [
                    'server'=>env('ADMIN_PATH', '/').'system/data/temp_data_upload/'.$k,
                    'extensions'=>join(',',$v['extensions']),
                    'sizeLimit'=>$v['maxSize'],
                ];
            }
        ?>
        var __uploadButton = {
            swf:"@assets('assets/webuploader/Uploader.swf')",
            chunkSize: <?php echo \TechOnline\Utils\FileUtil::formattedSizeToBytes(ini_get('upload_max_filesize'))-500*1024; ?>,
            config:<?php echo json_encode($uploadButtonConfig); ?>
        };
    </script>
    <script src="@assets('assets/admin/js/basic.js')"></script>
    <link rel="stylesheet" href="@assets('assets/uikit/css/ui.css')">
    <link rel="stylesheet" href="@assets('assets/admin/css/style.css')">
    <title>@yield('pageTitle')</title>
    @if(!empty(config('admin.globalCssFile')))
        <link rel="stylesheet" href="{{\TechSoft\Laravel\Assets\AssetsUtil::fix(config('admin.globalCssFile'))}}">
    @endif
    @if(!empty(config('admin.globalJsFile')))
        <script src="{{\TechSoft\Laravel\Assets\AssetsUtil::fix(config('admin.globalJsFile'))}}"></script>
    @endif
    @section('headAppend')@show
</head>
<body class="dialog">
@section('body')
    <div class="admin-dialog-body">
        @section('dialogBody')@show
    </div>

    <div class="admin-dialog-foot">
        <a href="javascript:;" class="close uk-button uk-button-default">关闭</a>
        <a href="javascript:;" class="submit uk-button uk-button-primary">确定</a>
    </div>
@show
@section('bodyAppend')@show
</body>
</html>