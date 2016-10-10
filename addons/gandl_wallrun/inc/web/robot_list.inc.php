<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;


$where = '';
$params = array(':uniacid' => $_W['uniacid']);

$total = pdo_fetchcolumn("select count(id) from " . tablename('gandl_wallrun_robot') . " where uniacid=:uniacid " . $where . "", $params);
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$pager = pagination($total, $pindex, $psize);
$start = ($pindex - 1) * $psize;
$limit .= " LIMIT {$start},{$psize}";
$list = pdo_fetchall("select A.id,B.* from " . tablename('gandl_wallrun_robot') . " A LEFT JOIN ".tablename('mc_members')." B ON(A.uid=B.uid)  where A.uniacid=:uniacid  " . $where . " order by A.id desc " . $limit, $params);
/**
for($i=0;$i<count($list);$i++){
	// 处理活动状态
	if($list[$i]['start_time'] <= TIMESTAMP && $list[$i]['end_time'] >= TIMESTAMP){
		$list[$i]['state']=1;
	}else if($list[$i]['start_time'] > TIMESTAMP){
		$list[$i]['state']=2;
	}else if($list[$i]['end_time'] < TIMESTAMP){
		$list[$i]['state']=3;
	}
	// 处理活动入口
	$url = $this->createMobileUrl('index', array('pid' => pencode($list[$i]['id'])));
	$list[$i]['surl'] = $url;
	$url = substr($url, 2);
	$url = $_W['siteroot'] . 'app/' . $url;
	$list[$i]['url'] = $url;
}
**/

include $this->template('web/robot_list');
?>