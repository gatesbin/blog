<div class="label">
    <b>{{$field['title']}}:</b>
</div>
<div class="field">
    <div>
        <textarea id="{{$key}}" name="{{$key}}">{{$data}}</textarea>
    </div>
    <script>
        $(function(){
            window.api.markdown('{{$key}}');
        });
    </script>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif