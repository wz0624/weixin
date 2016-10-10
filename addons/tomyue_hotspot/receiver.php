<?php
/**
 * 	云热点模块处理程序
 *
 * @author 云热点团队
 * @url http://012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Tomyue_HotspotModuleReceiver extends WeModuleReceiver {
	public function receive() {
		$type = $this->message['type'];
		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看微赞文档来编写你的代码
	}
}