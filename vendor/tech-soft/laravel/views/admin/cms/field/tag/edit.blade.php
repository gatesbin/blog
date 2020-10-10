<div class="label">
    <b>{{$field['title']}}：</b>
</div>
<div class="field">
    <input name="{{$key}}" id="{{$key}}" value="<?php if(!empty($data)){ echo htmlspecialchars(join('::',$data)); } ?>" />
</div>
@if(!empty($field['map']))
    <div>
        从下面点击可直接选取
    </div>
    <div id="{{$key}}_tag_pool" class="tagsinput" style="width:100%;height:auto;">
        @foreach($field['map'] as $k=>$v)
            <span class="tag" style="cursor:pointer;">
                <span data-key="{{$k}}" data-value="{{$v}}">{{$v}}</span>
            </span>
        @endforeach
    </div>
@endif
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif
<script>
    (function () {
        var $tag = $('#{{$key}}').tagsInput({
            'width':'100%',
            'height':'auto',
            'delimiter':'::',
            'defaultText':'回车结束',
            'onChange':function(a,b){
            }
        });
        $('#{{$key}}_tag_pool').on('click','[data-key]',function () {
            var tag = $(this).attr('data-value');
            if($tag.tagExist(tag)){
                window.api.dialog.tipError('已经存在 : '+tag);
            }else{
                $tag.addTag(tag);
            }
        });
    })();
</script>