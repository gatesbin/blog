<div class="pb">
    <div class="head">修改头像</div>
    <div class="body">
        <div>
            <?php $avatar = 'assets/lib/img/avatar_big.png'; ?>
            @if(!empty($_memberUser['avatarBig']))
                <?php $avatar = $_memberUser['avatarBig']; ?>
            @endif
            <img data-member-image-upload-preview="avatar" style="height:200px;width:200px;border:1px solid #CCC;vertical-align:bottom;" src="{{\TechSoft\Laravel\Assets\AssetsUtil::fix($avatar)}}" />
        </div>

        <div style="margin-top:10px;">
            <div>
                <a href="javascript:;" id="avatarZoomOut" class="uk-button uk-button-default"><i class="uk-icon-search-minus"></i></a>
                <a href="javascript:;" id="avatarZoomIn" class="uk-button uk-button-default"><i class="uk-icon-search-plus"></i></a>
                <div class="uk-form-file uk-display-inline-block">
                    <a class="uk-button"><i class="uk-icon-plus"></i> 选择文件</a>
                    <input type="file" id="avatarSelector" accept="image/*" />
                </div>
                <a href="javascript:;" id="avatarSave" class="uk-button uk-button-primary"><i class="uk-icon-save"></i> 保存头像</a>
            </div>
            <div style="margin-top:10px;">
                <img id="image" style="max-width:80%;max-height:300px;" />
            </div>
        </div>
    </div>
</div>