<?php

Route::group(
    [
        'middleware' => [
            \TechOnline\Laravel\Middleware\BaseMiddleware::class,
        ]
    ],
    function () {

        Route::match(['get', 'post'], '', '\App\Http\Controllers\Main\IndexController@index');
        Route::match(['get', 'post'], 'blog/{id}', '\App\Http\Controllers\Main\BlogController@index')->where(['id' => '[0-9_]+']);
        Route::match(['get', 'post'], 'blog/{id}/comment', '\App\Http\Controllers\Main\BlogController@comment')->where(['id' => '[0-9_]+']);
        Route::match(['get', 'post'], 'blog/comment_captcha', '\App\Http\Controllers\Main\BlogController@commentCaptcha');
        Route::match(['get', 'post'], 'message', '\App\Http\Controllers\Main\MessageController@index')->where(['id' => '[0-9_]+']);
        Route::match(['get', 'post'], 'message/submit', '\App\Http\Controllers\Main\MessageController@submit')->where(['id' => '[0-9_]+']);
        Route::match(['get', 'post'], 'message/submit_captcha', '\App\Http\Controllers\Main\MessageController@submitCaptcha');
        Route::match(['get', 'post'], 'message/up/{id}', '\App\Http\Controllers\Main\MessageController@up')->where(['id' => '[0-9_]+']);
        Route::match(['get', 'post'], 'message/down/{id}', '\App\Http\Controllers\Main\MessageController@down')->where(['id' => '[0-9_]+']);

    }
);

Route::match(['get', 'post'], 'placeholder/{width}x{height}', '\TechSoft\Laravel\Misc\Controllers\PlaceholderController@index');
Route::match(['get', 'post'], 'data_image/{file}/limit_{width}x{height}', '\TechSoft\Laravel\Misc\Controllers\ImageController@limit')->where(['file' => 'data\\/[a-z0-9_]+\\/\\d+\\/\\d+\\/\\d+\\/[a-z0-9_\\.]+', 'width' => '[0-9]+', 'height' => '[0-9]+']);

Route::match(['get', 'post'], 'install/ping', '\TechSoft\Laravel\Install\InstallController@ping');
Route::match(['get', 'post'], 'install/execute', '\TechSoft\Laravel\Install\InstallController@execute');
Route::match(['get', 'post'], 'install/lock', '\TechSoft\Laravel\Install\InstallController@lock');

include __DIR__ . '/routes_admin.php';
