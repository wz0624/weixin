<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;

if('black'==$_GPC['submit']) {
	// 把用户列入黑名单
	$id=$_GPC['id'];
	if(empty($id)){
		returnError('请选择要操作的用户');
	}

	$why=$_GPC['why'];
	if(empty($why)){
		returnError('请填写列入黑名单的原因');
	}
	

	$ret1=pdo_query('UPDATE '.tablename('gandl_wall_user') .' SET black=1,black_why=:black_why where uniacid=:uniacid and id=:id', array(':uniacid' => $_W['uniacid'],':id' => $id,':black_why' => $why));
	if(false===$ret1){
		return $this->returnMessage('失败啦，重新试试呢~');
	}

	returnSuccess('操作成功');

}else if('unblack'==$_GPC['submit']) {
	// 把用户移出黑名单
	$id=$_GPC['id'];
	if(empty($id)){
		returnError('请选择要操作的用户');
	}

	$why=$_GPC['why'];
	if(empty($why)){
		returnError('请填写移出黑名单的原因');
	}

	$ret1=pdo_query('UPDATE '.tablename('gandl_wall_user') .' SET black=0,black_why=:black_why where uniacid=:uniacid and id=:id', array(':uniacid' => $_W['uniacid'],':id' => $id,':black_why' => $why));
	if(false===$ret1){
		return $this->returnMessage('失败啦，重新试试呢~');
	}

	returnSuccess('操作成功');

}else{

}



?>