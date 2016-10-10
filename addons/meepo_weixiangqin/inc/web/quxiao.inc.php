<?php
global $_W,$_GPC;
$tablename = 'hnfans';
$member = 'mc_mapping_fans';
$weid = $_W['uniacid'];
$op = empty($_GPC['op'])? 'list':$_GPC['op'];
$pindex = max(1, intval($_GPC['page']));
$psize = 20;
if(checksubmit('yingcang') && !empty($_GPC['select'])) {
	 foreach($_GPC['select'] as $row){
			pdo_update($tablename,array('yingcang'=>2),array('id'=>$row,'weid'=>$weid));
	 }
	message('隐藏成功！', $this->createWebUrl('quxiao', array('page' => $_GPC['page'] )),'success');
}
if(checksubmit('xianshi') && !empty($_GPC['select'])) {
	 foreach($_GPC['select'] as $row){
			pdo_update($tablename,array('yingcang'=>1),array('id'=>$row,'weid'=>$weid));
	 }
	message('显示成功！', $this->createWebUrl('quxiao', array('page' => $_GPC['page'] )),'success');
}
if (checksubmit('delete') && !empty($_GPC['select'])) {
	pdo_delete($tablename, " id  IN  ('" . implode("','", $_GPC['select']) . "')");
	message('删除成功！', $this->createWebUrl('quxiao', array('page' => $_GPC['page'] )),'success');
}
if($op=='list'){
	$paras  = array(':weid'=>$weid,':follow'=>1);
	$lists = pdo_fetchall("select o.*,a.follow from ".tablename($tablename)." o"
					." left join ".tablename($member)." a on o.from_user = a.openid where o.weid=:weid AND a.follow!=:follow   ORDER BY o.time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize,$paras);
	$total = pdo_fetchcolumn("select count(o.id) from ".tablename($tablename)." o"
					." left join ".tablename($member)." a on o.from_user = a.openid  where o.weid=:weid AND a.follow!=:follow",$paras);
  $pager = pagination($total, $pindex, $psize);
}elseif($op=='del'){
  $id = intval($_GPC['id']);
	pdo_delete($tablename,array('id'=>$id,'weid'=>$weid));
	message('删除成功',$this->createWebUrl('quxiao',array('page'=>$pindex)));
}elseif($op=='yingcang'){
	$id = intval($_GPC['id']);
	$yingcang = intval($_GPC['yingcang']);
	if($yingcang==2){
			$yingcang = 1;
	}else{
			$yingcang = 2;
	}
	pdo_update($tablename,array('yingcang'=>$yingcang),array('id'=>$id,'weid'=>$weid));
	message('操作成功',$this->createWebUrl('quxiao',array('page'=>$pindex)));
}
include $this->template('quxiao');