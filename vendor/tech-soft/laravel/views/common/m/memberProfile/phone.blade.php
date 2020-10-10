<div class="pb-form">
        <form action="?" method="post" data-ajax-form onsubmit="return false;">

            @if($_memberUser['phone'] && $_memberUser['phoneVerified'])
                <ul class="mui-table-view mui-table-view-chevron">
                    <li class="mui-table-view-cell">
                        <a href="javascript:;" class="mui-navigate-right" onclick="$('#changeBox').show();">
                            <span class="mui-badge mui-badge-inverted">
                                {{$_memberUser['phone']}}
                                [修改]
                            </span>
                            手机
                        </a>
                    </li>
                </ul>
            @else
                @if($_memberUser['phone'])
                    <div class="mui-content-padded">
                        <div class="uk-text-danger uk-text-center">
                            <i class="uk-icon-warning"></i> 手机还没有进行验证
                        </div>
                    </div>
                @endif
            @endif

            <div @if($_memberUser['phone'] && $_memberUser['phoneVerified']) style="display:none;" @endif id="changeBox">
                <div class="mui-input-group">
                    <div class="mui-input-row">
                        <label>手机</label>
                        <input name="phone" type="text" class="mui-input-clear mui-input" value="{{$_memberUser['phone'] or ''}}" placeholder="请输入手机">
                    </div>
                    <div class="mui-input-row captcha">
                        <img data-captcha src="/member/profile_captcha" onclick="this.src='/member/profile_captcha?'+Math.random();"/>
                        <label>图片验证</label>
                        <input type="text" name="captcha" class="mui-input-clear mui-input" placeholder="输入验证码" />
                    </div>
                    <div class="mui-input-row captcha">
                        <div>
                            <button type="button" class="btn btn-sm" data-verify-generate>获取验证码</button>
                            <button type="button" class="btn btn-sm" data-verify-countdown style="display:none;"></button>
                            <button type="button" class="btn btn-sm" data-verify-regenerate style="display:none;">重新获取</button>
                        </div>
                        <label>手机验证码</label>
                        <input type="text" name="verify" class="mui-input-clear mui-input" placeholder="输入验证码" />
                    </div>
                </div>

                <div class="submit">
                    <button type="submit" class="mui-btn mui-btn-block mui-btn-primary">提交</button>
                </div>
            </div>

        </form>
    </div>