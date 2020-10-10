@if(\TechSoft\Laravel\Oauth\OauthUtil::hasOauth())
    <div class="pb-oauth">
        <div class="title">
            您还可以使用以下方式登录
        </div>
        <div class="body">
            @if(\TechSoft\Laravel\Oauth\OauthUtil::isWechatMobileEnable())
                <a class="wechat" href="/oauth_login_{{\TechSoft\Laravel\Oauth\OauthType::WECHAT_MOBILE}}?redirect={{urlencode($redirect)}}"><i
                            class="mui-icon mui-icon-weixin"></i></a>
            @endif
            @if(\TechSoft\Laravel\Oauth\OauthUtil::isQQEnable())
                <a class="qq"
                   href="/oauth_login_{{\TechSoft\Laravel\Oauth\OauthType::QQ}}?redirect={{urlencode($redirect)}}"><i
                            class="mui-icon mui-icon-qq"></i></a>
            @endif
            @if(\TechSoft\Laravel\Oauth\OauthUtil::isWeiboEnable())
                <a class="weibo"
                   href="/oauth_login_{{\TechSoft\Laravel\Oauth\OauthType::WEIBO}}?redirect={{urlencode($redirect)}}"><i
                            class="mui-icon mui-icon-weibo"></i></a>
            @endif
        </div>
    </div>
@endif