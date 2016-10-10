<?php
/**
 * 捷讯派红包模块订阅器
 *
 * @author 捷讯设计
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class J_pocketmoneyModuleReceiver extends WeModuleReceiver {
	public function receive() {
		$type = $this->message['type'];
		if($this->message['event'] == 'unsubscribe') {
			pdo_update('j_pocketmoney_fans', array(
				'subscribe' => 0,
				'subscribe_time' => 0,
			), array('from_user' => $this->message['fromusername'], 'weid' => $GLOBALS['_W']['uniacid']));
		}elseif($this->message['event'] == 'subscribe') {
			pdo_update('j_pocketmoney_fans', array(
				'subscribe' => 1,
				'subscribe_time' => time(),
			), array('from_user' => $this->message['fromusername'], 'weid' => $GLOBALS['_W']['uniacid']));
			
		}
	}
}