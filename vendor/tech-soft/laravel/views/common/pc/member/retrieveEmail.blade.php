<div class="main-container">

    <div class="pb pb-breadcrumb">
        <ul class="uk-breadcrumb">
            <li><a href="/">首页</a></li>
            <li class="uk-active"><span>通过邮箱找回密码</span></li>
        </ul>
    </div>

    <div class="pb pb-account">
        <div class="head">
            <h1>通过邮箱找回密码</h1>
        </div>
        <div class="body">
            <div class="uk-grid">
                <div class="uk-width-1-2">
                    <div class="form">

                        <form action="?" method="post" class="uk-form" data-ajax-form>
                            <div class="line">
                                <div class="label">图片验证：</div>
                                <div class="field">
                                    <div class="uk-grid">
                                        <div class="uk-width-2-3">
                                            <input type="text" name="captcha" class="uk-width-1-1" />
                                        </div>
                                        <div class="uk-width-1-3">
                                            <img src="/retrieve/captcha" style="height:30px;cursor:pointer;border:1px solid #EEE;border-radius:3px;" data-captcha onclick="$(this).attr('src','/retrieve/captcha?'+Math.random())" />
                                        </div>
                                    </div>
                                    <div class="help">
                                    </div>
                                </div>
                            </div>
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
                                <div class="label">邮箱验证码：</div>
                                <div class="field">
                                    <input type="text" name="verify" class="uk-width-2-3" />
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
                            还没有账号？<a href="/register?redirect={{urlencode($redirect)}}">马上注册</a>
                        </div>
                        <div>
                            已想起来密码？<a href="/login?redirect={{urlencode($redirect)}}">马上登录</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>