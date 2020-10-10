<div class="label">
    <b>{{$field['title']}}:</b>
</div>
<div class="field">
    <div data-images-widget>
        <div class="list">
            <div class="add">
                <i class="uk-icon-plus"></i>
            </div>
            <div class="cf"></div>
        </div>
        <input data-images type="hidden" name="{{$key}}" value="<?php if(!empty($data)){ echo htmlspecialchars(json_encode($data)); } ?>" />
    </div>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif
