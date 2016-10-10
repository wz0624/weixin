<?php 
	global $_W,$_GPC;
	//$_W['openid'] = 111111;
	
	$prizeinfo = pdo_fetchall("SELECT * FROM " . tablename('zofui_jgghb_prize') . " WHERE  uniacid = '{$_W['uniacid']}'");

	//中过奖判断
	$getuserinfo = pdo_fetch("SELECT * FROM " . tablename('zofui_jgghb_log') . " WHERE  uniacid = '{$_W['uniacid']}' AND openid = '{$_W['openid']}' AND date_format(from_UNIXTIME(`time`),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d') AND money != '' ");
	if($getuserinfo){
		$hadprize = 1;
	}
	//用户参与次数
	$usertimes = pdo_fetchcolumn(" SELECT COUNT(id) FROM " . tablename('zofui_jgghb_log') . " WHERE uniacid ={$_W['uniacid']} AND openid = '{$_W['openid']}' AND date_format(from_UNIXTIME(`time`),'%Y-%m-%d') = date_format(now(),'%Y-%m-%d')");
	
	
	include $this -> template('index');