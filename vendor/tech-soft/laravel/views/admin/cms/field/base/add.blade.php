<div class="label">
    <b>{{$field['title']}}：</b>
</div>
<div class="field">
    <input type="text" name="{{$key}}" @if(!empty($readonly)) readonly="readonly" @endif value="{{$default or ''}}"/>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif