<div class="ex-page-item-editor" v-if="{{$appModule}}moduleItemEditing!=null && {{$appModule}}moduleItemEditing.type=='Text'">
    <i-form v-bind:label-width="80">
        <form-item label="名称">
            <i-input v-model="{{$appModule}}moduleItemEditing.title"></i-input>
        </form-item>
    </i-form>
</div>