<?php 
defined('IN_IA') or exit('Access Denied');
$uid=$_W['member']['uid'];
if(empty($uid)){
	$uid=$this->register_member();
}
$result=$this->mobile_from_find_student();
if($op=='list'){
  if($_GPC['time_date']){
      $in['member_uid']=$uid;
      $in['student_id']=$result['student_id'];
      $in['class_id']  =$result['class_id'];
      $in['teacher_id']=$result['teacher_id'];
      $in['leave_reason']=$_GPC['leave_reason'];
      $in['time_date'] =$_GPC['time_date'];
      $in['add_time']=time();
     pdo_insert('lianhu_leave',$in);
      message("提交成功！",$this->createMobileUrl('leave',array('op'=>'get')),'success');
  }
}
if($op=='get'){
    $list=pdo_fetchall("select * from {$table_pe}lianhu_leave where student_id=:sid ",array(":sid"=>$result['student_id']));
}
        