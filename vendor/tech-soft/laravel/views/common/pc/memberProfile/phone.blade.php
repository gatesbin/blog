<div class="pb ">

    <div class="head">绑定手机</div>
    <div class="content">

        <form action="?" class="uk-form" method="post" data-ajax-form>

            @if(!\TechSoft\Laravel\Config\ConfigUtil::get('systemSmsEnable'))
                <div class="line">
                    <div class="uk-alert uk-alert-danger">
                        <i class="uk-icon-warning"></i>
                        系统没有开启短信发送服务，短信可能无法发送。
                    </div>
                </div>
            @endif

            @if($_memberUser['phone'] && $_memberUser['phoneVerified'])
                <div class="line">
                    <div class="label">手机:</div>
                    <div class="field">
                        {{$_memberUser['phone']}} <span class="uk-text-success">已验证</span>
                    </div>
                </div>
                <div class="line">
                    <div class="label">&nbsp;</div>
                    <div class="field">
                        <a href="javascript:;" onclick="$('[data-modify-box]').show();" class="uk-button">修改</a>
                    </div>
                </div>
                <div data-modify-box style="display:none;">
                    <div class="line">
                        <div class="label">新手机:</div>
                        <div class="field">
                            <input type="text" name="phone" class="uk-width-1-2" value="" />
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">图形验证：</div>
                        <div class="field">
                            <div class="uk-grid uk-width-1-2" style="width:calc(50% + 10px);">
                                <div class="uk-width-1-3">
                                    <img data-captcha src="/member/profile_captcha" style="width:100%;height:30px;border:1px solid #CCC;border-radius:3px;cursor:pointer;" alt="刷新验证码" onclick="this.src='/member/profile_captcha?'+Math.random();"/>
                                </div>
                                <div class="uk-width-1-3">
                                    <input type="text" name="captcha" class="uk-width-1-1" />
                                </div>
                                <div class="uk-width-1-3">
                                    <button class="uk-button uk-button-default uk-width-1-1" type="button" data-verify-generate>获取验证码</button>
                                    <button class="uk-button uk-button-default uk-disabled uk-width-1-1" type="button" data-verify-countdown style="display:none;"></button>
                                    <button class="uk-button uk-button-default uk-width-1-1" type="button" data-verify-regenerate style="display:none;">重新获取</button>
                                </div>
                            </div>
                            <div class="help">
                            </div>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">手机验证：</div>
                        <div class="field">
                            <input type="text" name="verify" class="uk-width-1-2" />
                            <div class="help">
                            </div>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">&nbsp;</div>
                        <div class="field">
                            <button type="submit" class="uk-button uk-button-primary">提交</button>
                        </div>
                    </div>
                </div>
            @else
                @if($_memberUser['phone'])
                    <div class="line">
                        <div class="uk-alert uk-alert-danger uk-width-1-2">
                            手机还没有进行验证
                        </div>
                    </div>
                @endif
                <div class="line">
                    <div class="label">手机:</div>
                    <div class="field">
                        <input type="text" name="phone" class="uk-width-1-2" value="{{$_memberUser['phone'] or ''}}" />
                    </div>
                </div>
                <div class="line">
                    <div class="label">图形验证：</div>
                    <div class="field">
                        <div class="uk-grid uk-width-1-2" style="width:calc(50% + 10px);">
                            <div class="uk-width-1-3">
                                <img data-captcha src="/member/profile_captcha" style="width:100%;height:30px;border:1px solid #CCC;border-radius:3px;cursor:pointer;" alt="刷新验证码" onclick="this.src='/member/profile_captcha?'+Math.random();"/>
                            </div>
                            <div class="uk-width-1-3">
                                <input type="text" name="captcha" class="uk-width-1-1" />
                            </div>
                            <div class="uk-width-1-3">
                                <button class="uk-button uk-button-default uk-width-1-1" type="button" data-verify-generate>获取验证码</button>
                                <button class="uk-button uk-button-default uk-width-1-1 uk-disabled" type="button" data-verify-countdown style="display:none;"></button>
                                <button class="uk-button uk-button-default uk-width-1-1" type="button" data-verify-regenerate style="display:none;">重新获取</button>
                            </div>
                        </div>
                        <div class="help">
                        </div>
                    </div>
                </div>
                <div class="line">
                    <div class="label">手机验证：</div>
                    <div class="field">
                        <input type="text" name="verify" class="uk-width-1-2" />
                        <div class="help">
                        </div>
                    </div>
                </div>
                <div class="line">
                    <div class="label">&nbsp;</div>
                    <div class="field">
                        <button type="submit" class="uk-button uk-button-primary">提交</button>
                    </div>
                </div>
            @endif
        </form>

    </div>
</div>