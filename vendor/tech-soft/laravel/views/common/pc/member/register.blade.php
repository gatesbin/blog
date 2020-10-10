<div class="main-container">

    <div class="pb pb-breadcrumb">
        <ul class="uk-breadcrumb">
            <li><a href="/">首页</a></li>
            <li class="uk-active"><span>用户注册</span></li>
        </ul>
    </div>

    <div class="pb pb-account">
        <div class="head">
            <h1>用户注册</h1>
        </div>
        <div class="body">
            <div class="uk-grid">
                <div class="uk-width-1-2">
                    <div class="form">
                        <form action="?" method="post" class="uk-form" data-ajax-form>
                            <div class="line">
                                <div class="label">用户名：</div>
                                <div class="field">
                                    <input type="text" name="username" class="uk-width-2-3" />
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">图片验证：</div>
                                <div class="field">
                                    <div class="uk-grid">
                                        <div class="uk-width-2-3">
                                            <input type="text" name="captcha" onblur="doCheckCaptcha()" class="uk-width-1-1" />
                                        </div>
                                        <div class="uk-width-1-3">
                                            <img src="/register/captcha" style="height:30px;cursor:pointer;border:1px solid #EEE;border-radius:3px;" data-captcha onclick="$(this).attr('src','/register/captcha?'+Math.random())" />
                                        </div>
                                    </div>
                                    <div class="help">
                                        <text class="ub-text-muted" data-captcha-status="tip"><i class="iconfont icon-weibiaoti2"></i> 输入图片验证码验证</text>
                                        <text class="ub-text-muted" data-captcha-status="loading" style="display:none;"><i class="iconfont icon-Refresh"></i> 正在验证</text>
                                        <text class="ub-text-success" data-captcha-status="success" style="display:none;"><i class="iconfont icon-checked"></i> 验证通过</text>
                                        <text class="ub-text-danger" data-captcha-status="error" style="display:none;"><i class="iconfont icon-ioscloseoutline"></i> 验证失败</text>
                                    </div>
                                </div>
                            </div>
                            @if(\TechSoft\Laravel\Config\ConfigUtil::get('registerPhoneEnable'))
                                <div class="line">
                                    <div class="label">手机：</div>
                                    <div class="field">
                                        <div class="uk-grid">
                                            <div class="uk-width-2-3">
                                                <input type="text" name="phone" class="uk-width-1-1" value="" />
                                            </div>
                                            <div class="uk-width-1-3">
                                                <button class="uk-button uk-button-default uk-width-1-1" type="button" data-phone-verify-generate>获取验证码</button>
                                                <button class="uk-button uk-button-default uk-width-1-1 uk-disabled" type="button" data-phone-verify-countdown style="display:none;"></button>
                                                <button class="uk-button uk-button-default uk-width-1-1" type="button" data-phone-verify-regenerate style="display:none;">重新获取</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="line">
                                    <div class="label">手机验证：</div>
                                    <div class="field">
                                        <input type="text" name="phoneVerify" class="uk-width-2-3" value="" placeholder="" />
                                    </div>
                                </div>
                            @endif
                            @if(\TechSoft\Laravel\Config\ConfigUtil::get('registerEmailEnable'))
                                <div class="line">
                                    <div class="label">邮箱：</div>
                                    <div class="field">
                                        <div class="uk-grid">
                                            <div class="uk-width-2-3">
                                                <input type="text" name="email" class="uk-width-1-1" value="" />
                                            </div>
                                            <div class="uk-width-1-3">
                                                <button class="uk-button uk-button-default uk-width-1-1" type="button" data-email-verify-generate>获取验证码</button>
                                                <button class="uk-button uk-button-default uk-width-1-1 uk-disabled" type="button" data-email-verify-countdown style="display:none;"></button>
                                                <button class="uk-button uk-button-default uk-width-1-1" type="button" data-email-verify-regenerate style="display:none;">重新获取</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="line">
                                    <div class="label">邮箱验证：</div>
                                    <div class="field">
                                        <input type="text" name="emailVerify" class="uk-width-2-3" value="" placeholder="" />
                                    </div>
                                </div>
                            @endif
                            <div class="line">
                                <div class="label">密码：</div>
                                <div class="field">
                                    <input type="password" name="password" class="uk-width-2-3" />
                                    <div class="help">
                                    </div>
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">重复密码：</div>
                                <div class="field">
                                    <input type="password" name="passwordRepeat" class="uk-width-2-3" />
                                    <div class="help">
                                    </div>
                                </div>
                            </div>
                            <div class="line">
                                <div class="field">
                                    <button type="submit" class="uk-button uk-button-primary">提交</button>
                                </div>
                            </div>
                            <input type="hidden" name="redirect" value="{{$redirect}}" />
                        </form>
                    </div>
                </div>
                <div class="uk-width-1-2">
                    <div class="text">
                        <div>
                            已有账号？<a href="/login?redirect={{urlencode($redirect)}}">马上登录</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>