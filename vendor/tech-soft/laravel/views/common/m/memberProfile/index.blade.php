<ul class="mui-table-view mui-table-view-chevron">
        <li class="mui-table-view-cell mui-media">
            <a class="mui-navigate-right" href="/member/profile_avatar">
                <img class="mui-media-object mui-pull-left" src="{{\TechSoft\Laravel\Assets\AssetsUtil::fixOrDefault($_memberUser['avatarBig'],'assets/lib/img/avatar.png')}}" />
                <span class="mui-badge mui-badge-inverted">
                    [修改]
                </span>
            </a>
        </li>
    </ul>

    <ul class="mui-table-view mui-table-view-chevron">
        <li class="mui-table-view-cell">
            <a href="/member/profile_phone" class="mui-navigate-right">
                @if(empty($_memberUser['phone']))
                    <span class="mui-badge mui-badge-inverted">
                        [立即绑定]
                    </span>
                @else
                    <span class="mui-badge mui-badge-inverted">
                        {{$_memberUser['phone']}}
                        [修改]
                    </span>
                @endif
                手机
            </a>
        </li>
        <li class="mui-table-view-cell">
            <a href="/member/profile_email" class="mui-navigate-right">
                @if(empty($_memberUser['email']))
                    <span class="mui-badge mui-badge-inverted">
                        [立即绑定]
                    </span>
                @else
                    <span class="mui-badge mui-badge-inverted">
                        {{$_memberUser['email']}}
                        [修改]
                    </span>
                @endif
                邮箱
            </a>
        </li>
    </ul>

    <ul class="mui-table-view mui-table-view-chevron">
        @if(\TechSoft\Laravel\Config\ConfigUtil::get('oauthQQEnable',false))
            <li class="mui-table-view-cell">
                @if(\TechSoft\Laravel\Member\MemberUtil::getOauthOpenId($_memberUserId, \TechSoft\Laravel\Oauth\OauthType::QQ))
                    <a class="mui-navigate-right" href="javascript:;">
                        <span class="mui-badge mui-badge-inverted">
                            已绑定
                        </span>
                        绑定QQ
                    </a>
                @else
                    <a class="mui-navigate-right" href="/oauth_login_qq?redirect={{urlencode($base_request_path)}}">
                        <span class="mui-badge mui-badge-inverted">
                            立即绑定
                        </span>
                        绑定QQ
                    </a>
                @endif
            </li>
        @endif
        @if(\TechSoft\Laravel\Config\ConfigUtil::get('oauthWeiboEnable',false))
            <li class="mui-table-view-cell">
                @if(\TechSoft\Laravel\Member\MemberUtil::getOauthOpenId($_memberUserId, \TechSoft\Laravel\Oauth\OauthType::WEIBO))
                    <a class="mui-navigate-right" href="javascript:;">
                        <span class="mui-badge mui-badge-inverted">
                            已绑定
                        </span>
                        绑定微博
                    </a>
                @else
                    <a class="mui-navigate-right" href="/oauth_login_weibo?redirect={{urlencode($base_request_path)}}">
                        <span class="mui-badge mui-badge-inverted">
                            立即绑定
                        </span>
                        绑定微博
                    </a>
                @endif
            </li>
        @endif
    </ul>