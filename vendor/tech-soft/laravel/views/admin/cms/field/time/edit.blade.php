<div class="label">
    <b>{{$field['title']}}：</b>
</div>
<div class="field">
    <input type="time" name="{{$key}}" value="{!! htmlspecialchars($data) !!}" />
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif