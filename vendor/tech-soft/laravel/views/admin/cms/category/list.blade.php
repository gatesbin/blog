@extends('admin::frame')

@section('pageTitle',$config['pageTitleList'])

@section('bodyAppend')
    @parent
    <script>
        window.__dialogData = {};
        window.__dialogData.add = null;
        window.__dialogData.edit = null;
        window.__dialogData.view = null;
    </script>
    @if($config['canAdd'])
        <script>
            $(document).on('click', '.h-add', function () {
                var pid = $(this).attr('data-pid') || 0;
                window.__dialogData.add = layer.open({
                    type: 2,
                    title: "{{$config['pageTitleAdd']}}",
                    shadeClose: true,
                    shade: 0.8,
                    maxmin: false,
                    scrollbar: false,
                    area: ['80%', '80%'],
                    content: "{{action($config['actionAdd'])}}"+"?_pid="+pid,
                    end: function () {
                        window.__cms.action.refresh();
                    }
                });
                return false;
            });
        </script>
    @endif
    @if($config['canView'])
        <script>
            $('.h-view').on('click',function(){
                var id = $(this).attr('data-id');
                window.__dialogData.view = layer.open({
                    type: 2,
                    title: "{{$config['pageTitleView']}}",
                    shadeClose: true,
                    shade: 0.8,
                    maxmin: false,
                    scrollbar: false,
                    area: ['80%', '80%'],
                    content: "{{action($config['actionView'])}}"+"?_id="+id,
                    end: function () {
                    }
                });
            });
        </script>
    @endif
    @if($config['canEdit'])
        <script>
            $('.h-edit').on('click',function(){
                var id = $(this).attr('data-id');
                window.__dialogData.edit = layer.open({
                    type: 2,
                    title: "{{$config['pageTitleEdit']}}",
                    shadeClose: true,
                    shade: 0.8,
                    maxmin: false,
                    scrollbar: false,
                    area: ['80%', '80%'],
                    content: "{{action($config['actionEdit'])}}"+"?_id="+id,
                    end: function () {
                        window.location.reload();
                    }
                });
            });
        </script>
    @endif
    {!! $runtimeData['listAppend'] !!}
@endsection

@section('bodyMenu')
    @if($config['canAdd'])
        <a class="btn btn-main h-add" href="javascript:;" data-pid="{{$_pid}}"><span class="uk-icon-plus"></span> 添加</a>
    @endif
    @if($config['singleLevelEdit'] && $_pid)
        <a class="btn btn-default" href="?_pid={{$_pidPid}}"><span class="uk-icon-arrow-left"></span> 上级</a>
    @endif
@endsection

@section('bodyContent')

    <?php function __node_render($nodes,&$runtimeData,&$config,$currentLevel,$level){ ?>
        <?php foreach($nodes as $node){ ?>
            <tr>
                @if($config['primaryKeyShow'])
                    <td>
                        {{$node[$config['primaryKey']]}}
                    </td>
                @endif
                <?php $firstField = true; ?>
                @foreach($runtimeData['fields'] as $key)
                    <?php $field = &$config['fields'][$key]; ?>
                    <?php $value = isset($node[$key])?$node[$key]:null; ?>
                    <?php $html = $field['_instance']->listHtml($value); ?>
                    <td>
                        <?php if($firstField){ $firstField = false; echo str_repeat("<span style='color:#CCC;'>|--</span>",$level); } ?>
                        {!! $html !!}
                    </td>
                @endforeach
                <td>
                    @if($config['canSort'])
                        <a href="{{action($config['actionSort'],['direction'=>'up','_id'=>$node[$config['primaryKey']]])}}" data-uk-tooltip title="向上移动" class="action-btn"><span class="uk-icon-arrow-up"></span></a>
                        <a href="{{action($config['actionSort'],['direction'=>'down','_id'=>$node[$config['primaryKey']]])}}" data-uk-tooltip title="向下移动" class="action-btn"><span class="uk-icon-arrow-down"></span></a>
                    @endif
                    @if($config['canView'])
                        <a class="h-view action-btn" href="javascript:;" data-id="{{$node[$config['primaryKey']]}}" data-uk-tooltip title="查看"><span class="uk-icon-eye"></span></a>
                    @endif
                    @if($config['singleLevelEdit'])
                        @if(0==$config['maxLevel'] || $config['maxLevel']>$currentLevel+$level+1)
                            @if($config['canAdd'])
                                <a href="?_pid={{$node[$config['primaryKey']]}}" class="action-btn" data-uk-tooltip title="进入"><i class="uk-icon-arrow-right"></i></a>
                            @endif
                        @endif
                    @else
                        @if(0==$config['maxLevel'] || $config['maxLevel']>$level+1)
                            @if($config['canAdd'])
                                <a class="h-add action-btn" href="javascript:;" data-pid="{{$node[$config['primaryKey']]}}" data-uk-tooltip title="增加子节点"><span class="uk-icon-plus"></span></a>
                            @endif
                        @endif
                    @endif
                    @if($config['canEdit'])
                        <a class="h-edit action-btn" href="javascript:;" data-id="{{$node[$config['primaryKey']]}}" data-uk-tooltip title="修改"><span class="uk-icon-edit"></span></a>
                    @endif
                    @if($config['canDelete'])
                        <a class="h-delete action-btn" href="javascript:;" data-ajax-request-loading data-ajax-request="{{action($config['actionDelete'])}}?_id={{$node[$config['primaryKey']]}}" data-confirm="确定删除(将删除所有子分类)?" data-uk-tooltip title="删除"><span class="uk-icon-trash"></span></a>
                    @endif
                </td>
            </tr>
            <?php if(!empty($node['_child'])){ ?>
                <?php echo __node_render($node['_child'],$runtimeData,$config,$currentLevel,$level+1); ?>
            <?php } ?>
        <?php } ?>
    <?php } ?>

    <div class="block">
        @if(empty($data))
            <div class="admin-empty">暂无内容</div>
        @else
            <table class="uk-table">
                <thead>
                <tr>
                    @if($config['primaryKeyShow'])
                        <th>ID</th>
                    @endif
                    @foreach($runtimeData['fields'] as $key)
                        <?php $field = &$config['fields'][$key]; ?>
                        <th>
                            {{$field['title']}}
                        </th>
                    @endforeach
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php echo __node_render($data,$runtimeData,$config,$currentLevel,0); ?>
                </tbody>
            </table>
        @endif
    </div>

@endsection