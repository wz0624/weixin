<?php
defined('IN_IA') or exit('Access Denied');
$signPackage=$this->getSignPackage();
if($this->module['config']['line_type'][$_SESSION['school_id']]){
	$line_type_cfg=explode("||", $this->module['config']['line_type'][$_SESSION['school_id']]);
	foreach ($line_type_cfg as $key => $value) {
		if($value){
			$line_type[]=$value;
		}
	}
	$_W['line_type']=$line_type;
}
$student_info=$this->mobile_from_find_student();
if($_GPC['hid']){
    $result=$this->homeWorkInfo($_GPC['hid']);
    $home_work=1;
}else{
    $class_name=$student_info['class_name'];
    $line_id=$_GPC['aid'];
    $result=pdo_fetch("select line.*,tea.teacher_realname from {$table_pe}lianhu_line line left join {$table_pe}lianhu_teacher tea on tea.teacher_id=line.teacher_id  where line.line_id=:id",array(':id'=>$line_id));
    pdo_update('lianhu_line',array('line_look'=>$result['line_look']+1),array('line_id'=>$line_id));
}




