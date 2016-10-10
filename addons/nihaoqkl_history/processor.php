<?php
/**
 * 历史消息模块处理程序
 *
 * @author qklin
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Nihaoqkl_historyModuleProcessor extends WeModuleProcessor {

	public function respond() {
		$content = $this->message['content'];

		if($content == '历史消息') {
			return $this->respText('http://'.$_SERVER['HTTP_HOST'].'/app'.ltrim(murl('entry',array('do'=>'index','m'=>'nihaoqkl_history'),false),'.'));
		}
	}
}