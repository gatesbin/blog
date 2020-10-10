<form action="?" method="post" data-ajax-form onsubmit="return false;">

    <div class="pb-form">
        <div class="mui-input-group">
            <div class="mui-input-row">
                <label>用户名</label>
                <input name="username" type="text" class="mui-input-clear mui-input" placeholder="请输入用户名">
            </div>
            <div class="mui-input-row captcha">
                <img data-captcha src="/register/captcha" onclick="this.src='/register/captcha?'+Math.random();"/>
                <label>图片验证</label>
                <input type="text" name="captcha" onblur="doCheckCaptcha()" class="mui-input-clear mui-input" placeholder="输入验证码" />
            </div>
            <div class="mui-input-row captcha">
                <label>&nbsp;</label>
                <div class="value">
                    <text class="ub-text-muted" data-captcha-status="tip"><i class="iconfont icon-weibiaoti2"></i> 输入图片验证码验证</text>
                    <text class="ub-text-muted" data-captcha-status="loading" style="display:none;"><i class="iconfont icon-Refresh"></i> 正在验证</text>
                    <text class="ub-text-success" data-captcha-status="success" style="display:none;"><i class="iconfont icon-checked"></i> 验证通过</text>
                    <text class="ub-text-danger" data-captcha-status="error" style="display:none;"><i class="iconfont icon-ioscloseoutline"></i> 验证失败</text>
                </div>
            </div>
            @if(\TechSoft\Laravel\Config\ConfigUtil::get('registerPhoneEnable'))
                <div class="mui-input-row">
                    <label>手机</label>
                    <input name="phone" type="text" class="mui-input-clear mui-input" placeholder="请输入手机">
                </div>
                <div class="mui-input-row captcha">
                    <div>
                        <button type="button" class="btn btn-sm" data-phone-verify-generate>获取验证码</button>
                        <button type="button" class="btn btn-sm" data-phone-verify-countdown style="display:none;"></button>
                        <button type="button" class="btn btn-sm" data-phone-verify-regenerate style="display:none;">重新获取</button>
                    </div>
                    <label>手机验证码</label>
                    <input type="text" name="phoneVerify" class="mui-input-clear mui-input" placeholder="输入验证码" />
                </div>
            @endif
            @if(\TechSoft\Laravel\Config\ConfigUtil::get('registerEmailEnable'))
                <div class="mui-input-row">
                    <label>邮箱</label>
                    <input name="email" type="text" class="mui-input-clear mui-input" placeholder="请输入手机">
                </div>
                <div class="mui-input-row captcha">
                    <div>
                        <button type="button" class="btn btn-sm" data-email-verify-generate>获取验证码</button>
                        <button type="button" class="btn btn-sm" data-email-verify-countdown style="display:none;"></button>
                        <button type="button" class="btn btn-sm" data-email-verify-regenerate style="display:none;">重新获取</button>
                    </div>
                    <label>邮箱验证码</label>
                    <input type="text" name="emailVerify" class="mui-input-clear mui-input" placeholder="输入验证码" />
                </div>
            @endif
            <div class="mui-input-row">
                <label>密码</label>
                <input name="password" type="password" class="mui-input-password mui-input" placeholder="请输入密码">
            </div>
            <div class="mui-input-row">
                <label>重复密码</label>
                <input name="passwordRepeat" type="password" class="mui-input-password mui-input" placeholder="再输一次密码">
            </div>
            <input type="hidden" name="redirect" value="{{\Illuminate\Support\Facades\Input::get('redirect','')}}">
        </div>

        <div class="submit">
            <button type="submit" class="mui-btn mui-btn-block mui-btn-primary">注册</button>
        </div>
    </div>

</form>

<div class="pb-center-link">
    <a href="/login?redirect={{urlencode($redirect)}}">立即登录</a>
    @if(!\TechSoft\Laravel\Config\ConfigUtil::get('retrieveDisable',false))
        <a href="/retrieve?redirect={{urlencode($redirect)}}">忘记密码</a>
    @endif
</div>

@include('soft::common.m.member.oauth')