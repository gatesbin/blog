<form action="?" method="post" data-ajax-form onsubmit="return false;">

    <div class="pb-form">
        <div class="mui-input-group">
            <div class="mui-input-row">
                <label>用户名</label>
                <input name="username" type="text" class="mui-input-clear mui-input" placeholder="请输入用户名">
            </div>
            <div class="mui-input-row">
                <label>密码</label>
                <input name="password" type="password" class="mui-input mui-input-password" placeholder="请输入密码">
            </div>
            @if(\TechSoft\Laravel\Config\ConfigUtil::get('loginCaptchaEnable',false))
                <div class="mui-input-row captcha">
                    <img data-captcha src="/login/captcha" onclick="this.src='/login/captcha?'+Math.random();"/>
                    <label>图片验证</label>
                    <input type="text" name="captcha" class="mui-input-clear mui-input" placeholder="输入验证码" />
                </div>
            @endif
            <input type="hidden" name="redirect" value="{{\Illuminate\Support\Facades\Input::get('redirect','')}}">
        </div>
        <div class="submit">
            <button type="submit" class="mui-btn mui-btn-block mui-btn-primary">登录</button>
        </div>
    </div>

</form>

<div class="pb-center-link">
    @if(!\TechSoft\Laravel\Config\ConfigUtil::get('registerDisable',false))
        <a href="/register?redirect={{urlencode($redirect)}}">注册账号</a>
    @endif
    @if(!\TechSoft\Laravel\Config\ConfigUtil::get('retrieveDisable',false))
        <a href="/retrieve?redirect={{urlencode($redirect)}}">忘记密码</a>
    @endif
</div>

@include('soft::common.m.member.oauth')