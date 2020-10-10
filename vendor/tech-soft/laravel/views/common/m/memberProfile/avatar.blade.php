<div class="pb-member-avatar">

    <div id="avatarBox">
        <div class="preview" id="oldAvatarBox">
            <img src="{{\TechSoft\Laravel\Assets\AssetsUtil::fixOrDefault($_memberUser['avatarBig'],'assets/lib/img/avatar.png')}}">
        </div>
    </div>

    <div class="editor mui-content-padded" id="editorBox">
        <div class="view">
            <img id="avatarImage" />
        </div>
        <div class="tool">
            <button id="avatarZoomOut" class="mui-btn mui-btn-block iconfont">&#xe76e;</button>
            <button id="avatarZoomIn" class="mui-btn mui-btn-block iconfont">&#xe638;</button>
        </div>
        <div class="save">
            <button id="avatarSave" class="mui-btn mui-btn-block mui-btn-primary">保存头像</button>
        </div>
    </div>

    <div class="upload mui-content-padded">
        <button type="button" class="mui-btn mui-btn-primary mui-btn-block">
            <i class="iconfont">&#xe605;</i>
            选择图片
            <input type="file" id="avatarSelector" accept="image/*" />
        </button>
    </div>

</div>