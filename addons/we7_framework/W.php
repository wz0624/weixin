<?php
/**
 * 微赞模块框架
 *
 * @author Jialin
 * @url http://www.012wz.com/thread-13093-1-1.html
 * @承接web网站定制化开发，微赞模块开发
 */
define('APP_DEBUG',false);
// 定义应用目录
define('APP_PATH',__DIR__ . '/App/');
define('SITE_PATH',__DIR__);
define('MODOULE_PATH','../addons/'.pathinfo(__DIR__,PATHINFO_BASENAME ).'/');
// 引入入口文件
require  SITE_PATH . '/Core/Start.php';

