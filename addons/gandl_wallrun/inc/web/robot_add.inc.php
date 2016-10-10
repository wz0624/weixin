<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;


if('add'==$_GPC['submit']) {

	$openid = 'GANDL_WALLRUN_ROBOT_'.TIMESTAMP; // 生成30位唯一外部ID
	$salt   = random(8); // 生成加密盐

	$robot = array(
		'uniacid' => $_W['uniacid'],
		'email' => md5($openid).'@gandl_wallrun.com',
		'password' => md5($openid . $salt . $_W['config']['setting']['authkey']),
		'salt' => $salt,
		'groupid' => 0,
		'createtime' => TIMESTAMP,
		'nickname' => $_GPC['nickname'],
		'avatar' => $_GPC['avatar'],
		'gender' => $_GPC['gender']
	);

	pdo_insert('mc_members', $robot);
	$robot['uid'] = pdo_insertid();
	if(empty($robot['uid'])){
		returnError('机器人账户创建失败');
	}
	
	pdo_insert('gandl_wallrun_robot', array(
		'uniacid' => $robot['uniacid'],
		'uid' => $robot['uid']
	));
	$id = pdo_insertid();
	if(empty($id)){
		returnError('机器人创建失败');
	}


	returnSuccess('机器人创建成功',$this->createWebUrl('robot_list'));

}else{
	

	load()->func('tpl');
	include $this->template('web/robot_add');
}



?>