<div class="item">
    <div class="label">
        <b>{{$field['title']}}:</b>
    </div>
    <div class="field">
        <select data-search-field="{{$key}}" data-search-type="equal">
            <option value="">[全部]</option>
            @foreach($options as $k=>$v)
                <option value="{{$k}}">{{$v}}</option>
            @endforeach
        </select>
    </div>
</div>