<form action="?" method="post" data-ajax-form onsubmit="return false;">

    <div class="pb-form">
        <div class="mui-input-group">
            <div class="mui-input-row">
                <label>用户名</label>
                <div class="value">{{$_memberUser['username']}}</div>
            </div>
        </div>

        <div class="mui-input-group-title">性别</div>
        <div class="mui-input-group">
            <div class="mui-input-row mui-radio mui-left">
                <label>男</label>
                <input name="gender" type="radio" value="{{\TechSoft\Laravel\Member\Type\Gender::MALE}}" @if($_memberUser['gender']==\TechSoft\Laravel\Member\Type\Gender::MALE) checked @endif />
            </div>
            <div class="mui-input-row mui-radio mui-left">
                <label>女</label>
                <input name="gender" type="radio" value="{{\TechSoft\Laravel\Member\Type\Gender::FEMALE}}" @if($_memberUser['gender']==\TechSoft\Laravel\Member\Type\Gender::FEMALE) checked @endif />
            </div>
            <div class="mui-input-row mui-radio mui-left">
                <label>保密</label>
                <input name="gender" type="radio" value="{{\TechSoft\Laravel\Member\Type\Gender::UNKNOWN}}" @if($_memberUser['gender']==\TechSoft\Laravel\Member\Type\Gender::UNKNOWN) checked @endif />
            </div>
        </div>

        <div class="mui-input-group">
            <div class="mui-input-row">
                <label>真实姓名</label>
                <input name="realname" type="text" class="mui-input-clear mui-input" placeholder="输入姓名" value="{{$_memberUser['realname'] or ''}}" />
            </div>
            <div class="mui-input-row" style="height:80px;">
                <label>个性签名</label>
                <textarea rows="2" name="signature" type="text" class="mui-input" placeholder="输入个性签名">{{$_memberUser['signature'] or ''}}</textarea>
            </div>
        </div>


        <div class="submit">
            <button type="submit" class="mui-btn mui-btn-block mui-btn-primary">保存</button>
        </div>
    </div>

</form>