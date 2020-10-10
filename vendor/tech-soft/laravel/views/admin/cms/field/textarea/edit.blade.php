<div class="label">
    <b>{{$field['title']}}:</b>
</div>
<div class="field">
    <textarea name="{{$key}}" rows="3">{{$data}}</textarea>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif