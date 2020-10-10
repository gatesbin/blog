@extends($config['viewLayout'])

@section('pageTitle',$config['pageTitleList'])

@section('bodyAppend')
    @parent
    <script>
        var __cms = {
            __: 1
            , actionList: "{{action($config['actionList'])}}"
            , actionAdd: "@if($config['canAdd']){{action($config['actionAdd'])}}@endif"
            , actionEdit: "@if($config['canEdit']){{action($config['actionEdit'])}}@endif"
            , actionDelete: "@if($config['canDelete']){{action($config['actionDelete'])}}@endif"
            , actionExport: "@if($config['canExport']){{action($config['actionExport'])}}@endif"
            , actionView: "@if($config['canView']){{action($config['actionView'])}}@endif"
            , action: {
                // 刷新列表
                refresh: function () {
                    window.lister.load(false);
                },
                // 获取当前选择的ID
                getBatchIds : function () {
                    var ids = [];
                    $('[data-cms-checkbox-item]:checked').each(function (i,o) {
                        ids.push(parseInt($(o).attr('data-cms-checkbox-item')));
                    });
                    return ids;
                },
                // 搜索
                // search: null,
                // 获取当前的param
                 param: function () {
                     return window.lister.getParam();
                 },
                // 设置Option
                 setOption: function(name,value){
                    window.lister.setOption(name,value);
                 }
                // 触发搜索
                // doSearch: null
            }
        };

        window.__dialogData = {};
        window.__dialogData.add = null;
        window.__dialogData.edit = null;
        window.__dialogData.view = null;
        window.__dialogSetting = {
            add:{
                width:'95%',
                height:'95%'
            },
            edit:{
                width:'95%',
                height:'95%'
            },
            view:{
                width:'95%',
                height:'95%'
            }
        };
    </script>
    @if($config['canView'])
        <script>
            $('.admin-lister-container').on('click', '.h-view', function () {
                var id = $(this).closest('tr').attr('data-item-id');
                window.__dialogData.view = layer.open({
                    type: 2,
                    title: "{{$config['pageTitleView']}}",
                    shadeClose: true,
                    shade: 0.8,
                    maxmin: false,
                    scrollbar: false,
                    area: [window.__dialogSetting.view.width, window.__dialogSetting.view.height],
                    content: "{{action($config['actionView'])}}" + "?_id=" + id,
                    end: function () {
                        window.__cms.action.refresh();
                    }
                });
                return false;
                return false;
            });
        </script>
    @endif
    @if($config['canEdit'])
        <script>
            $('.admin-lister-container').on('click', '.h-edit', function () {
                var id = $(this).closest('tr').attr('data-item-id');
                window.__dialogData.edit = layer.open({
                    type: 2,
                    title: "{{$config['pageTitleEdit']}}",
                    shadeClose: true,
                    shade: 0.8,
                    maxmin: false,
                    scrollbar: false,
                    area: [window.__dialogSetting.edit.width, window.__dialogSetting.edit.height],
                    content: "{{action($config['actionEdit'])}}" + "?_id=" + id,
                    end: function () {
                        window.__cms.action.refresh();
                    }
                });
                return false;
            });
        </script>
    @endif
    @if($config['canDelete'])
        <script>
            $('.admin-lister-container').on('click', '.h-delete', function () {
                var id = $(this).closest('tr').attr('data-item-id');
                window.api.dialog.confirm('确定删除?',function(){
                    $.get("{{action($config['actionDelete'])}}" + "?_id=" + id,{},function(res){
                        window.api.base.defaultFormCallback(res,{
                            success:function(res){
                                window.__cms.action.refresh();
                            }
                        });
                    });
                });
                return false;
            });
            $('.h-delete-batch').on('click',function () {
                var ids = window.__cms.action.getBatchIds();
                if(ids.length==0){
                    window.api.dialog.tipError('请选择记录');
                    return false;
                }
                window.api.dialog.confirm('确定删除?',function(){
                    $.get("{{action($config['actionDelete'])}}" + "?_id=" + ids.join(','),{},function(res){
                        window.api.base.defaultFormCallback(res,{
                            success:function(res){
                                window.__cms.action.refresh();
                            }
                        });
                    });
                });
                return false;
            });
        </script>
    @endif
    @if($config['canAdd'])
        <script>
            $('.admin-content-head').on('click', '.h-add', function () {
                window.__dialogData.add = layer.open({
                    type: 2,
                    title: "{{$config['pageTitleEdit']}}",
                    shadeClose: true,
                    shade: 0.8,
                    maxmin: false,
                    scrollbar: false,
                    area: [window.__dialogSetting.add.width, window.__dialogSetting.add.height],
                    content: "{{action($config['actionAdd'])}}",
                    end: function () {
                        window.__cms.action.refresh();
                    }
                });
                return false;
            });
        </script>
    @endif
    <script>
        $(function () {
            $(document).on('change','[data-cms-checkbox-all]',function () {
                $('[data-cms-checkbox-item]').prop('checked',$(this).is(':checked'));
                return false;
            });
            $(document).on('change','[data-cms-checkbox-item]',function () {
                $('[data-cms-checkbox-all]').prop('checked',$('[data-cms-checkbox-item]').length==$('[data-cms-checkbox-item]:checked').length);
                return false;
            });
            $(document).on('click', '.h-refresh', function () {
                window.__cms.action.refresh();
                return false;
            });
        });
    </script>
    {!! $runtimeData['listAppend'] !!}
@endsection

@section('bodyMenu')
    @if($config['canAdd'])
        @if($config['addInNewWindow'])
            <a class="btn" href="{{action($config['actionAdd'])}}"><span class="uk-icon-plus"></span> 添加</a>
        @else
            <a class="btn h-add" href="javascript:;"><span class="uk-icon-plus"></span> 添加</a>
        @endif
    @endif
    @if($config['canImport'])
        <a class="btn" href="javascript:;" data-dialog-request="{{action($config['actionImport'])}}"><span class="uk-icon-upload"></span> 导入</a>
    @endif
    @if ($config['batchOperate'] && $config['batchDelete'])
        <a class="btn h-delete-batch" href="javascript:;"><span class="uk-icon-trash"></span> 删除</a>
    @endif
    <a class="btn h-refresh" href="javascript:;"><span class="uk-icon-refresh"></span> 刷新</a>
    {!! $runtimeData['listMenuAppend'] !!}
@endsection


@section('bodyContent')

    <div class="block uk-margin-bottom-remove">
        <div data-admin-cms-lister class="admin-lister-container">
            @if(!empty($runtimeData['fieldsSearch']))
            <div class="lister-search uk-form">
                    @foreach($runtimeData['fieldsSearch'] as $key)
                        <?php $field = &$config['fields'][$key]['_instance']; ?>
                        @if($field->search)
                            <?php echo $field->searchHtml(); ?>
                        @endif
                    @endforeach
                    <div class="item">
                        @if(!empty($runtimeData['fieldsSearch']))
                            <a class="btn btn-main" href="javascript:;" data-search-button data-uk-tooltip title="搜索"><span class="uk-icon-search"></span></a>
                            <a class="btn btn-default" href="javascript:;" data-reset-search-button data-uk-tooltip title="清空"><span class="uk-icon-refresh"></span></a>
                        @endif
                        @if($config['canExport'])
                            <a class="btn" href="javascript:;" data-uk-tooltip title="导出"
                               onclick="this.href='{{action($config['actionExport'])}}?option='+encodeURIComponent(JSON.stringify(__cms.action.param()));" target="_blank"><span class="uk-icon-download"></span></a>
                        @endif
                    </div>
            </div>
            @endif
            <div class="lister-table"></div>
            <div class="page-container"></div>
        </div>
    </div>

    @if(false)
        <div style="background:#FFF;" class="z-content-box">

            @if($runtimeData['batchOperation'])
                <div class="cms-list-multi-operation-area" id="cmsMultiOperationBox">
                    {!! $runtimeData['batchOperation'] !!}
                </div>
            @endif

        </div>
    @endif


@endsection