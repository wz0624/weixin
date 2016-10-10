<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;

if('save'==$_GPC['submit']) {

	$id = intval($_GPC['id']);
	if(empty($id)) {
		returnError('请选择要编辑的数据');
	}
	$robot = pdo_fetch("select * from " . tablename('gandl_wallrun_robot') . " where id=:id ", array(':id' => $id));
	if(empty($robot)) {
		returnError('机器人不存在');
	}

	$robotUp = array(
		'nickname' => $_GPC['nickname'],
		'avatar' => $_GPC['avatar'],
		'gender' => $_GPC['gender']
	);

	pdo_update('mc_members', $robotUp, array('uid' => $robot['uid']));


	returnSuccess('机器人修改成功',$this->createWebUrl('robot_list'));

}else{

	$id = intval($_GPC['id']);
	if(empty($id)) {
		returnError('抱歉，传递的参数错误！');
	}

	$robot = pdo_fetch("select * from " . tablename('gandl_wallrun_robot') . " where id=:id ", array(':id' => $id));
	if(empty($robot)) {
		returnError('机器人不存在');
	}
	
	$robot=pdo_fetch("select * from " . tablename('mc_members') . " where uid=:uid ", array(':uid' => $robot['uid']));
	if(empty($robot)) {
		returnError('机器人账户不存在');
	}
	

	load()->func('tpl');
	include $this->template('web/robot_edit');

}



?>