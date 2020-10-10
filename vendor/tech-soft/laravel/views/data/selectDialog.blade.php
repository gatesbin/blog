@extends('soft::data.dialogFrame')

@section('pageTitle','请选择文件')

@section('headAppend')
    @parent
    <link rel="stylesheet" href="@assets('assets/lib/css/pager.css')"/>
    <style type="text/css">
        #list{overflow: hidden;padding: 10px 0;}
        #list .empty {line-height:150px;text-align:center;color:#999;}
        #list .item {width: 120px;margin:10px;float: left;position: relative;}
        #list .item .type{width: 120px;height: 120px;border: 1px solid #EEE;text-align:center;line-height:120px;text-align:center;color:#CCC;font-size:30px;}
        #list .item .image{background-size:contain;background-color:#eee;background-repeat: no-repeat;width: 120px;height: 120px;border: 1px solid #EEE;position:relative;background-position:center;}
        #list .item .image .checked{position: absolute;display: none;right: 0;bottom: 0;border: 14px solid #07d;border-left-color: transparent;border-top-color: transparent;}
        #list .item .image .checked i{color: #FFF;font-size: 14px;right: -14px;top: -2px;position: absolute;display: block;}
        #list .item .name{text-align:center;height:20px;line-height:20px;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;width:100%;color:#999;}
        #list .item.checked .image {border-color: #07d;}
        #list .item.checked .image .checked{display:block;}
        #list .item .action{text-align:center;}
        #list .item .action a{color:#999;}
        #pageContainer{padding:0 0 50px 210px;}
        #pageContainer a{text-decoration:none;}
        #pageContainer #pageLeft{width:200px;position:fixed;left:0px;top:0px;bottom:50px;background:#F8F8F8;border-radius:3px;}
        #pageContainer #pageLeft #pageLeftTrigger{display:none;}
        #categoryMenu{padding:8px;background:#EEE;box-sizing:border-box;}
        #categoryMenu a{display:inline-block;width:30%;text-align:center;color:#999;line-height:18px;}
        #categoryMenu a.enable{color:#0666f3;}
        #categorySearch{padding:0 8px 8px 8px;background:#EEE;line-height:24px;font-size:12px;}
        #categorySearch input[type=text]{line-height:24px;height:26px;display:block;width:100%;border:1px solid #CCC;outline:none;border-radius:3px;padding:0 3px;box-sizing:border-box;}
        #categoryTree{position:fixed;left:0px;width:200px;top:68px;bottom:50px;overflow:auto;padding:0;margin:0;}
        #categoryTree ul{padding:0;}
        #categoryTree a:hover{text-decoration:none;}
        #categoryTree a.selected{color:#0666f3;}
        #pageContainer #pageRight{border-radius:3px;}
        #contentHead{padding:5px 10px;background:#FFF;border-bottom:1px solid #EEE;height:40px;box-sizing:border-box;}
        #imageUploadButton{width:100px;float:right;border-radius:4px;}
        #customLinkButton{float:right;display:block;margin-right:10px;line-height:30px;color:#999;font-size:12px;}
        #contentTitle{line-height:30px;}
        #contentTitle a{display:inline-block;margin:0 0 0 10px;font-size:12px;color:#999;}
        #contentTitle a.enable{color:#0666f3;}
        @media (max-width: 600px) {
            #pageContainer{padding:0 0 0 0;}
            #pageContainer:after{content:'';display:none;position:fixed;background:rgba(0,0,0,0.5);z-index:999;top:0;right:0;left:0;bottom:0;}
            #pageContainer #pageLeft{display:block;position:fixed;left:-200px;top:0px;bottom:0px;z-index:1000;}
            #pageContainer #pageLeft #pageLeftTrigger{display:block;position:absolute;right:-30px;top:10px;background:#EEE;border-radius:0 5px 5px 0;height:30px;width:30px;text-align:center;line-height:30px;}
            #pageContainer #pageLeft #categoryTree{top:100px;left:-200px;}
            #pageContainer.left-show #pageLeft{left:0px;}
            #pageContainer.left-show #pageLeft  #categoryTree{left:0px;}
            #pageContainer.left-show:after{display:block;}
            #pageContainer #pageRight{}
            #pageContainer #pageRight #contentHead{height:auto;overflow:hidden;padding:0px;padding-left:20px;}
            #pageContainer #pageRight #contentHead a{margin:0 5px;}
        }
    </style>
