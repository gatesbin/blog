<div class="label">
    <b>{{$field['title']}}：</b>
</div>
<div class="field">
    <div class="widget-attr" data-attr>
        <div data-list>
            @if(!empty($data))
                @foreach($data as $attr)
                    <div class="item" style="padding:0 0 10px 0;">
                        <input type="text" name="{{$key}}[name][]" value="{{$attr['name']}}" class="name" style="width:6em;" />
                        <span>:</span>
                        <input type="text" name="{{$key}}[value][]" value="{{$attr['value']}}" class="value" style="width:6em;" />
                        <a href="javascript:;" data-remove><i class="uk-icon-remove"></i></a>
                    </div>
                @endforeach
            @endif
        </div>
        <div>
            <a href="javascript:;" class="uk-text-muted" data-add><i class="uk-icon-plus"></i>增加一个</a>
        </div>
        <script type="text/html" data-tpl>
            <div class="item" style="padding:0 0 10px 0;">
                <input type="text" name="{{$key}}[name][]" value="" class="name" placeholder="" style="width:6em;" />
                <span>:</span>
                <input type="text" name="{{$key}}[value][]" value="" class="value" placeholder="" style="width:6em;" />
                <a href="javascript:;" data-remove><i class="uk-icon-remove"></i></a>
            </div>
        </script>
    </div>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif