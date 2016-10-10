<?php
/**
 * 简单预约模块处理程序
 *
 * @author junsion
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class boyhood_bookModuleProcessor extends WeModuleProcessor {
	public function respond() {
	$content = $this->message['content'];
		$rid = $this->rule;
		$rule = pdo_fetch('select * from '.tablename($this->modulename.'_rule')." where rid='{$rid}'");
		if (!empty($rule)){
			return $this->respNews(array(array('title'=>$rule['title'],'description'=>$rule['description'],'picurl'=>toimage($rule['thumb']),'url'=>$this->createMobileUrl('index',array('rid'=>$rid)))));
		}
	}
}