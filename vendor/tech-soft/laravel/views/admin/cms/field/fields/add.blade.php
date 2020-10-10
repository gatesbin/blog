<style type="text/css">
    #{{$key}}-fieldsDesigner{}
    #{{$key}}-fieldsDesigner td.name{width:100px;text-align:right;}
    #{{$key}}-fieldsDesigner td.control{}
    #{{$key}}-fieldsDesigner td.control input{width:200px;}
</style>
<div class="label">
    <b>{{$field['title']}}：</b>
</div>
<div class="field">
    <input type="hidden" name="{{$key}}" value="" />
    <div id="{{$key}}-fieldsDesigner">
        <div>
            <table class="raw" v-cloak>
                <tbody>
                    <tr v-for="(field,index) in fields" @click="editField(index)">
                        <td class="name">@{{ field.name }}：</td>
                        <td class="control">
                            <div v-show="field.type=='text'">
                                <input type="text" disabled />
                            </div>
                            <div v-show="field.type=='select'">
                                <select disabled>
                                    <option>选择框</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <a href="javascript:;" class="uk-text-muted" @click="editField(-1)"><i class="uk-icon-plus"></i>增加一个字段</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="{{$key}}-fieldEditor" class="uk-modal">
            <div class="uk-modal-dialog">
                <button type="button" class="uk-modal-close uk-close"></button>
                <div class="uk-modal-header">
                    <h3>字段编辑</h3>
                </div>
                <table class="raw">
                    <tbody>
                    <tr>
                        <td width="100" class="uk-text-right">名称：</td>
                        <td>
                            <input type="text" v-model="editingField.name" />
                        </td>
                    </tr>
                    <tr>
                        <td width="100" class="uk-text-right">类型：</td>
                        <td>
                            <select v-model="editingField.type">
                                <option value="text">单行文本</option>
                                <option value="select">选择框</option>
                            </select>
                        </td>
                    </tr>
                    <tr v-show="editingField.type=='select'">
                        <td width="100" class="uk-text-right">选项值：</td>
                        <td>
                            <textarea v-model="editingField.option" rows="4" placeholder="每行一个"></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="uk-modal-footer uk-text-right">
                    <button type="button" class="uk-button uk-button-default" @click="deleteField" v-show="editingFieldIndex!=-1">删除</button>
                    <button type="button" class="uk-button uk-button-primary" @click="saveField">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>
@if(!empty($field['desc']))
    <div class="desc">
        {!! $field['desc'] !!}
    </div>
@endif
<script>
    $(function () {
        var app = new window.api.vue({
            el: '#{{$key}}-fieldsDesigner',
            data: {
                fields: [],
                editingFieldIndex:-1,
                editingField: {
                    type:'text',
                    name:'',
                    option:'',
                },
            },
            methods:{
                syncFields:function () {
                    $('input[name={{$key}}]').val(JSON.stringify(this.fields));
                },
                editField:function (index) {
                    this.editingFieldIndex = index;
                    if(-1==index){
                        this.editingField = {
                            type:'text',
                            name:'',
                            option:'',
                        };
                    }else{
                        this.editingField = $.extend({},this.fields[index]);
                    }
                    fieldEditor.show();
                },
                saveField:function () {
                    if(!this.editingField.name){
                        window.api.dialog.tipError('字段名称不能为空');
                        return;
                    }
                    if(-1==this.editingFieldIndex){
                        this.fields.push({
                            type:this.editingField.type,
                            name:this.editingField.name,
                            option:this.editingField.option
                        });
                    }else{
                        this.fields[this.editingFieldIndex].type=this.editingField.type;
                        this.fields[this.editingFieldIndex].name=this.editingField.name;
                        this.fields[this.editingFieldIndex].option=this.editingField.option;
                    }
                    fieldEditor.hide();
                    this.syncFields();
                },
                deleteField:function () {
                    this.fields.splice(this.editingFieldIndex,1);
                    fieldEditor.hide();
                    this.syncFields();
                },
            }
        });
        app.syncFields();
        var fieldEditor = UIkit.modal('#{{$key}}-fieldEditor');
    });
</script>