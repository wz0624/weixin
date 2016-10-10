<?php
/**
 * 捷讯派红包模块处理程序
 *
 * @author 捷讯设计
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class J_pocketmoneyModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$reply = pdo_fetch("SELECT * FROM ".tablename('j_pocketmoney_reply')." WHERE rid = :rid", array(':rid' => $this->rule));
		if (!empty($reply)) {
			//如果会员存在，则更新subscribe，添加则放到页面中
			pdo_update('j_pocketmoney_fans',array('subscribe'=>1,'subscribe_time'=>time()),array('from_user'=>$this->message['from']));
			if($reply['gametype'])return $this->respText("只能通过摇一摇周边进入哦~");
			$rid=$this->rule;
			$response[] = array(
				'title' => $reply['title'],
				'description' => $reply['description'],
				'picurl' => $_W['attachurl'].$reply['cover'],
				'url' => $this->createMobileUrl('index',array('r_id'=>$rid,'r'=>TIMESTAMP)),
			);
			return $this->respNews($response);
			//return $this->respText($reply['title'].": \r\n <a href='".$this->createMobileUrl('redpack')."'>马上领取</a>");
		}
	}
}