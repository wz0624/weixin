<?php
/**
 * 公众号授权系统模块微站定义
 *
 * @author Jialin
 * @url http://www.012wz.com/thread-13093-1-1.html
 * @qq 77035993
 */
defined('IN_IA') or exit('Access Denied');


class Weixin_loginModuleSite extends WeModuleSite {


	public function __call($name, $arguments)
	{
		require 'W.php';
		W::Start($name,$arguments);
	}
}