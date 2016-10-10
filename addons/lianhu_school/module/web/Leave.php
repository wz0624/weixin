<?php 
defined('IN_IA') or exit('Access Denied');
#班主任权限
$admin=$this->teacher_qx('no');
$class_list=$this->teacher_main();
$grades=$this->grade_class();
$school_uniacid=" and ".$this->where_uniacid_school;
$ac=$_GPC['ac']?$_GPC['ac']:'list';
$grade_list=pdo_fetchall("select * from {$table_pe}lianhu_grade where 1=1 {$school_uniacid}");
if($admin!='teacher')
   $class_list=pdo_fetchall("select * from {$table_pe}lianhu_class where status=1 {$school_uniacid} ");
#列表
#组合class_id
foreach ($class_list as $key => $value) {
    $class_ids[$key]=$value['class_id'];
}
$class_id_str=implode(",",$class_ids);
if($ac=='list'){
    $where =" 1=:a ";
    $params[':a']=1;    
    if($_GPC['class_id']){
			$where .=" and class_id=:cid";
            $params[':cid']=$_GPC['class_id'];
	}
	if($_GPC['student_name']){
        $student_ids=pdo_fetchall("select student_id from {$table_pe}lianhu_student  where student_name=:student_name and  class_id in ({$class_id_str}) " ,
                                   array(':student_name'=>$_GPC['student_name']));		 
        if($student_ids){
            foreach ($student_ids as $key => $value) {
                $student_id_arr[$key]=$value['student_id'];
            }              
            $student_id_str=implode(",",$student_id_arr);
            $where .=" and student_id in ({$student_id_st}) ";
        }
   }
   if($_GPC['status']){
			$where .="and leave_status=:leave_status";
            $params[':leave_status']=$_GPC['leave_status'];
  }    
  $total  =pdo_fetchcolumn("select count(*) from {$table_pe}lianhu_leave where {$where} ",$params);
  $list   =pdo_fetchall("select * from {$table_pe}lianhu_leave where {$where} order by add_time desc {$sql_limit}",$params);
}
if($ac=='edit'){
    $where[":id"]=$_GPC['id'];
    $result=pdo_fetch("select * from {$table_pe}lianhu_leave where leave_id=:id ",$where);
    if($_GPC['teacher_text']){
       $up['teacher_text'] =$_GPC['teacher_text'];
       $up['leave_status'] =2;
       pdo_update('lianhu_leave',$up,array('leave_id'=>$_GPC['id']));
       message("处理成功",$this->createWebUrl('leave'),'success');
    }
}
