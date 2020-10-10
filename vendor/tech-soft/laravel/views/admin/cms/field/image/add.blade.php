<div class="label">
    <b>{{$field['title']}}：</b>
</div>
<div class="field">
    <div style="position:relative;display:inline-block;" data-image-button-{{$key}}>
        <img style="height:60px;border:1px solid #CCC;border-radius:3px;" data-image-preview-{{$key}} src="@assets('assets/lib/img/none.png')"/>
        <input type="hidden" name="{{$key}}" value="" data-image-value-{{$key}} />
        <div style="line-height:60px;text-align:center;position:absolute;left:0;right:0;top:0;bottom:0;background:rgba(0,0,0,0.3);color:#FFF;display:none;"><i class="uk-icon-plus"></i></div>
        <a href="javascript:;" style="position:absolute;right:5px;top:0px;display:none;" data-uk-tooltip title="清除"><i class="uk-icon-remove"></i></a>
    </div>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif
<script>
    $('[data-image-button-{{$key}}]').on('mouseover',function(){
        $(this).find('a,div').show();
    }).on('mouseout',function () {
        $(this).find('a,div').hide();
    }).on('click','a',function () {
        $('[data-image-preview-{{$key}}]').attr('src', "@assets('assets/lib/img/none.png')");
        $('[data-image-value-{{$key}}]').val('');
        return false;
    }).on('click', function () {
        new window.api.imageSelectDialog({
            limitMax: 1,
            limitMin: 1,
            callback: function (paths) {
                $('[data-image-preview-{{$key}}]').attr('src', paths[0]);
                $('[data-image-value-{{$key}}]').val(paths[0]);
            }
        }).show();
    });
</script>