<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;

	$wall_id = intval($_GPC['wall_id']);
	if(empty($wall_id)) {
		returnError('抱歉，传递的参数错误！');
	}

	$id = intval($_GPC['id']);
	if(empty($id)) {
		returnError('抱歉，传递的参数错误！');
	}
	
	// 取消该用户的管理员
	pdo_query('UPDATE '.tablename('gandl_wall_user') .' SET admin=0 where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'],':wall_id' =>$wall_id,':id'=>$id));



	returnSuccess('删除成功！', $this->createWebUrl('admin_list', array('wall_id' => $_GPC['wall_id'])), 'success');

?>