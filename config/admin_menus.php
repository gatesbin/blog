<?php
return [

    'list:博客管理' => [
        '博客管理' => '\App\Http\Controllers\Admin\BlogController@dataList',
        '博客评论' => '\App\Http\Controllers\Admin\BlogCommentController@dataList',
    ],

    'comment:留言管理' => [
        '留言管理' => '\App\Http\Controllers\Admin\MessageController@dataList',
    ],

    'cog:基础设置' => [
        '基本设置' => '\App\Http\Controllers\Admin\ConfigController@setting',
        '访问设置' => '\App\Http\Controllers\Admin\ConfigController@visit',
        '博客设置' => '\App\Http\Controllers\Admin\ConfigController@blog',
        '联系方式' => '\App\Http\Controllers\Admin\ConfigController@contact',
    ],

    'cogs:系统管理' => [
        'HIDE:修改密码' => '\TechSoft\Laravel\Admin\Controller\SystemController@changePwd',

        '管理员角色' => '\TechSoft\Laravel\Admin\Controller\SystemController@userRoleList',
        'HIDE:角色修改' => '\TechSoft\Laravel\Admin\Controller\SystemController@userRoleEdit',
        'HIDE:角色删除' => '\TechSoft\Laravel\Admin\Controller\SystemController@userRoleDelete',

        '管理员' => '\TechSoft\Laravel\Admin\Controller\SystemController@userList',
        'HIDE:管理员修改' => '\TechSoft\Laravel\Admin\Controller\SystemController@userEdit',
        'HIDE:管理员删除' => '\TechSoft\Laravel\Admin\Controller\SystemController@userDelete',
    ],

];
