<?php
defined('IN_IA') or exit('Access Denied');

class Maketeam_readmanageModuleSite extends WeModuleSite {

	public function doMobileMobile() {
		//这个操作被定义用来呈现 功能封面
	}

	public function doMobileDetail() {
		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		$openid = $_W['fans']['from_user'];
		if (!strpos($userAgent, 'MicroMessenger')) {
			message('请使用微信浏览器打开！');
			$openid = 'opk4HsyhyQpJvVAUhA6JGhdMSImo';
		}
		$follow = pdo_fetchcolumn('select follow from '.tablename("mc_mapping_fans")." where openid='{$openid}'");
		$sql = "SELECT * FROM " . tablename('news_reply') . " WHERE `id`=:id";
		$row = pdo_fetch($sql, array(':id'=>$id));
		if (!empty($row['url'])) {
			header("Location: ".$row['url']);
		}
		$row = istripslashes($row);
		if($_W['os'] == 'android' && $_W['container'] == 'wechat' && $_W['account']['account']) {
			$subscribeurl = "weixin://profile/{$_W['account']['account']}";
		} else {
			$sql = 'SELECT `subscribeurl` FROM ' . tablename('account_wechats') . " WHERE `acid` = :acid";
			$subscribeurl = pdo_fetchcolumn($sql, array(':acid' => intval($_W['acid'])));
		}
		$rid = $row['rid'];
		$resdata = pdo_fetch("SELECT * FROM ".tablename('maketeam_readmanage')." WHERE rid = '{$rid}' ");
		load()->model('mc');
		$user = mc_fetch($_W['member']['uid']);
		$selected_groups = explode(',',$resdata['order_level']);

		//是否符合查看内容条件
		$group = mc_groups($_W['uniacid']);
		if($resdata['read_type'] == '1'){//会员积分模式
			if($user['credit1'] < $resdata['order_count'] || $follow != '1'){//1、积分不足或者未关注
				header("Location: ".$resdata['follow_url']);
			}
		}elseif($resdata['read_type'] == '2'){//会员等级模式
			//已选中的等级
			$selected_groups = explode(',',$resdata['order_level']);
			if(!(in_array($user['groupid'], $selected_groups)) || $follow != '1'){//2、等级不合适或者未关注
				header("Location: ".$resdata['follow_url']);
			}
		}
		include $this->template('detail');
	}

}