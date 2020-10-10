@extends('admin::frame')

@section('pageTitle','请您登录')

@section('headAppend')
    @parent
    <style type="text/css">
        body{min-width:auto !important;}
    </style>
@endsection

@section('body')

    <div class="admin-login">
        <div class="head">{!! config('admin.login.head') !!}</div>
        <div class="form">
            <form class="uk-form" method="post" action="?" data-ajax-form>
                <div class="line">
                    账号
                    <input type="text" name="username" value="{{\Illuminate\Support\Facades\Input::get('username','')}}" placeholder=""/>
                </div>
                <div class="line">
                    密码
                    <input type="password" name="password" value="{{\Illuminate\Support\Facades\Input::get('password','')}}" placeholder=""/>
                </div>
                @if(config('admin.login.captcha'))
                    <div class="line">
                        <div class="uk-grid">
                            <div class="uk-width-1-2">
                                <input type="text" name="captcha" value="" placeholder="验证码"/>
                            </div>
                            <div class="uk-width-1-2">
                                <img data-captcha style="height:40px;width:100%;border:1px solid #CCC;border-radius:3px;" data-uk-tooltip title="点击刷新" src="{{action('\TechSoft\Laravel\Admin\Controller\LoginController@captcha')}}" onclick="this.src='{{action('\TechSoft\Laravel\Admin\Controller\LoginController@captcha')}}?'+Math.random();" />
                            </div>
                        </div>
                    </div>
                @endif
                <div class="line">
                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars(\Illuminate\Support\Facades\Input::get('redirect',env('ADMIN_PATH','/admin/'))); ?>">
                    <button type="submit" class="uk-button uk-button-main">提交</button>
                </div>
            </form>
        </div>
    </div>

@endsection
