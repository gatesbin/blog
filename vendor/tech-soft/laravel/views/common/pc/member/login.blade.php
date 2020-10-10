<div class="main-container">

    <div class="pb pb-breadcrumb">
        <ul class="uk-breadcrumb">
            <li><a href="/">首页</a></li>
            <li class="uk-active"><span>用户登录</span></li>
        </ul>
    </div>

    <div class="pb pb-account">
        <div class="head">
            <h1>用户登录</h1>
        </div>
        <div class="body">
            <div class="uk-grid">
                <div class="uk-width-1-1 uk-width-medium-1-2">
                    <div class="form">
                        <form action="?" method="post" class="uk-form" data-ajax-form>
                            <div class="line">
                                <div class="label">用户：</div>
                                <div class="field">
                                    <input type="text" name="username" class="uk-width-1-1 uk-width-medium-2-3"/>
                                    <div class="help">
                                    </div>
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">密码：</div>
                                <div class="field">
                                    <input type="password" name="password"
                                           class="uk-width-1-1 uk-width-medium-2-3"/>
                                    <div class="help">
                                    </div>
                                </div>
                            </div>
                            @if(\TechSoft\Laravel\Config\ConfigUtil::get('loginCaptchaEnable',false))
                                <div class="line">
                                    <div class="label">图片验证：</div>
                                    <div class="field">
                                        <div class="uk-grid">
                                            <div class="uk-width-1-1 uk-width-medium-2-3">
                                                <input type="text" name="captcha"
                                                       class="uk-width-1-1 uk-width-medium-1-1"/>
                                            </div>
                                            <div class="uk-width-1-1 uk-width-medium-1-3">
                                                <img src="/login/captcha"
                                                     style="height:30px;cursor:pointer;border:1px solid #EEE;border-radius:3px;"
                                                     data-captcha
                                                     onclick="$(this).attr('src','/login/captcha?'+Math.random())"/>
                                            </div>
                                        </div>
                                        <div class="help">
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="line">
                                <div class="field">
                                    <button type="submit" class="uk-button uk-button-primary">提交</button>
                                </div>
                            </div>
                            <input type="hidden" name="redirect" value="{{$redirect}}"/>
                        </form>
                    </div>
                </div>
                <div class="uk-width-1-1 uk-width-medium-1-2">
                    <div class="text">
                        <div>
                            没有账号？<a href="/register?redirect={{urlencode($redirect)}}">马上注册</a>
                        </div>
                        <div>
                            忘记密码？<a href="/retrieve?redirect={{urlencode($redirect)}}">找回密码</a>
                        </div>
                        @if(\TechSoft\Laravel\Oauth\OauthUtil::hasOauth())
                            <div>
                                您还可以使用以下方式登录
                            </div>
                            <div class="oauth">
                                @if(\TechSoft\Laravel\Oauth\OauthUtil::isWechatEnable())
                                    <a class="item wechat" href="javascript:;"
                                       data-dialog-request="/oauth_login_{{\TechSoft\Laravel\Oauth\OauthType::WECHAT}}?redirect={{urlencode($redirect)}}"><i
                                                class="uk-icon-wechat"></i></a>
                                @endif
                                @if(\TechSoft\Laravel\Oauth\OauthUtil::isQQEnable())
                                    <a class="item qq"
                                       href="/oauth_login_{{\TechSoft\Laravel\Oauth\OauthType::QQ}}?redirect={{urlencode($redirect)}}"><i
                                                class="uk-icon-qq"></i></a>
                                @endif
                                @if(\TechSoft\Laravel\Oauth\OauthUtil::isWeiboEnable())
                                    <a class="item weibo"
                                       href="/oauth_login_{{\TechSoft\Laravel\Oauth\OauthType::WEIBO}}?redirect={{urlencode($redirect)}}"><i
                                                class="uk-icon-weibo"></i></a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>