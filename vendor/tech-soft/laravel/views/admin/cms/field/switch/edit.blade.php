<div class="label">
    <b>{{$field['title']}}:</b>
</div>
<div class="field">
    <label>
        <input type="radio" name="{{$key}}" value="1" @if($data) checked @endif /> {{$labelYes}}
    </label>
    &nbsp;&nbsp;
    <label>
        <input type="radio" name="{{$key}}" value="0" @if(!$data) checked @endif /> {{$labelNo}}
    </label>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif