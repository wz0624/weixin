<?php
global $_W,$_GPC;
$weid = intval($_W['uniacid']);
$table_name = 'meepo_hongnianglikes';
$user_table = 'hnfans';
if($_W['isajax']){
	$ids = $_GPC['ids'];
	$condition = '';
	if(!empty($ids)){
		$ids = trim($ids);
		$condition .= "AND id NOT IN (".$ids.")";
	}
	$record = pdo_fetch("SELECT `openid`,`toopenid`,`createtime`,`flower_num`,`id` FROM ".tablename($table_name)." WHERE weid=:weid AND createtime>=:createtime $condition",array(':weid'=>$weid,':createtime'=>(TIMESTAMP-60)));
	if(!empty($record)){
		$FROM = pdo_fetch("SELECT `avatar`,`nickname` FROM ".tablename($user_table)." WHERE from_user=:from_user AND weid=:weid",array(':from_user'=>$record['openid'],':weid'=>$weid));
		if(preg_match('/http:(.*)/',$FROM['avatar'])){
		}elseif(preg_match('/images(.*)/',$FROM['avatar'])){
						$FROM['avatar'] = $_W['attachurl'].$FROM['avatar'];
		}else{
					$FROM['avatar'] = '../addons/meepo_weixiangqin/template/mobile/tpl/static/friend/images/cdhn80.jpg';
		}
		$to_nickname = pdo_fetchcolumn("SELECT `nickname` FROM ".tablename($user_table)." WHERE from_user=:from_user AND weid=:weid",array(':from_user'=>$record['toopenid'],':weid'=>$weid));
		$second = TIMESTAMP-$record['createtime'];
		if($second < 60){
			if($second==0){
				$second=1;
			}
			$time = $second.'秒前！';
		}else{
			$time = '1分钟前！';
		}
		$data = array(
				'errno'=>0,
				'avatar'=>$FROM['avatar'],
				'word'=>$FROM['nickname'].'给'.$to_nickname.'送了'.$record['flower_num'].'朵花！',
				'time'=>$time,
				'id'=>$record['id'],
		);
		die(json_encode($data));
	}else{
		die(json_encode(error(-1,'fail')));
	}
}