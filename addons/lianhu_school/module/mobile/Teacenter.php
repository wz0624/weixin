<?php
defined('IN_IA') or exit('Access Denied');
$teacher_info=$this->teacher_mobile_qx();
$result=pdo_fetch("select tea.* ,users.username,users.uid from {$table_pe}lianhu_teacher tea left join ".tablename('users')." users on  users.uid=tea.fanid where tea.teacher_id=:tid",array(":tid"=>$teacher_info['teacher_id']));
######
 $msg_count=count($this->web_msg(true));
######
$class_list=$this->getTeacherClass($teacher_info['teacher_id']);
foreach ($class_list['list'] as $key => $value) {
    $class_s .=$value['class_name'].',';
}
$class_s = trim($class_s,',');