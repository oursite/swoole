<?php
if(!\extension_loaded('swoole')) {
    throw new \Exception("no swoole extension. get: https://github.com/matyhtf/swoole");
}
date_default_timezone_set("Asia/Shanghai");

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', __DIR__ . DS );
define('SERVICE_PATH', dirname(ROOT_PATH));
require_once ROOT_PATH . 'vendor' . DS . 'autoload.php';
//加载当前环境
require_once 'environment.php';
if (!defined('V4_ENV')) {
    exit('环境配置错误');
}
//先根据当前环境加载公共配置
V4\Core\Config::loadByEnv(V4_ENV);
$server = new \Lib\Server(V4_ENV);
