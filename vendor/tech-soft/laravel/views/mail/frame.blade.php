<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <title>[{{\TechSoft\Laravel\Config\ConfigUtil::get('siteName')}}] @yield('pageTitle')</title>
    <style type="text/css">
        *{padding:0;margin:0;font-family:"Segoe UI","Lucida Grande",Helvetica,Arial,"Microsoft YaHei",FreeSans,Arimo,"Droid Sans","wenquanyi micro hei","Hiragino Sans GB","Hiragino Sans GB W3",sans-serif;color:#666;box-sizing:border-box;}
        body{font-size:13px;background:#F8F8F8;margin:20px 0;}
        #wrap{margin:0 auto;max-width:800px;padding:10px;}
        #head,#content,#foot,#signature{background:#FFF;}
        #head{background:#3f3f3f;height:50px;padding:5px 10px;}
        #head .logo{line-height:40px;color:#FFF;font-size:20px;text-decoration:none;}
        #content{padding:30px 10px;}
        #content p{line-height:2em;}
        #signature{padding:10px;color:#999;}
        #foot{text-align:center;line-height:50px;border-top:1px solid #EEE;color:#999;}
    </style>
</head>
<body>
<div id="wrap">
    <div id="head">
        <a class="logo" href="http://{{\TechSoft\Laravel\Config\ConfigUtil::get('siteDomain')}}" target="_blank">
            {{\TechSoft\Laravel\Config\ConfigUtil::get('siteName')}}
        </a>
    </div>
    @section('body')
        <div id="content">
            @section('bodyContent')
            @show
        </div>
        <div id="signature">
            此邮件为系统邮件，请勿回复。
        </div>
        <div id="foot">
            {{\TechSoft\Laravel\Config\ConfigUtil::get('siteName')}} &copy; {{\TechSoft\Laravel\Config\ConfigUtil::get('siteDomain')}} 版权所有
        </div>
    @show
</div>
</body>
</html>
