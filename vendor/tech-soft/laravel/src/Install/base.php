<?php
define('INSTALL_BASE', realpath(__DIR__ . '/../../../../..'));
$defaultEnv = array();
if(!empty($configFiles = glob(INSTALL_BASE.'/env.*.json'))){
    foreach ($configFiles as $configFile) {
        $env = @json_decode(@file_get_contents($configFile),true);
        if(!empty($env)){
            $defaultEnv = array_merge($defaultEnv,$env);
        }
    }
}
define('INSTALL_LOCK_FILE', INSTALL_BASE . '/storage/install.lock');
define('ENV_FILE_EXAMPLE', INSTALL_BASE . '/env.example');
define('ENV_FILE', INSTALL_BASE . '/.env');
define('DEMO_DIR', INSTALL_BASE . '/public/data_demo/');
if (file_exists($licenseFile = INSTALL_BASE . '/license.txt')) {
    define('LICENSE_URL', trim(file_get_contents($licenseFile)));
}

include INSTALL_BASE . '/vendor/techonline/utils/src/FileUtil.php';
include INSTALL_BASE . '/vendor/techonline/utils/src/EnvUtil.php';

function randString($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}

function jsonErr($msg)
{
    header('Content-type: application/json');
    exit(json_encode(array(
        'code' => -1,
        'msg' => $msg
    )));
}


function post($k, $defaultValue = '')
{
    return isset($_POST[$k]) ? $_POST[$k] : $defaultValue;
}

