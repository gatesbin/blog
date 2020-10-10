@if(empty($field['searchType']))
    <div class="item">
        <div class="label">
            <b>{{$field['title']}}:</b>
        </div>
        <div class="field">
            <input data-datetime-picker type="text" value="" data-search-field="{{$key}}" data-search-type="equal" style="width:11em;" />
        </div>
    </div>
@else
    @if(in_array('range',$field['searchType']))
        <div class="item">
            <div class="label">
                <b>{{$field['title']}}:</b>
            </div>
            <div class="field">
                <input data-datetime-picker type="text" value="" data-search-field="{{$key}}" data-search-group="{{$key}}Range" data-search-type="min" style="width:11em;" />
                -
                <input data-datetime-picker type="text" value="" data-search-field="{{$key}}" data-search-group="{{$key}}Range" data-search-type="max" style="width:11em;" />
            </div>
        </div>
    @endif
    @if(in_array('rangeDate',$field['searchType']))
        <div class="item">
            <div class="label">
                <b>{{$field['title']}}:</b>
            </div>
            <div class="field">
                <input data-date-picker type="text" value="" data-search-field="{{$key}}" data-search-group="{{$key}}Range" data-search-type="min" style="width:7em;text-align:center;" />
                -
                <input data-date-picker type="text" value="" data-search-field="{{$key}}" data-search-group="{{$key}}Range" data-search-type="max" style="width:7em;text-align:center;" />
            </div>
        </div>
    @endif
@endif