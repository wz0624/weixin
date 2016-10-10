<?php

/**

 * 科目一模拟考试模块订阅器

 *

 * @author 穿越的一只小猪

 * @url http://bbs.wdlcms.com/

 */

defined('IN_IA') or exit('Access Denied');



class Jiakao_systemModuleReceiver extends WeModuleReceiver {

	public function receive() {

		$type = $this->message['type'];

		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看微赞文档来编写你的代码

	}

}