<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;


	$id = intval($_GPC['id']);
	if(empty($id)) {
		returnError('抱歉，传递的参数错误！');
	}

	if(false === pdo_delete('my0511_yao_award', array('uniacid' => $_W['uniacid'],'id' => $id),'AND')){
		returnError('删除失败，请重试');
	}

	returnSuccess('删除成功！', $this->createWebUrl('award_list', array('yao_id' => $_GPC['yao_id'])), 'success');

?>