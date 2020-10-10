<div class="label">
    <b>{{$field['title']}}:</b>
</div>
<div class="field">
    <textarea name="{{$key}}" rows="3">{{$default or ''}}</textarea>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif