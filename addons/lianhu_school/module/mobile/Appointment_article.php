<?php
defined('IN_IA') or exit('Access Denied');
$student_info=$this->mobile_from_find_student();
$class_name=$student_info['class_name'];
$line_id=intval($op);
$where=" appointment_type_limit=0 || (appointment_type_limit=1 && appointment_grade_class like '%{$student_info['grade_id']}%' ) || (appointment_type_limit=2 && appointment_grade_class like '%{$student_info['class_id']}%' ) ";
$result=pdo_fetch("select * from {$table_pe}lianhu_appointment where ({$where} ) and appointment_id=:id ",array(':id'=>$op));
if(empty($result)){
	message('没有找到此预约','','error');
}
$where_list=" student_id={$student_info['student_id']} and appointment_id={$result['appointment_id']} ";
$app_course_list=unserialize($result['appointment_mutex']);
$course_id_str=implode(',',$app_course_list);
$course_list=pdo_fetchall("select * from {$table_pe}lianhu_appointment_course where course_id in({$course_id_str}) and status=1");

foreach ($course_list as $key => $value) {
        $course_list[$key]['time']=unserialize($value['course_time']);
        $course_list[$key]['acount']=pdo_fetchcolumn("select count(*) num from {$table_pe}lianhu_applist where status!=2 and 
        appointment_id=:appointment_id and content=:content  ",array(':appointment_id'=>$result['appointment_id'],':content'=>$value['course_id'].'a') );
        $course_list[$key]['bcount']=pdo_fetchcolumn("select count(*) num from {$table_pe}lianhu_applist where status!=2 and 
        appointment_id=:appointment_id and content=:content  ",array(':appointment_id'=>$result['appointment_id'],':content'=>$value['course_id'].'b') );
}
$you_result=pdo_fetch("select * from {$table_pe}lianhu_applist where {$where_list} ");
#查找此预约的限制，报名情况等
if($_GPC['submit'] && !$you_result){
     $in['appointment_id']=$_GPC['appointment_id'];
     $in['student_id']=$student_info['student_id'];
     $in['addtime']=time();
     $course_ids=$_POST['course'];
     foreach ($course_ids as $key => $value) {
         $course_re=pdo_fetch("select * from {$table_pe}lianhu_appointment_course where course_id=:id and status=1",array(':id'=>$key));
         if($value=='a'){
             $acount=pdo_fetchcolumn("select count(*) num from {$table_pe}lianhu_applist where status!=2 and 
                appointment_id=:appointment_id and content=:content  ",array(':appointment_id'=>$result['appointment_id'],':content'=>$key.'a') );    
            $in['content']=$key.'a';
         }else{
              $bcount=pdo_fetchcolumn("select count(*) num from {$table_pe}lianhu_applist where status!=2 and 
                appointment_id=:appointment_id and content=:content  ",array(':appointment_id'=>$result['appointment_id'],':content'=>$key.'b') );    
            $in['content']=$key.'b';
         }
         if($acount>=$course_re['course_num'] ||  $bcount>=$course_re['course_num']){
             message($course_re['course_name'].'预约已满，请刷新','','error');
         }
        pdo_insert('lianhu_applist',$in);
        $re=true;
     }
     if($re){
         $new_appointment_join_num=$result['appointment_join_num']+1;
         pdo_update('lianhu_appointment',array('appointment_join_num'=>$new_appointment_join_num),array('appointment_id'=>$result['appointment_id']));
     }
     
     message('预约成功',$this->createMobileUrl('applist_result'),'success');
}


