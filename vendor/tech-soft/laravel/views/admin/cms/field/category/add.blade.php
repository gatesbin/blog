<div class="label">
    <b>{{$field['title']}}:</b>
</div>
<div class="field">
    <select name="{{$key}}" style="width:auto;padding-right:2em;">
        <option value="">[请选择]</option>
        @foreach($options as $option)
            <option value="{{$option['id']}}">{{$option['title']}}</option>
        @endforeach
    </select>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif