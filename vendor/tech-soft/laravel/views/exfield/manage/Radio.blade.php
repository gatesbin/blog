<div class="ex-field-item-manage" v-if="moduleItem.type=='Radio'">
    <i-button type="dashed" @click="{{$appModule}}editItem(moduleIndex)">单选 - @{{ moduleItem.title }}</i-button>
    <i-button type="dashed" icon="md-trash" @click="{{$appModule}}deleteItem(moduleIndex)"></i-button>
</div>