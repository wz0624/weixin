<?php
/**
 * 云备份模块处理程序
 *
 * @author PHP大巴
 * @url http://www.phpdb.net/
 */
defined('IN_IA') or exit('Access Denied');

class Pdb_cloudbakModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微赞文档来编写你的代码
	}
}