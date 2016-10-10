<?php
	$item = pdo_fetch("select * from ".tablename('hc_ffk_fangfangkan')." where uniacid = ".$uniacid);
	if(empty($item)){
		message('游戏活动还未开启，敬请期待。。');
	}
	if(empty($item['isopen'])){
		message('本游戏活动已被关闭！');
	}
	$profile = pdo_fetch('SELECT * FROM '.tablename('hc_ffk_member')." WHERE uniacid = ".$uniacid." and openid = '".$openid."'");
	$id = $profile['id'];
	if(intval($profile['id']) && $profile['status']==0){
		include $this->template('forbidden');
		exit;
	}
	if(empty($profile)){
		$this->CheckCookie();
	} else {
		if(empty($profile['headimgurl'])){
			$this->CheckCookie();
		}
	}
	
	$starttime = strtotime(date('Y-m-d 00:00:00'));
	//$endtime = strtotime(date('Y-m-d 23:59:59'));
	if($profile['ffkupdatetime'] < $starttime){
		pdo_update('hc_ffk_member', array('ffkupdatetime'=>time(), 'ffktimes'=>$item['gametimes']), array('id'=>$id));
	}
	if($op=='gametimes'){
		if($profile['ffktimes']>0){
			pdo_update('hc_ffk_member', array('ffktimes'=>$profile['ffktimes']-1), array('id'=>$id));
			echo $profile['ffktimes'];
			exit;
		} else {
			echo 0;
			exit;
		}
	}
	if($op=='addcredit'){
		$credit = intval($_GPC['credit']);
		if($credit){
			$follow = pdo_fetch("select uid, follow from ".tablename('mc_mapping_fans')." where uniacid = ".$uniacid." and openid = '".$openid."'");
			load()->model('mc');
			mc_credit_update($follow['uid'], 'credit1', $credit, array('0'=>'', '1'=>'翻翻看'.$profile['realname'].'翻翻看积分'));
		}
	}
	
	$level = array(
		'0'=>'easy',
		'1'=>'normal',
		'2'=>'hard',
	);
	
	include $this->template('index');
?>