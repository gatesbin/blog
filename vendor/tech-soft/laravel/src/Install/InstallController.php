<?php

namespace TechSoft\Laravel\Install;

use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use TechSoft\Laravel\Admin\Util\AdminUtil;


class InstallController extends Controller
{
    public function lock()
    {
        file_put_contents(storage_path('install.lock'), 'lock');
        return Response::send(0, 'install lock ok ^_^');
    }

    public function ping()
    {
        return 'ok';
    }

    public function execute()
    {
        if (file_exists(storage_path('install.lock'))) {
            echo "删除 install.lock 文件再安装 T_T";
            return;
        }

        $username = Input::get("username");
        $password = Input::get("password");
        $installDemo = Input::get('installDemo');
        if (empty($username)) {
            echo "管理用户名为空";
            return;
        }
        if (empty($password)) {
            echo "管理用户密码为空";
            return;
        }

        $exitCode = Artisan::call("migrate");
        if (0 != $exitCode) {
            echo "安装错误 exitCode($exitCode)";
            return;
        }

        $adminUserCount = ModelUtil::count('admin_user');
        if ($adminUserCount === 0) {
            AdminUtil::add($username, $password);
                        if ($installDemo && file_exists('./data_demo/data.php')) {
                $data = include('./data_demo/data.php');
                if (!empty($data['inserts'])) {
                    foreach ($data['inserts'] as $table => $records) {
                        DB::table($table)->insert($records);
                    }
                }
                if (!empty($data['updates'])) {
                    foreach ($data['updates'] as $record) {
                        DB::table($record['table'])->where($record['where'])->update($record['update']);
                    }
                }
            }
        }

        echo 'ok';
    }
}
