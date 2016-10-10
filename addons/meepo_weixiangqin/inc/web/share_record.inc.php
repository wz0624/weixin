<?php
global $_GPC, $_W;
$weid = $_W['uniacid'];
checklogin();
if (checksubmit('delete') && !empty($_GPC['select'])) {
foreach($_GPC['select'] as $row){
	$the_record2 = pdo_fetch("SELECT * FROM ".tablename('hongniangsharelogs')." WHERE weid=:weid AND id=:id",array(':weid'=>$weid,':id'=>intval($row)));
	pdo_delete('hongniangsharelogs',array('weid'=>$weid,'id'=>intval($row)));
	
}
message('删除成功！', $this->createWebUrl('share_record'),'success');
}
load()->func('tpl');
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if($op=='display'){
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
$condition = '';
if (!empty($_GPC['keyword'])) {
	$condition .= " AND a.nickname LIKE '%{$_GPC['keyword']}%'";
}	
$sql = "select * from ".tablename('hongniangsharelogs')."  where weid=:weid ORDER BY sharetime DESC"
. " LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
$list = pdo_fetchall($sql,array(':weid'=>$weid));
if(!empty($list)){
	foreach($list as $row){
		$row['from_user2'] = pdo_fetch("SELECT * FROM ".tablename('hnfans')." WHERE from_user=:from_user",array(':from_user'=>$row['openid']));
		$lists[] = $row;
	}
}
$total = pdo_fetchcolumn("select count(*)  from ".tablename('hongniangsharelogs')." where weid=:weid ORDER BY sharetime DESC",array(':weid'=>$weid));
$pager = pagination($total, $pindex, $psize);
}elseif($op=='del'){
	$id = intval($_GPC['id']);
	$the_record = pdo_fetch("SELECT * FROM ".tablename('hongniangsharelogs')." WHERE weid=:weid AND id=:id",array(':weid'=>$weid,':id'=>$id));
	if(!empty($the_record)){
		pdo_delete('hongniangsharelogs',array('weid'=>$weid,'id'=>$id));
		message('删除成功',$this->createWebUrl('share_record'));
	}else{
		message('错误、此项不存在或是已经被删除！',$this->createWebUrl('share_record'),'error');
	}
	
}
include $this->template('share_record');