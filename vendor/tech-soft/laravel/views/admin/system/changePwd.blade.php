@extends('admin::frame')

@section('pageTitle','修改密码')

@section('bodyContent')

    <div class="block" style="width:400px;">
        <div class="body">
            <form class="admin-form" method="post" action="?" data-ajax-form onsubmit="return false;">
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <div class="line">
                                <div class="label">旧密码</div>
                                <div class="field"><input type="password" name="passwordOld" value="" placeholder="" /></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="line">
                                <div class="label">新密码</div>
                                <div class="field"><input type="password" name="passwordNew" value="" placeholder=""/></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="line">
                                <div class="label">重复新密码</div>
                                <div class="field"><input type="password" name="passwordNewRepeat" value="" placeholder=""/></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="line">
                                <button type="submit" class="uk-button uk-button-primary">保存</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

@endsection