<?php
defined('IN_IA') or exit('Access Denied');
$teacher_info=$this->teacher_mobile_qx();
$uid=$teacher_info['fanid'];
$t_id=$teacher_info['teacher_id'];
if($this->module['config']['line_type'][$_SESSION['school_id']]){
	$line_type_cfg=explode("||", $this->module['config']['line_type'][$_SESSION['school_id']]);
	foreach ($line_type_cfg as $key => $value) {
		if($value){
			$line_type[]=$value;
		}
	}
}
$alist=$this->getTeacherClass($t_id);
$list=$alist['list'];

$cid=$_GPC['cid'];
if($cid){
	$class=pdo_fetch("select * from {$table_pe}lianhu_class where class_id=:cid",array(':cid'=>$_GPC['cid']));
	if(!$class){
		message('没有找到此班级',$this->createMobileUrl('Tea_line'),'error');
	}
}
if($ac=='new'){
	if($_GPC['submit']){
		$in['class_id']=$_GPC['cid'];
		$in['line_title']=$_GPC['line_title'];
		$in['line_content']=$_GPC['line_content'];
		$in['line_type']=$_GPC['line_type'];
		$in['addtime']=TIMESTAMP;
		$in['teacher_id']=$t_id;
		$in['teacher_intro']=$t_name."添加";
		$in['uniacid']=$_W['uniacid'];
		$in['school_id']=$_SESSION['school_id'];		
		pdo_insert('lianhu_line',$in);
		message('添加成功',$this->createMobileUrl('Tea_line',array('ac'=>'old','cid'=>$_GPC['cid'])),'success');
	}
}
if($ac=='edit'){
	$lid=$_GPC['lid'];
	if($_GPC['submit']){
		$result=pdo_fetch("select * from {$table_pe}lianhu_line where line_id=:lid",array(':lid'=>$lid));
		$in['line_title']=$_GPC['line_title'];
		$in['line_content']=$_GPC['line_content'];
		$in['line_type']=$_GPC['line_type'];
		$in['teacher_id']=$t_id;
		$in['status']=$_GPC['status'];
		$in['teacher_intro']=$t_name."编辑";
		pdo_update('lianhu_line',$in,array('line_id'=>$lid));
		message('编辑成功',$this->createMobileUrl('Tea_line',array('ac'=>'old','cid'=>$result['class_id'])),'success');				
	}
	$result=pdo_fetch("select * from {$table_pe}lianhu_line where line_id=:lid",array(':lid'=>$lid));
}
if($ac=='old'){
	$pagesize=20;
	$page=$_GPC['page']?$_GPC['page']:1;
	$start=($page-1)*$pagesize;
	$total=pdo_fetchcolumn("select count(*) num from  {$table_pe}lianhu_line where class_id=:cid",array(':cid'=>$cid));
	$list=pdo_fetchall("select line.*,class.class_name,tea.teacher_realname from {$table_pe}lianhu_line line left join {$table_pe}lianhu_class class on class.class_id=line.class_id
	 left join {$table_pe}lianhu_teacher tea on line.teacher_id=tea.teacher_id where line.class_id=:cid order by addtime desc limit {$start},{$pagesize}",array(':cid'=>$cid));
	$pager = pagination($total, $page+1, $pagesize);
}