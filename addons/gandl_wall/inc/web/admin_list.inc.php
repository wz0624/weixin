<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;

$wall_id=$_GPC['wall_id'];



$params = array(':uniacid' => $_W['uniacid'],':wall_id'=>$wall_id);

$wall = pdo_fetch("select * from " . tablename('gandl_wall') . " where uniacid=:uniacid  and id=:wall_id ", $params);

$list = pdo_fetchall("select * from " . tablename('gandl_wall_user') . " where uniacid=:uniacid  and wall_id=:wall_id and admin>0 ", $params);


load()->model('mc');

for($i=0;$i<count($list);$i++){
	$list[$i]['_user']= mc_fetch($list[$i]['user_id'], array('nickname', 'avatar'));
}

// 生成管理员地址(5分钟内有效)
$admin_url = $_W['siteroot'] . 'app/' . substr($this->createMobileUrl('user', array('pid' => pencode($wall_id),'cmd'=>'admin','exp'=>pencode(time()+300))),2);

include $this->template('web/admin_list');
?>