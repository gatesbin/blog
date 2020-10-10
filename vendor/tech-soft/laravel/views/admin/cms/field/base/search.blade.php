@if(empty($field['searchType']))
    <div class="item">
        <div class="label">
            <b>{{$field['title']}}:</b>
        </div>
        <div class="field">
            <input type="text" value="" data-search-field="{{$key}}" data-search-type="like" />
        </div>
    </div>
@else
    @if(in_array('range',$field['searchType']))
        <div class="item">
            <div class="label">
                <b>{{$field['title']}}:</b>
            </div>
            <div class="field">
                <input type="text" value="" data-search-field="{{$key}}" data-search-group="{{$key}}Range" data-search-type="min" style="width:8em;" />
                -
                <input type="text" value="" data-search-field="{{$key}}" data-search-group="{{$key}}Range" data-search-type="max" style="width:8em;" />
            </div>
        </div>
    @endif
    @if(in_array('equal',$field['searchType']))
        <div class="item">
            <div class="label">
                <b>{{$field['title']}}:</b>
            </div>
            <div class="field">
                <input type="text" value="" data-search-field="{{$key}}" data-search-type="equal" />
            </div>
        </div>
    @endif
@endif