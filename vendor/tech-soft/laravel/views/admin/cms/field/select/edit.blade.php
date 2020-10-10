<div class="label">
    <b>{{$field['title']}}：</b>
</div>
<div class="field">
    <select name="{{$key}}" style="width:auto;padding-right:2em;">
        @foreach($options as $k=>$v)
            <option value="{{$k}}"@if($k==$data) selected @endif>{{$v}}</option>
        @endforeach
    </select>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif