@extends('admin::frame')

@section('pageTitle','后台首页')

@section('adminScript')
    @parent
    @include('soft::admin.dashboardVersion')
@endsection

@section('bodyContent')

    @include('soft::admin.dashboardDemo')

    <div class="uk-grid">
        <div class="uk-width-1-3">
            <div class="admin-block-stat">
                <div class="icon red">
                    <i class="uk-icon-list"></i>
                </div>
                <a class="number" href="{{action('\App\Http\Controllers\Admin\BlogController@dataList')}}">
                    {{number_format(\TechOnline\Laravel\Dao\ModelUtil::count('blog'))}}
                </a>
                <div class="name">
                    博客总数
                </div>
            </div>
        </div>
        <div class="uk-width-1-3">
            <div class="admin-block-stat">
                <div class="icon green">
                    <i class="uk-icon-comment"></i>
                </div>
                <a class="number" href="{{action('\App\Http\Controllers\Admin\MessageController@dataList')}}">
                    {{number_format(\TechOnline\Laravel\Dao\ModelUtil::count('message'))}}
                </a>
                <div class="name">
                    留言总数
                </div>
            </div>
        </div>
        <div class="uk-width-1-3">
            <div class="admin-block-stat">
                <div class="icon blue">
                    <i class="uk-icon-comments"></i>
                </div>
                <a class="number" href="{{action('\App\Http\Controllers\Admin\BlogCommentController@dataList')}}">
                    {{number_format(\TechOnline\Laravel\Dao\ModelUtil::count('blog_comment'))}}
                </a>
                <div class="name">
                    博客评论
                </div>
            </div>
        </div>
    </div>


    @include('soft::admin.dashboardCopyright')

@endsection

