<div class="main-container">

    <div class="pb pb-breadcrumb">
        <ul class="uk-breadcrumb">
            <li><a href="/">首页</a></li>
            <li class="uk-active"><span>设置新密码</span></li>
        </ul>
    </div>

    <div class="pb pb-account">
        <div class="head">
            <h1>设置新密码</h1>
        </div>
        <div class="body">
            <div class="uk-grid">
                <div class="uk-width-1-2">
                    <div class="form">
                        <form action="?" method="post" class="uk-form" data-ajax-form>
                            <div class="line">
                                <div class="label">
                                    账户
                                </div>
                                <div class="field">
                                    @if(!empty($memberUser['username']))
                                        {{\TechOnline\Utils\StrUtil::mask($memberUser['username'])}}
                                    @elseif(!empty($memberUser['email']))
                                        {{\TechOnline\Utils\StrUtil::mask($memberUser['email'])}}
                                    @elseif(!empty($memberUser['phone']))
                                        {{\TechOnline\Utils\StrUtil::mask($memberUser['phone'])}}
                                    @endif
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">新密码：</div>
                                <div class="field">
                                    <input type="password" name="password" />
                                    <div class="help">
                                    </div>
                                </div>
                            </div>
                            <div class="line">
                                <div class="label">重复密码：</div>
                                <div class="field">
                                    <input type="password" name="passwordRepeat" />
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