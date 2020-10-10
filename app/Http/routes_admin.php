<?php

Route::group(
    [
        'prefix' => env('ADMIN_PATH', '/admin/'),
        'middleware' => [
            \TechSoft\Laravel\Admin\Middleware\AdminAuthMiddleware::class,
        ]
    ], function () {

    include __DIR__ . '/../../vendor/tech-soft/laravel/src/Admin/Routes/routes.php';
    Route::match(['get', 'post'], '', '\App\Http\Controllers\Admin\IndexController@index');

    Route::match(['get', 'post'], 'config/setting', '\App\Http\Controllers\Admin\ConfigController@setting');
    Route::match(['get', 'post'], 'config/blog', '\App\Http\Controllers\Admin\ConfigController@blog');
    Route::match(['get', 'post'], 'config/visit', '\App\Http\Controllers\Admin\ConfigController@visit');
    Route::match(['get', 'post'], 'config/visit_limit', '\App\Http\Controllers\Admin\ConfigController@visitLimit');
    Route::match(['get', 'post'], 'config/visit_ips', '\App\Http\Controllers\Admin\ConfigController@visitIps');
    Route::match(['get', 'post'], 'config/contact', '\App\Http\Controllers\Admin\ConfigController@contact');

    Route::match(['get', 'post'], 'blog/list', '\App\Http\Controllers\Admin\BlogController@dataList');
    Route::match(['get', 'post'], 'blog/view', '\App\Http\Controllers\Admin\BlogController@dataView');
    Route::match(['get', 'post'], 'blog/edit', '\App\Http\Controllers\Admin\BlogController@dataEdit');
    Route::match(['get', 'post'], 'blog/add', '\App\Http\Controllers\Admin\BlogController@dataAdd');
    Route::match(['get', 'post'], 'blog/delete', '\App\Http\Controllers\Admin\BlogController@dataDelete');

    Route::match(['get', 'post'], 'message/list', '\App\Http\Controllers\Admin\MessageController@dataList');
    Route::match(['get', 'post'], 'message/view', '\App\Http\Controllers\Admin\MessageController@dataView');
    Route::match(['get', 'post'], 'message/edit', '\App\Http\Controllers\Admin\MessageController@dataEdit');
    Route::match(['get', 'post'], 'message/add', '\App\Http\Controllers\Admin\MessageController@dataAdd');
    Route::match(['get', 'post'], 'message/delete', '\App\Http\Controllers\Admin\MessageController@dataDelete');

    Route::match(['get', 'post'], 'blog_comment/list', '\App\Http\Controllers\Admin\BlogCommentController@dataList');
    Route::match(['get', 'post'], 'blog_comment/view', '\App\Http\Controllers\Admin\BlogCommentController@dataView');
    Route::match(['get', 'post'], 'blog_comment/edit', '\App\Http\Controllers\Admin\BlogCommentController@dataEdit');
    Route::match(['get', 'post'], 'blog_comment/add', '\App\Http\Controllers\Admin\BlogCommentController@dataAdd');
    Route::match(['get', 'post'], 'blog_comment/delete', '\App\Http\Controllers\Admin\BlogCommentController@dataDelete');

});
