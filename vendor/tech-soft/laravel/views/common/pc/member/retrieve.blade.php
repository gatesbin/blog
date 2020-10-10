<div class="main-container">

    <div class="pb pb-breadcrumb">
        <ul class="uk-breadcrumb">
            <li><a href="/">首页</a></li>
            <li class="uk-active"><span>找回密码</span></li>
        </ul>
    </div>

    <div class="pb pb-account">
        <div class="head">
            <h1>找回密码</h1>
        </div>
        <div class="body">
            <div class="uk-grid">
                <div class="uk-width-1-2">
                    <div class="form uk-text-center">

                        <?php $found = false; ?>
                        @if(\TechSoft\Laravel\Config\ConfigUtil::get('retrieveEmailEnable',false))
                            <a class="uk-button" href="/retrieve/email"><i class="uk-icon-envelope"></i> 通过邮箱找回</a>
                            <?php $found = true; ?>
                        @endif
                        @if(\TechSoft\Laravel\Config\ConfigUtil::get('retrievePhoneEnable',false))
                            <a class="uk-button" href="/retrieve/phone"><i class="uk-icon-tablet"></i> 通过手机找回</a>
                            <?php $found = true; ?>
                        @endif

                        @if(!$found)
                            <div class="uk-alert uk-alert-danger">没有开启任何找回密码方式</div>
                        @endif
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