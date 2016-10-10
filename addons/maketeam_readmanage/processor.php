<?php
defined('IN_IA') or exit('Access Denied');

class Maketeam_readmanageModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
		$rid = $this->rule;
		checkauth();
		$resdata = pdo_fetch("SELECT * FROM ".tablename('maketeam_readmanage')." WHERE `rid` IN ($rid)  ORDER BY RAND() LIMIT 1");
		if($resdata['respond_type'] == '1'){//文字回复类型
			$sql = "SELECT * FROM " . tablename('basic_reply') . " WHERE `rid` IN ({$this->rule})  ORDER BY RAND() LIMIT 1";
			$reply = pdo_fetch($sql);
			$reply['content'] = htmlspecialchars_decode($reply['content']);
			$reply['content'] = str_replace(array('<br>', '&nbsp;'), array("\n", ' '), $reply['content']);
			$reply['content'] = strip_tags($reply['content'], '<a>');
		}elseif($resdata['respond_type'] == '2'){//图文回复类型
			$sql = "SELECT * FROM " . tablename('news_reply') . " WHERE rid = :id ORDER BY displayorder DESC, id ASC LIMIT 8";
			$commends = pdo_fetchall($sql, array(':id'=>$rid));
			$news = array();
			foreach($commends as $c) {
				$row = array();
				$row['title'] = $c['title'];
				$row['description'] = $c['description'];
				!empty($c['thumb']) && $row['picurl'] = tomedia($c['thumb']);
				$row['url'] = empty($c['url']) ? $this->createMobileUrl('detail', array('id' => $c['id'])) : $c['url'];
				$news[] = $row;
			}
		}

		//是否符合查看内容条件
		load()->model('mc');
		$user = mc_fetch($_W['member']['uid']);
		$group = mc_groups($_W['uniacid']);
		if($resdata['read_type'] == '1'){//会员积分模式
			if($user['credit1'] < $resdata['order_count']){//1、积分不足
				return $this->respText($resdata['norule']);
			}else{
				if($resdata['respond_type'] == '1'){
					return $this->respText($reply['content']);
				}elseif($resdata['respond_type'] == '2'){
					return $this->respNews($news);
				}
			}
		}elseif($resdata['read_type'] == '2'){//会员等级模式
			//已选中的等级
			$selected_groups = explode(',',$resdata['order_level']);
			if(in_array($user['groupid'], $selected_groups)){//2、等级合适
				if($resdata['respond_type'] == '1'){
					return $this->respText($reply['content']);
				}elseif($resdata['respond_type'] == '2'){
					return $this->respNews($news);
				}
			}else{
				return $this->respText($resdata['norule']);
			}
		}
	}
}