<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="{{\TechSoft\Laravel\Assets\AssetsUtil::fixOrDefault(\TechSoft\Laravel\Config\ConfigUtil::get('siteFavIco'),'default_favicon.ico')}}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="keywords" content="@yield('pageKeywords',\TechSoft\Laravel\Config\ConfigUtil::get('siteKeywords'))">
    <meta name="description" content="@yield('pageDescription',\TechSoft\Laravel\Config\ConfigUtil::get('siteDescription'))">
    <meta name="viewport" content="width=device-width, minimum-scale=0.5, maximum-scale=5, user-scalable=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <script src="@assets('assets/init.js')"></script>
    <title>@section('pageTitle')@yield('pageTitleMain') - {{\TechSoft\Laravel\Config\ConfigUtil::get('siteName')}}@show</title>
    @section('headScript')
        <link rel="stylesheet" href="@assets('assets/uikit/css/ui.css')"/>
        <link rel="stylesheet" href="@assets('theme/default/pc/css/style.css')"/>
    @show
    @section('headAppend')@show
    {!! \TechSoft\Laravel\Config\ConfigUtil::get('systemCounter') !!}
</head>
<body>
@section('body')

    <header style="background-image:url('{{\TechSoft\Laravel\Assets\AssetsUtil::fixOrDefault(\TechSoft\Laravel\Config\ConfigUtil::get('blogBackground'),'theme/default/pc/img/bg.jpg')}}');">
        <div class="container">
            <div class="avatar">
                <img src="{{\TechSoft\Laravel\Assets\AssetsUtil::fixOrDefault(\TechSoft\Laravel\Config\ConfigUtil::get('siteLogo'),'/placeholder/200x200')}}" />
            </div>
            <h1><a href="/">{{\TechSoft\Laravel\Config\ConfigUtil::get('blogName','[博客名]')}}</a></h1>
            <h2>{{\TechSoft\Laravel\Config\ConfigUtil::get('blogSlogan','[博客标语]')}}</h2>
            <ul class="menu">
                <li><a href="/" data-uk-tooltip title="首页"><i class="uk-icon-home"></i></a></li>
                @if(\TechSoft\Laravel\Config\ConfigUtil::get('blogMessageEnable'))
                    <li><a href="/message" data-uk-tooltip title="留言"><i class="uk-icon-comment"></i></a></li>
                @endif
                @if(\TechSoft\Laravel\Config\ConfigUtil::get('contactEmail'))
                    <li><a data-uk-tooltip title="邮箱:{{\TechSoft\Laravel\Config\ConfigUtil::get('contactEmail')}}" href="mailto:{{\TechSoft\Laravel\Config\ConfigUtil::get('contactEmail')}}"><i class="uk-icon-envelope-o"></i></a></li>
                @endif
                @if(\TechSoft\Laravel\Config\ConfigUtil::get('contactWeibo'))
                    <li><a data-uk-tooltip title="微博:{{\TechSoft\Laravel\Config\ConfigUtil::get('contactWeibo')}}" href="{{\TechSoft\Laravel\Config\ConfigUtil::get('contactWeibo')}}" target="_blank"><i class="uk-icon-weibo"></i></a></li>
                @endif
                @if(\TechSoft\Laravel\Config\ConfigUtil::get('contactWechat'))
                    <li><a data-uk-tooltip title="微信:{{\TechSoft\Laravel\Config\ConfigUtil::get('contactWechat')}}" href="javascript:;"><i class="uk-icon-wechat"></i></a></li>
                @endif
                @if(\TechSoft\Laravel\Config\ConfigUtil::get('contactQQ'))
                    <li><a data-uk-tooltip title="QQ:{{\TechSoft\Laravel\Config\ConfigUtil::get('contactQQ')}}" href="javascript:;"><i class="uk-icon-qq"></i></a></li>
                @endif
                <li><a href="javascript:javascript:;" id="fullScreenTrigger" data-uk-tooltip title="全屏"><i class="uk-icon-expand"></i></a></li>
            </ul>
            <div class="introduction">
                {!! \TechSoft\Laravel\Util\HtmlUtil::text2html(\TechSoft\Laravel\Config\ConfigUtil::get('blogIntroduction','[个人介绍]')) !!}
            </div>
        </div>
        <div class="copyright">
            <a href="http://www.miitbeian.gov.cn/" target="_blank">{{\TechSoft\Laravel\Config\ConfigUtil::get('siteBeian','[备案编号]')}}</a>
            &copy;
            {{\TechSoft\Laravel\Config\ConfigUtil::get('siteDomain','[网站域名]')}}
        </div>
    </header>

    <div id="body">
        <div class="container">
            @section('bodyContent')
            @show
        </div>
    </div>

@show
@section('bodyScript')
    <script src="@assets('assets/main/js/basic.js')"></script>
@show
@section('bodyAppend')@show
</body>
</html>
