@extends('admin::frameDialog')

@if($adminUser)
    @section('pageTitle','编辑用户')
@else
    @section('pageTitle','增加用户')
@endif


@section('dialogBody')

    <form class="admin-form" method="post" action="?" data-ajax-form onsubmit="return false;">
        <table>
            <tbody>
            <tr>
                <td>
                    <div class="line">
                        <div class="label">用户名</div>
                        <div class="field"><input type="text" name="username" value="{{$adminUser['username'] or ''}}" placeholder="" /></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="line">
                        <div class="label">密码</div>
                        <div class="field"><input type="text" name="password" value="" placeholder="如 留空表示不修改" /></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="line">
                        <div class="label">角色</div>
                        <div class="field">
                            @foreach($roles as $role)
                                <label>
                                    <input type="checkbox" name="roles[]" @if(in_array($role['id'],$adminUserRoleIds)) checked @endif value="{{$role['id']}}" />
                                    {{$role['name']}}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>

@endsection