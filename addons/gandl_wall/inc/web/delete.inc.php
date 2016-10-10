<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;


	$id = intval($_GPC['id']);
	if(empty($id)) {
		message('抱歉，传递的参数错误！', '', 'error');
	}

	$wall = pdo_fetch("select * from " . tablename('gandl_wall') . " where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'],':id' => $id));
	if(empty($wall)) {
		message('抱歉，没有相关数据！', '', 'error');
	}


	if(false === pdo_delete('gandl_wall', array('uniacid' => $_W['uniacid'],'id' => $id),'AND')){
		message('删除失败，请重试', '', 'error');
	}

	message('删除成功！', $this->createWebUrl('list'), 'success');

?>