@endsection

@section('bodyAppend')
    @parent
    <script>
        var __uploadButton = {
            swf:'@assets('assets/webuploader/Uploader.swf')',
            chunkSize: <?php echo \TechOnline\Utils\FileUtil::formattedSizeToBytes(ini_get('upload_max_filesize'))-500*1024; ?>,
            extensions:<?php echo json_encode(join(',',config('data.upload.'.$category.'.extensions'))); ?>,
            sizeLimit:<?php echo json_encode(config('data.upload.'.$category.'.maxSize')); ?>,
        };
    </script>
    <script src="@assets('assets/basic/js/dataSelectDialog.js')"></script>
@endsection

@section('bodyContent')

    <div id="pageContainer">
        <div id="pageLeft">
            <div id="pageLeftTrigger"><i class="uk-icon-th-large"></i></div>
            <div id="categoryMenu">
                <a href="javascript:;" data-category-add class="enable"><i class="uk-icon-plus"></i> 新建</a>
                <a href="javascript:;" data-category-edit><i class="uk-icon-edit"></i> 修改</a>
                <a href="javascript:;" data-category-delete><i class="uk-icon-trash"></i> 删除</a>
            </div>
            <div id="categorySearch">
                <div class="uk-grid">
                    <div class="uk-width-medium-2-3 uk-width-1-1">
                        <input type="text" id="categorySearchInput" placeholder="搜索目录" />
                    </div>
                    <div class="uk-width-medium-1-3 uk-width-1-1">
                        <input type="checkbox" id="categorySearchAll" checked /><span data-uk-tooltip title="是否只搜索已归档的一级目录">主目录</span>
                    </div>
                </div>
            </div>
            <ul id="categoryTree">
            </ul>
        </div>
        <div id="pageRight">
            <div id="contentHead">
                <div id="imageUploadButton"></div>
                <a id="customLinkButton" href="javascript:;"><i class="uk-icon-link"></i> 输入URL</a>
                <div id="contentTitle">
                    <a href="javascript:;" data-image-edit><i class="uk-icon-edit"></i> 分类</a>
                    <a href="javascript:;" data-image-delete><i class="uk-icon-trash"></i> 删除</a>
                </div>
            </div>
            <div id="contentBody">
                <div id="list">
                </div>
                <div id="page" class="page-container">
                </div>
            </div>
        </div>
    </div>

    <div id="categoryEdit" class="uk-modal">
        <div class="uk-modal-dialog" style="width:300px;">
            <a class="uk-modal-close uk-close"></a>
            <div class="uk-modal-header">
                分类
            </div>
            <div class="uk-form">
                <div>
                    上级
                    <select name="categoryEditPid"></select>
                </div>
                <div style="padding:10px 0 0 0;">
                    名称
                    <input type="text" name="categoryEditTitle" />
                    <input type="hidden" name="categoryEditId" value="0" />
                </div>
            </div>
            <div class="uk-modal-footer">
                <a href="javascript:;" class="uk-button uk-button-primary" data-category-edit-save>确定</a>
            </div>
        </div>
    </div>

    <div id="imageEdit" class="uk-modal">
        <div class="uk-modal-dialog" style="width:300px;">
            <a class="uk-modal-close uk-close"></a>
            <div class="uk-modal-header">
                文件
            </div>
            <div class="uk-form">
                <div>
                    分类
                    <select name="imageEditCategoryId"></select>
                </div>
            </div>
            <div class="uk-modal-footer">
                <a href="javascript:;" class="uk-button uk-button-primary" data-image-edit-save>确定</a>
            </div>
        </div>
    </div>

@endsection
