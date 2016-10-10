<?php
/**
 * 是男人就动起来!模块微站定义
 *
 * @author 老虎
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Tiger_paobunanModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微赞文档来编写你的代码
	}
}