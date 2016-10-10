<?php
/**
 */
defined('IN_IA') or exit('Access Denied');

class Yike_red_packetModuleReceiver extends WeModuleReceiver {
	public function receive() {
		$type = $this->message['type'];
		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看易客文档来编写你的代码
		return $this->respText('chenggong');
	}
}
