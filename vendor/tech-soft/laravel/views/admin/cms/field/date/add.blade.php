<div class="label">
    <b>{{$field['title']}}：</b>
</div>
<div class="field">
    <input type="text" name="{{$key}}" value="{{$default or ''}}" data-date-picker style="width:120px;" />
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif