<?php 
defined('IN_IA') or exit('Access Denied');
set_time_limit(0);
#教师权限
$teacher_info=$this->teacher_mobile_qx();
$_W['uid']=$teacher_info['fanid'];
$model=$_GPC['model'] ? $_GPC['model'] :"class";
if($model=='class')
    $result=$this->teacher_standard('no');
else
    $result=$this->student_standard();	

$course_list=$this->teacherCourse($teacher_info['teacher_id'],1);
if($_POST['class_list']){
    $in['school_id']    =$_SESSION['school_id'];
    $in['content']      =$_GPC['content'];
    $in['course_id']    =$_GPC['course_id'];
    $in['teacher_id']   =$teacher_info['teacher_id'];
    $in['add_time']     =time();
    $in['uniacid']      =$_W['uniacid'];
    $img_arrs           =$_POST['img_value'];
    if($img_arrs){
        foreach ($img_arrs as $key => $value) {
             $img=$this->getWechatMedia($value);
             if($img) 
                $img_in[]= $img;
             else 
                $img_in[]= $up_file_name; 
        }
    }
//    if($_POST['voice_value'])
//         $voice=$this->getWechatMedia($_POST['voice_value'],2);
   if($img_in)
          $in['img']        =serialize($img_in);
    if($voice)
          $in['voice']       =$voice; 
    foreach ($_POST['class_list'] as $key => $value) {
           if($value){
               $in['class_id']=$value;
               pdo_insert('lianhu_homework',$in);
               $hid=pdo_insertid();
               $hids[]=$hid;
           }
    }
    $que_num=false;
    foreach ($hids as $key => $value) {
         $que_num=$this->send_class_msg($value,$que_num);
    }
  message("作业发布成功，进入消息发布通道请勿关闭网页",$this->createMobileUrl('sendToMsg',array('que_num'=>$que_num)),'success');        
}
