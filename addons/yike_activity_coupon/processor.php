<?php
/**
 * 易客优惠券模块处理程序
 *
 * @author stevezheng
 * @url http://www.yike1908.com/
 */
defined('IN_IA') or exit('Access Denied');

class Yike_activity_couponModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看易客文档来编写你的代码
	}
}