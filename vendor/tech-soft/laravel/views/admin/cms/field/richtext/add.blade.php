<div class="label">
    <b>{{$field['title']}}:</b>
</div>
<div class="field">
    <div>
        <script id="{{$key}}" name="{{$key}}" type="text/plain"></script>
    </div>
    <script>
        $(function(){
            window.api.editor('{{$key}}');
        });
    </script>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif