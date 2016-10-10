<?php
/**
 * 易客优惠券模块订阅器
 *
 * @author stevezheng
 * @url http://www.yike1908.com/
 */
defined('IN_IA') or exit('Access Denied');

class Yike_activity_couponModuleReceiver extends WeModuleReceiver {
	public function receive() {
		$type = $this->message['type'];
		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看易客文档来编写你的代码
	}
}