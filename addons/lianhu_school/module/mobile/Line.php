<?php
defined('IN_IA') or exit('Access Denied');
#教师或者学生家长
$signPackage=$this->getSignPackage();
$in_type    =$this->judePortType();
if(empty($in_type)) 
    message('您还未绑定',$this->createMobileUrl('home'),'error');
if($_GPC['class_id'])
    $class_id=$_GPC['class_id'];
 else 
    $class_id=$in_type['info']['class_id'];   

$class_name= $this->className($class_id);
####班级圈类别
if($this->module['config']['line_type'][$_SESSION['school_id']]){
	$line_type_cfg=explode("||", $this->module['config']['line_type'][$_SESSION['school_id']]);
	foreach ($line_type_cfg as $key => $value) {
		if($value){
			$line_type[]=$value;
		}
	}
	$_W['line_type']=$line_type;
}
$tiao_count  =count($_W['line_type']);
# 公共班级圈
if($op=='list')
     $list=$this->getLineList(1,10,$class_id);




