<?php
global $_W,$_GPC;

if(!pdo_tableexists('meepo_bbs_member')){
	$sql = "CREATE TABLE ".tablename('meepo_bbs_member')." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL,
  `uniacid` int(11) unsigned NOT NULL,
  `status` tinyint(2) unsigned NOT NULL,
  `groupid` int(11) unsigned NOT NULL,
  `time` int(11) DEFAULT NULL,
  `openid` varchar(64) DEFAULT NULL,
  `online` tinyint(2) DEFAULT '0',
  `nickname` varchar(32) DEFAULT '',
  `avatar` varchar(320) DEFAULT NULL,
  `gender` tinyint(2) DEFAULT '0',
  `city` varchar(32) DEFAULT '',
  `provice` varchar(32) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8";
	pdo_query($sql);
}

load()->model('mc');
$uid = mc_openid2uid($_W['openid']);
$user = mc_fetch($uid,array('nickname','avatar','realname','mobile','gender','residecity','resideprovince'));

if(empty($user['nickname'])){
	$user = mc_oauth_userinfo();
}

$sql = "SELECT * FROM ".tablename('meepo_bbs_member')." WHERE uniacid = :uniacid AND openid = :openid";
$params = array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']);
$member = pdo_fetch($sql,$params);

if(empty($member)){
	$data = array();
	$data['uniacid'] = $_W['uniacid'];
	$data['openid'] = $_W['openid'];
	$data['nickname'] = $user['nickname'];
	$data['avatar'] = tomedia($user['avatar']);
	$data['time'] = time();
	$data['gender'] = $user['gender'];
	$data['city'] = $user['residecity'];
	$data['provice'] = $user['resideprovince'];
	$data['status'] = $_W['fans']['follow'];
	$data['uid'] = $uid;
	pdo_insert('meepo_bbs_member',$data);
}else{
	$data = array();
	$data['nickname'] = $user['nickname'];
	$data['avatar'] = tomedia($user['avatar']);
	$data['time'] = time();
	$data['status'] = $_W['fans']['follow'];
	$data['uid'] = $uid;
	pdo_update('meepo_bbs_member',$data,array('id'=>$member['id']));
}

$sql = "SELECT * FROM ".tablename('meepo_bbs_member')." WHERE uniacid = :uniacid AND openid = :openid";
$params = array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']);
$member = pdo_fetch($sql,$params);

$user = array_merge($user,$member);