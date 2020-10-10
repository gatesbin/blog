<?php

Route::match(['get', 'post'], 'login', '\TechSoft\Laravel\Admin\Controller\LoginController@index');
Route::match(['get', 'post'], 'login/captcha', '\TechSoft\Laravel\Admin\Controller\LoginController@captcha');
Route::match(['get', 'post'], 'logout', '\TechSoft\Laravel\Admin\Controller\LoginController@logout');

Route::match(['get', 'post'], 'sso/client', '\TechSoft\Laravel\Admin\Controller\LoginController@ssoClient');
Route::match(['get', 'post'], 'sso/server', '\TechSoft\Laravel\Admin\Controller\LoginController@ssoServer');
Route::match(['get', 'post'], 'sso/server_success', '\TechSoft\Laravel\Admin\Controller\LoginController@ssoServerSuccess');
Route::match(['get', 'post'], 'sso/server_logout', '\TechSoft\Laravel\Admin\Controller\LoginController@ssoServerLogout');

Route::match(['get', 'post'], 'system/change_pwd', '\TechSoft\Laravel\Admin\Controller\SystemController@changePwd');
Route::match(['get', 'post'], 'system/clear_cache', '\TechSoft\Laravel\Admin\Controller\SystemController@clearCache');

Route::match(['get', 'post'], 'system/user/role/list', '\TechSoft\Laravel\Admin\Controller\SystemController@userRoleList');
Route::match(['get', 'post'], 'system/user/role/edit/{id?}', '\TechSoft\Laravel\Admin\Controller\SystemController@userRoleEdit');
Route::match(['get', 'post'], 'system/user/role/delete/{id}', '\TechSoft\Laravel\Admin\Controller\SystemController@userRoleDelete');

Route::match(['get', 'post'], 'system/user/list', '\TechSoft\Laravel\Admin\Controller\SystemController@userList');
Route::match(['get', 'post'], 'system/user/edit/{id?}', '\TechSoft\Laravel\Admin\Controller\SystemController@userEdit');
Route::match(['get', 'post'], 'system/user/delete/{id}', '\TechSoft\Laravel\Admin\Controller\SystemController@userDelete');

Route::match(['get', 'post'], 'system/log/list', '\TechSoft\Laravel\Admin\Controller\SystemController@logList');
Route::match(['get', 'post'], 'system/log/delete/{id}', '\TechSoft\Laravel\Admin\Controller\SystemController@logDelete');

Route::match(['get', 'post'], 'system/data/select_dialog/{category}', '\TechSoft\Laravel\Admin\Controller\DataController@selectDialog');
Route::match(['get', 'post'], 'system/data/temp_data_upload/{category}', '\TechSoft\Laravel\Admin\Controller\DataController@tempDataUpload');
Route::match(['get', 'post'], 'system/data/ueditor', '\TechSoft\Laravel\Admin\Controller\DataController@ueditorHandle');
