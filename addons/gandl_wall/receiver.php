<?php
/**
 * 红包墙模块推送事件处理
 *
 * @author gl5512968
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Gandl_wallModuleReceiver extends WeModuleReceiver {

	public function receive() {
		global $_W;

		//$type = $this->message['type'];

		//if('event'==$type){
			$event = $this->message['event'];
			if('subscribe'==$event){
				load()->model('mc');
				$this->_fan = mc_fansinfo($this->message['from'], $_W['acid'], $_W['uniacid']);
				// 当前用户关注公众号时(首次关注)，将该公众号下所有红包墙中该用户的冷却时间清零
				if(!empty($this->_fan)  && $this->_fan['uid']>0){
					pdo_update('gandl_wall_user', array('rob_next_time'=>0), array('user_id' =>$this->_fan['uid'], 'uniacid' => $_W['uniacid'], 'followed'=>0));
					pdo_update('gandl_wall_user', array('followed'=>1,'follow'=>1), array('user_id' =>$this->_fan['uid'], 'uniacid' => $_W['uniacid']));
				}
			}else if('unsubscribe'==$event){
				load()->model('mc');
				$this->_fan = mc_fansinfo($this->message['from'], $_W['acid'], $_W['uniacid']);
				// 
				if(!empty($this->_fan)  && $this->_fan['uid']>0){
					pdo_update('gandl_wall_user', array('follow'=>0), array('user_id' =>$this->_fan['uid'], 'uniacid' => $_W['uniacid']));
				}
			}
		//}

	}

}