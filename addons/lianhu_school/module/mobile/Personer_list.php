<?php 
defined('IN_IA') or exit('Access Denied');
$uid=$_W['member']['uid'];
$result=$this->mobile_from_find_student();
$class_id=$result['class_id'];
$teacher_list=$this->classTeacher($class_id);


