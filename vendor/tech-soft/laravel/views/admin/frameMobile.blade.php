<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
    <meta name="keywords" content="@yield('pageKeywords','')">
    <meta name="description" content="@yield('pageDescription','')">
    <title>@yield('pageTitle','')</title>
    @section('headScript')
        <script src="@assets('assets/init.js')"></script>
        <link href="@assets('assets/mui/css/mui.css')" rel="stylesheet"/>
        <link href="@assets('assets/admin_mobile/css/style.css')" rel="stylesheet"/>
    @show
    @section('headAppend')@show
</head>
<body>
@section('body')
@show
@section('bodyScript')
    <script src="@assets('assets/admin_mobile/js/basic.js')"></script>
@show
</body>
</html>