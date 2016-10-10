<?php
defined('IN_IA') or exit('Access Denied');
$student_info=$this->mobile_from_find_student();
$class_name=$student_info['class_name'];
$list=pdo_fetchall("select * from {$table_pe}lianhu_applist where student_id={$student_info['student_id']} order by addtime desc" );
foreach ($list as $key => $value) {
    if($value['content']){
            $course_id=intval($value['content']);    
            preg_match('/\w$/',$value['content'],$matchs);
            $course_name=pdo_fetchcolumn("select course_name from {$table_pe}lianhu_appointment_course where course_id={$course_id}");
            $list[$key]['my_course']=$course_name.':'.$matchs[0].'¿ÎÊ±';
     }
}
            