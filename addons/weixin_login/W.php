<?php




define('APP_DEBUG',false);
// 定义应用目录
define('APP_PATH',__DIR__ . '/App/');
define('SITE_PATH',__DIR__);
define('MODOULE_PATH','../addons/'.pathinfo(__DIR__,PATHINFO_BASENAME ).'/');
// 引入入口文件
require  SITE_PATH . '/Core/Start.php';

