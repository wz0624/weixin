<?php
global $_W,$_GCP;
$weid = $_W['uniacid'];
$openid = $_W['openid'];
$flower_table = 'meepo_hongnianglikes';
$sayhi_table = 'meepo_hongniangsayhi';
$chat_table = 'hnmessage';
if(empty($openid)){
   message('请从微信重新进入');
}
$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){
	 $url=$this->createMobileUrl('Errorjoin');			
		header("location:$url");
		exit;
}
if(strpos($useragent, 'WindowsWechat')){
		$url=$this->createMobileUrl('Errorjoin');			
		header("location:$url");
		exit;
}
$cfg = $this->module['config'];	
$res = $this->getusers($weid,$openid);
$all_num = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('hnfans')." WHERE weid = :weid",array(':weid'=>$_W['uniacid']));
//$adv_data = array();
$flower = pdo_fetchall("SELECT * FROM ".tablename($flower_table)." WHERE weid = :weid ORDER BY RAND() LIMIT 3",array(':weid'=>$_W['uniacid']));
$flowers = array();
if(!empty($flower) && COUNT($flower)==3){
		foreach($flower as $row){
			$row['from_user'] = pdo_fetch("SELECT `avatar`,`nickname` FROM ".tablename('hnfans')." WHERE from_user=:from_user AND weid=:weid",array(':weid'=>$_W['uniacid'],':from_user'=>$row['openid']));
			$row['to_user'] = pdo_fetchcolumn("SELECT `nickname` FROM ".tablename('hnfans')." WHERE from_user=:from_user AND weid=:weid",array(':weid'=>$_W['uniacid'],':from_user'=>$row['toopenid']));
			$flowers[] = $row;
		}
}
$sayhi = pdo_fetchall("SELECT * FROM ".tablename($sayhi_table)." WHERE weid = :weid ORDER BY RAND() LIMIT 3",array(':weid'=>$_W['uniacid']));
$sayhis = array();
if(!empty($sayhi) && COUNT($sayhi)==3){
		foreach($sayhi as $row){
			$row['from_user'] = pdo_fetch("SELECT `avatar`,`nickname` FROM ".tablename('hnfans')." WHERE from_user=:from_user AND weid=:weid",array(':weid'=>$_W['uniacid'],':from_user'=>$row['openid']));
			$row['to_user'] = pdo_fetchcolumn("SELECT `nickname` FROM ".tablename('hnfans')." WHERE from_user=:from_user AND weid=:weid",array(':weid'=>$_W['uniacid'],':from_user'=>$row['toopenid']));
			$sayhis[] = $row;
		}
}
$message = pdo_fetchall("SELECT *  FROM ".tablename($chat_table)." WHERE weid = :weid GROUP BY geter ORDER BY RAND() LIMIT 3",array(':weid'=>$_W['uniacid']));
$messages = array();
if(!empty($message) && COUNT($message)==3){
		foreach($message as $row){
			$row['to_user'] = pdo_fetchcolumn("SELECT `nickname` FROM ".tablename('hnfans')." WHERE from_user=:from_user AND weid=:weid",array(':weid'=>$_W['uniacid'],':from_user'=>$row['geter']));
			$messages[] = $row;
		}
}
$user = pdo_fetchall("SELECT * FROM ".tablename('hnfans')." WHERE weid = :weid ORDER BY RAND() LIMIT 3",array(':weid'=>$_W['uniacid']));
include $this->template('sms');
	