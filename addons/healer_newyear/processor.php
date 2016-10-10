<?php
/**
 * H新年签模块处理程序
 *
 * @author healer
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Healer_newyearModuleProcessor extends WeModuleProcessor {
	public function respond() {
		//$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微赞文档来编写你的代码
		global $_W, $_GPC;

		$newyear = pdo_fetch("SELECT * FROM " . tablename("healer_newyear") . " WHERE rid=:rid", array(":rid" => $this->rule));

		if (!empty($newyear)) {
			//解签
			$array = explode("\r\n", trim($newyear["sortition"]));
			return $this->respText("您抽中的签：\r\n\r\n" . $array[mt_rand(0, count($array) - 1)]);
		}
	}
}