if (!file_exists(ENV_FILE)) {
    file_put_contents(ENV_FILE, "APP_ENV=beta
APP_DEBUG=true
APP_KEY=" . randString(32));
}

if (!empty($_POST)) {

    if (file_exists(INSTALL_LOCK_FILE)) {
        jsonErr("删除install.lock文件再安装 :(");
    }

    $dbHost = post('db_host');
    $dbDatabase = post('db_database');
    $dbUsername = post('db_username');
    $dbPassword = post('db_password', '');
    $dbPrefix = post('db_prefix', '');
    $username = post('username');
    $password = post('password');
    $installDemo = (post('installDemo') ? 1 : 0);
    $installLicense = (post('installLicense') ? 1 : 0);
    if (empty($dbHost)) {
        jsonErr("数据库主机名不能为空");
    }
    if (empty($dbDatabase)) {
        jsonErr("数据库数据库不能为空");
    }
    if (empty($dbUsername)) {
        jsonErr("数据库用户不能为空");
    }
    if (empty($username)) {
        jsonErr("管理用户不能为空");
    }
    if (empty($password)) {
        jsonErr("管理用户密码不能为空");
    }
    if (defined('LICENSE_URL') && !$installLicense) {
        jsonErr("请先同意《软件安装许可协议》");
    }

        try {
        new PDO("mysql:host=$dbHost;dbname=$dbDatabase", $dbUsername, $dbPassword);
    } catch (\Exception $e) {
        jsonErr('连接数据信息 ' . $dbHost . '.' . $dbDatabase . ' 失败!');
    }

        $envContent = file_get_contents(ENV_FILE_EXAMPLE);

    $envContent = preg_replace("/DB_HOST=(.*?)\\n/", "DB_HOST=" . $dbHost . "\n", $envContent);
    $envContent = preg_replace("/DB_DATABASE=(.*?)\\n/", "DB_DATABASE=" . $dbDatabase . "\n", $envContent);
    $envContent = preg_replace("/DB_USERNAME=(.*?)\\n/", "DB_USERNAME=" . $dbUsername . "\n", $envContent);
    $envContent = preg_replace("/DB_PASSWORD=(.*?)\\n/", "DB_PASSWORD=" . $dbPassword . "\n", $envContent);
    $envContent = preg_replace("/DB_PREFIX=(.*?)\\n/", "DB_PREFIX=" . $dbPrefix . "\n", $envContent);

    $envContent = preg_replace("/APP_KEY=(.*?)\\n/", "APP_KEY=" . randString(32) . "\n", $envContent);
    file_put_contents(ENV_FILE, $envContent);

        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/install/execute?username=' . urlencode($username) . '&password=' . urlencode($password) . '&installDemo=' . $installDemo;
    $content = @file_get_contents($url);
    if ('ok' != $content) {
        if (empty($content)) {
            $content = '请求安装链接失败';
        }
        jsonErr($content);
    }

    file_put_contents(INSTALL_LOCK_FILE, 'lock');

    if (function_exists('after_install_callback')) {
        after_install_callback();
    }

    header('Content-type: application/json');
    exit(json_encode(array(
        'code' => 0,
        'msg' => '安装成功',
        'redirect' => '/admin/'
    )));
}

?>
<!doctype html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="renderer" content="webkit">
    <script src="/assets/init.js"></script>
    <script src="/assets/basic/js/basic.js"></script>
    <link rel="stylesheet" href="/assets/uikit/css/ui.css">
    <title>安装助手</title>
    <style type="text/css">
        body, html {
            min-height: 100%;
        }

        .license-content p {
            font-size: 14px;
            line-height: 1.8em;
            margin: 0;
        }
    </style>
</head>
<body style="background:#333;padding:40px 0;">

<?php
$error = 0;
function ok($msg)
{
    echo '<div class="uk-alert uk-alert-success"><span class="uk-icon-check"></span> ' . $msg . '</div>';
}

function err($msg, $solutionUrl = null)
{
    global $error;
    $error++;
    echo '<div class="uk-alert uk-alert-danger"><span class="uk-icon-times"></span> ' . $msg . ' ' . ($solutionUrl ? '<a target="_blank" href="' . $solutionUrl . '">解决办法</a>' : '') . '</div>';
}

function env_writable()
{
    $file = INSTALL_BASE . '/.env';
    if (!file_exists($file)) {
        if (false === file_put_contents($file, "")) {
            @unlink($file);
            return false;
        }
        @unlink($file);
        return true;
    }
    $content = @file_get_contents($file);
    if (false === file_put_contents($file, $content)) {
        return false;
    }
    return true;
}

function rewrite_ok()
{
    if (file_exists(INSTALL_BASE . '/storage/rewrite.skip')) {
        return true;
    }
    $url = 'http://' . $_SERVER['HTTP_HOST'] . '/install/ping';
    $content = @file_get_contents($url);
    if ('ok' == $content) {
        return true;
    }
    return false;
}

function env($key, $defaultValue = '')
{
    static $values = null;
    if (null === $values) {
        $content = file_get_contents(INSTALL_BASE . '/.env.example');
        foreach (explode("\n", $content) as $line) {
            if ($line = trim($line)) {
                list($k, $v) = explode('=', $line);
                $values[trim($k)] = trim($v);
            }
        }
    }
    return isset($values[$key]) ? $values[$key] : $defaultValue;
}

?>

<div style="width:600px;min-height:100vh;margin:0 auto;">

    <?php if (file_exists(INSTALL_BASE . '/storage/install.lock')) { ?>
        <div class="uk-alert uk-alert-danger uk-text-center">系统无需重复安装</div>
    <?php } else { ?>
        <h1 class="uk-text-center" style="color:#FFF;">
            安装助手
        </h1>
        <div class="uk-panel uk-panel-header" style="background:#FFF;">
            <h2 class="uk-panel-title" style="padding:10px;">
                环境检查
            </h2>
            <div class="uk-panel-body">
                <?php ok('系统：' . PHP_OS); ?>
                <?php version_compare(PHP_VERSION, '5.5.9', '>=') ? ok('PHP版本' . PHP_VERSION) : err('PHP版本>=5.5.9 当前为' . PHP_VERSION); ?>
                <?php ok('最大上传：' . \TechOnline\Utils\FileUtil::formatByte(\TechOnline\Utils\EnvUtil::env('uploadMaxSize'))); ?>
                <?php function_exists('openssl_open') ? ok('OpenSSL PHP 扩展') : err('缺少 OpenSSL PHP 扩展'); ?>
                <?php function_exists('exif_read_data') ? ok('Exif PHP 扩展') : err('缺少 Exif PHP 扩展'); ?>
                <?php function_exists('shell_exec') ? ok('shell_exec 函数') : err('缺少 shell_exec 函数'); ?>
                <?php function_exists('proc_open') ? ok('proc_open 函数') : err('缺少 proc_open 函数'); ?>
                <?php function_exists('putenv') ? ok('putenv 函数') : err('缺少 putenv 函数'); ?>
                <?php function_exists('proc_get_status') ? ok('proc_get_status 函数') : err('缺少 proc_get_status 函数'); ?>
                <?php function_exists('bcmul') ? ok('bcmath 扩展') : err('缺少 PHP bcmath 扩展'); ?>
                <?php class_exists('pdo') ? ok('PDO PHP 扩展') : err('缺少 PDO PHP 扩展'); ?>
                <?php (class_exists('pdo') && in_array('mysql', PDO::getAvailableDrivers())) ? ok('PDO Mysql 驱动正常') : err('缺少 PDO Mysql 驱动'); ?>
                <?php function_exists('mb_internal_encoding') ? ok('缺少 Mbstring PHP 扩展') : err('Mbstring PHP 扩展'); ?>
                <?php function_exists('token_get_all') ? ok('缺少 Tokenizer PHP 扩展') : err('Tokenizer PHP 扩展'); ?>
                <?php function_exists('finfo_file') ? ok('缺少 PHP Fileinfo 扩展') : err('PHP Fileinfo 扩展'); ?>
                <?php is_writable(INSTALL_BASE . '/storage/') ? ok('/storage/目录可写') : err('/storage/目录不可写'); ?>
                <?php is_writable(INSTALL_BASE . '/public/') ? ok('/public/目录可写') : err('/public/目录不可写'); ?>
                <?php is_writable(INSTALL_BASE . '/bootstrap/cache/') ? ok('/bootstrap/cache/目录可写') : err('/bootstrap/cache/目录不可写'); ?>
                <?php rewrite_ok() ? ok('Rewrite规则正确') : err('Rewrite规则错误'); ?>
            </div>
        </div>
        <?php if ($error > 0) { ?>
            <div class="uk-alert uk-alert-danger uk-text-center">请解决以上问题再进行安装</div>
        <?php } else if (!env_writable()) { ?>
            <div class="uk-alert uk-alert-danger uk-text-center">/.env文件不可写，请手动配置安装</div>
        <?php } else { ?>
            <form class="uk-form uk-form-stacked" method="post" action="?" data-ajax-form>
                <div class="uk-panel uk-panel-header" style="background:#FFF;">
                    <div class="uk-panel-title" style="padding:10px;">MySQL数据库信息</div>
                    <div class="uk-panel-body">
                        <div class="uk-form-row">
                            <label class="uk-form-label">主机</label>
                            <input type="text" style="width:100%;" name="db_host" value="<?php echo htmlspecialchars(empty($defaultEnv['db_host'])?'':$defaultEnv['db_host']); ?>"/>
                        </div>
                        <div class="uk-form-row">
                            <label class="uk-form-label">数据库名</label>
                            <input type="text" style="width:100%;" name="db_database" value="<?php echo htmlspecialchars(empty($defaultEnv['db_name'])?'':$defaultEnv['db_name']); ?>"/>
                        </div>
                        <div class="uk-form-row">
                            <label class="uk-form-label">用户名</label>
                            <input type="text" style="width:100%;" name="db_username" value="<?php echo htmlspecialchars(empty($defaultEnv['db_username'])?'':$defaultEnv['db_username']); ?>"/>
                        </div>
                        <div class="uk-form-row">
                            <label class="uk-form-label">密码</label>
                            <input type="text" style="width:100%;" name="db_password" value="<?php echo htmlspecialchars(empty($defaultEnv['db_password'])?'':$defaultEnv['db_password']); ?>"/>
                        </div>
                        <div class="uk-form-row">
                            <label class="uk-form-label">数据表前缀</label>
                            <input type="text" style="width:100%;" name="db_prefix" value="<?php echo htmlspecialchars(empty($defaultEnv['db_prefix'])?'':$defaultEnv['db_prefix']); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="uk-panel uk-panel-header" style="background:#FFF;">
                    <div class="uk-panel-title" style="padding:10px;">管理信息</div>
                    <div class="uk-panel-body">
                        <div class="uk-form-row">
                            <label class="uk-form-label">用户</label>
                            <input type="text" style="width:100%;" name="username" value="<?php echo htmlspecialchars(empty($defaultEnv['admin_username'])?'':$defaultEnv['admin_username']); ?>"/>
                        </div>
                        <div class="uk-form-row">
                            <label class="uk-form-label">密码</label>
                            <input type="text" style="width:100%;" name="password" placeholder="<?php echo htmlspecialchars(empty($defaultEnv['admin_password'])?'':$defaultEnv['admin_password']); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="uk-panel uk-panel-header" style="background:#FFF;">
                    <div class="uk-panel-body uk-text-left" style="width:40%;margin:0 auto;">
                        <?php if (file_exists(DEMO_DIR)) { ?>
                            <div class="uk-form-row" style="padding-left:1em;">
                                <label>
                                    <input type="checkbox" name="installDemo" value="1"/>
                                    安装初始化数据
                                </label>
                            </div>
                        <?php } ?>
                        <?php if (defined('LICENSE_URL')) { ?>
                            <div class="uk-form-row" style="padding-left:1em;">
                                <label>
                                    <input type="checkbox" checked name="installLicense" value="1"/>
                                    同意
                                </label>
                                <a href="#license" data-uk-modal>《软件安装许可协议》</a>
                                <div id="license" class="uk-modal">
                                    <div class="uk-modal-dialog">
                                        <iframe src="<?php echo LICENSE_URL; ?>" style="width:100%;height:400px;overflow:scroll;border:1px solid #EEE;" frameborder="0"></iframe>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="uk-form-row">
                            <button type="submit" style="width:100%;" class="uk-button uk-button-primary">提交</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php } ?>
    <?php } ?>
</div>
</body>
</html>