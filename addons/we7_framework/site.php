<?php
/**
 * 微赞模块框架
 *
 * @author Jialin
 * @url http://www.012wz.com/thread-13093-1-1.html
 * @承接web网站定制化开发，微赞模块开发
 */
defined('IN_IA') or exit('Access Denied');

class We7_frameworkModuleSite extends WeModuleSite {


	public function __call($name, $arguments)
	{
		require 'W.php';
		W::Start($name,$arguments);
	}
}