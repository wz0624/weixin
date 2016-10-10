<?php
/**
 * 模块处理程序
 *
 * @author 微赞科技
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Wdl_proxyModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微赞文档来编写你的代码
		
		$api = 'http://www.weiduola.cn/api/ipproxy.php?mod=get';
		$data = file_get_contents($api);
		$data = json_decode($data);
		$reply = '推荐的服务器是：';
		foreach ($data as $item) {
		    $reply .= $item .',';
		}
	    return $this->respText($reply);
	}
}