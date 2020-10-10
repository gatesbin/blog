<ul class="mui-table-view">
    @if(\TechSoft\Laravel\Config\ConfigUtil::get('retrievePhoneEnable',false))
        <li class="mui-table-view-cell">
            <a class="mui-navigate-right" href="/retrieve/phone">
                <i class="iconfont">&#xe600;</i> 通过手机找回密码
            </a>
        </li>
    @endif
    @if(\TechSoft\Laravel\Config\ConfigUtil::get('retrieveEmailEnable',false))
        <li class="mui-table-view-cell">
            <a class="mui-navigate-right" href="/retrieve/email">
                <i class="iconfont">&#xe604;</i> 通过邮箱找回密码
            </a>
        </li>
    @endif
</ul>