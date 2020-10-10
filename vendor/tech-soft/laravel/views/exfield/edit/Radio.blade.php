<div class="ex-page-item-editor" v-if="{{$appModule}}moduleItemEditing!=null && {{$appModule}}moduleItemEditing.type=='Radio'">
    <i-form v-bind:label-width="80">
        <form-item label="名称">
            <i-input v-model="{{$appModule}}moduleItemEditing.title"></i-input>
        </form-item>
        <form-item label="选项">
            <div v-for="(listItem,listIndex) in {{$appModule}}moduleItemEditing.data.option">
                <i-input v-model="{{$appModule}}moduleItemEditing.data.option[listIndex]" style="width:200px;"></i-input>
                <i-button type="dashed" icon="md-trash" @click="{{$appModule}}moduleItemEditing.data.option.splice(listIndex,1)"></i-button>
            </div>
            <i-button type="dashed" icon="md-add" @click="{{$appModule}}moduleItemEditing.data={{$appModule}}safePushAndReturnData({{$appModule}}moduleItemEditing.data,'option','');"></i-button>
        </form-item>
    </i-form>
</div>