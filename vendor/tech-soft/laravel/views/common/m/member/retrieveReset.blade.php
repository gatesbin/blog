<div class="pb-form">

        <form action="?" method="post" data-ajax-form onsubmit="return false;">

            <div class="mui-input-group">
                <div class="mui-input-row">
                    <label>账户</label>
                    <div class="value">
                        @if(!empty($memberUser['username']))
                            {{\TechOnline\Utils\StrUtil::mask($memberUser['username'])}}
                        @elseif(!empty($memberUser['email']))
                            {{\TechOnline\Utils\StrUtil::mask($memberUser['email'])}}
                        @elseif(!empty($memberUser['phone']))
                            {{\TechOnline\Utils\StrUtil::mask($memberUser['phone'])}}
                        @else
                            UID-{{$memberUser['id'] or 0}}
                        @endif
                    </div>
                </div>
                <div class="mui-input-row">
                    <label>新密码</label>
                    <input name="password" type="password" class="mui-input-password mui-input" placeholder="请输入密码">
                </div>
                <div class="mui-input-row">
                    <label>重复密码</label>
                    <input name="passwordRepeat" type="password" class="mui-input-password mui-input" placeholder="再输一次密码">
                </div>
            </div>

            <div class="submit">
                <button type="submit" class="mui-btn mui-btn-block mui-btn-primary">确定</button>
            </div>

        </form>

    </div>