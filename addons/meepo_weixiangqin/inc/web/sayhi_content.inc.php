<?php
global $_W,$_GPC;
$tablename = 'meepo_hongniangsayhi_content';
$weid = $_W['uniacid'];
$op = empty($_GPC['op'])? 'list':$_GPC['op'];
if($op=='list'){
	$lists = pdo_fetchall('SELECT * FROM '.tablename($tablename).' WHERE weid=:weid ORDER BY createtime DESC',array(':weid'=>$weid));
}elseif($op=='post'){
	$id = intval($_GPC['id']);
	$list = pdo_fetch('SELECT * FROM '.tablename($tablename).' WHERE weid=:weid AND id=:id',array(':weid'=>$weid,':id'=>$id));
	if(checksubmit('submit')){
			$data = array();
			$data['content'] = $_GPC['content'];
			if(empty($id)){
				 $data['weid'] = $weid;
				 $data['createtime'] = time();
			   pdo_insert($tablename,$data);
				 message('新增成功',$this->createWebUrl('sayhi_content'));
			}else{
				 pdo_update($tablename,$data,array('id'=>$id,'weid'=>$weid));
				 message('保存成功',$this->createWebUrl('sayhi_content'));
			}
	}
}elseif($op=='del'){
  $id = intval($_GPC['id']);
	pdo_delete($tablename,array('id'=>$id,'weid'=>$weid));
	message('删除成功',$this->createWebUrl('sayhi_content'));
}
include $this->template('sayhi_content');