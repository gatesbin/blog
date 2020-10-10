<form action="?" method="post" data-ajax-form onsubmit="return false;">

    <div style="text-align:center;padding:20px 0 0 0;">
        <img style="height:80px;" src="{{$oauthUserInfo['avatar'] or '/assets/lib/img/none.png'}}" />
    </div>


    <div class="pb-form">
        <div class="mui-input-group">
            <div class="mui-input-row">
                <label>用户名</label>
                <input name="username" type="text" value="{{$oauthUserInfo['username'] or ''}}" class="mui-input-clear mui-input" placeholder="请输入用户名">
            </div>
        </div>
        <div class="submit">
            <button type="submit" class="mui-btn mui-btn-block mui-btn-primary">确定绑定</button>
        </div>
    </div>

    <input type="hidden" name="redirect" value="{{$redirect or ''}}" />

</form>