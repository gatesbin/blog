<div class="label">
    <b>{{$field['title']}}：</b>
</div>
<div class="field">
    <div data-file-widget="{category:'{{$category}}'}">
        <input type="hidden" name="{{$key}}" data-file value="" />
        <span class="file"></span>
        <span class="add" data-uk-tooltip title="选择文件"><i class="uk-icon-plus"></i></span>
        <span class="delete" data-uk-tooltip title="删除"><i class="uk-icon-remove"></i></span>
    </div>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif