<?php
/**
 * 拼车一族模块定义
 *
 * @author Yoby
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Yoby_carModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		
		if(checksubmit()) {
			$cfg = array(
				'guanzhu' =>$_GPC['guanzhu'],
			
			);
			if($this->saveSettings($cfg)) {
				message('保存成功', 'refresh');
			}
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}

}