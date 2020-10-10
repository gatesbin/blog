@extends('admin::frame')

@section('pageTitle','角色')

@section('bodyMenu')
    @if(\TechSoft\Laravel\Admin\Util\AdminPowerUtil::permit('\TechSoft\Laravel\Admin\Controller\SystemController@userRoleEdit'))
        <a href="#" data-dialog-request="{{action('\TechSoft\Laravel\Admin\Controller\SystemController@userRoleEdit')}}" class="btn"><i class="uk-icon-plus"></i> 增加</a>
    @endif
@endsection

@section('bodyContent')

    <div class="block">

        <div data-admin-lister class="admin-lister-container">

            <div class="lister-table"></div>
            <div class="page-container"></div>

        </div>

    </div>

@endsection