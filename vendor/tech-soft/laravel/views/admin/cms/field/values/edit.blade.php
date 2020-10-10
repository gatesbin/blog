<div class="label">
    <b>{{$field['title']}}：</b>
</div>
<div class="field">
    <div data-values-widget>
        <input data-values type="hidden" name="{{$key}}" value="<?php echo htmlspecialchars(json_encode($data)); ?>" />
        <div class="list">
        </div>
        <div class="add">
            <i class="uk-icon-plus"></i>
        </div>
    </div>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif