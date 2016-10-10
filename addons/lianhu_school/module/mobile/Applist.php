<?php
defined('IN_IA') or exit('Access Denied');
$school_uniacid=" and uniacid={$_W['uniacid']} and school_id={$_SESSION['school_id']}";
$student_info=$this->mobile_from_find_student();
$class_name=$student_info['class_name'];
$where=" appointment_type_limit=0 || (appointment_type_limit=1 && appointment_grade_class like '%{$student_info['grade_id']}%' ) || (appointment_type_limit=2 && appointment_grade_class like '%{$student_info['class_id']}%' ) ";

$total=pdo_fetchcolumn("select count(*) num from {$table_pe}lianhu_appointment where ({$where}) and status !=0 {$school_uniacid} ");
$list=pdo_fetchall("select* from {$table_pe}lianhu_appointment where ({$where})  and status !=0  {$school_uniacid} order by appointment_addtime desc ");











