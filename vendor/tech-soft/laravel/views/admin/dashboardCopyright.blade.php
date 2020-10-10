@if(!defined('__CONFIG_COPYRIGHT_HIDE__'))
    <div class="uk-grid">
        <div class="uk-width-1-1 uk-width-medium-1-2">
            <div class="admin-block">
                <div class="head">系统概况</div>
                <div class="body" style="height:160px;">
                    <div>
                        版本：<span>V{{\App\Constant\AppConstant::VERSION}}</span>
                    </div>
                    <div style="padding:10px 0 0 0;" data-admin-version="{{\App\Constant\AppConstant::VERSION}}"></div>
                    <div style="padding:10px 0 0 0;" data-admin-auth></div>
                </div>
            </div>
        </div>
        <div class="uk-width-1-1 uk-width-medium-1-2">
            <div class="admin-block">
                <div class="head">版权说明</div>
                <div class="body" style="height:160px;">
                    <table class="uk-table">
                        <tbody>
                        <tr>
                            <td>
                                使用中遇到问题请 <a href="http://{{__BASE_SITE_SOFTWARE__}}/product/{{\App\Constant\AppConstant::APP}}" target="_blank">反馈给我们</a>，如需系统定制请 <a href="http://{{__BASE_SITE_SOFTWARE__}}/product/{{\App\Constant\AppConstant::APP}}" target="_blank">联系我们</a>。
                            </td>
                        </tr>
                        <tr>
                            <td>
                                请您在使用过程中始终保留版权，如需商业授权请<a href="http://{{__BASE_SITE_SOFTWARE__}}/product/{{\App\Constant\AppConstant::APP}}" target="_blank">联系我们进行授权</a>。
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="http://{{__BASE_SITE_SOFTWARE__}}/product/{{\App\Constant\AppConstant::APP}}" target="_blank">&copy; {{__BASE_SITE__}}</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
