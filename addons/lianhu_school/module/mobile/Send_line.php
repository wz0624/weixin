<?php 
defined('IN_IA') or exit('Access Denied');
set_time_limit(0);
$uid=$_W['member']['uid'];
if(empty($uid))
   $uid=$this->register_member();
$result=$this->mobile_from_find_student();
#begin
if($_POST['content']){
    #解析图片
    $img_arrs=$_POST['img_value'];
    if($img_arrs){
        foreach ($img_arrs as $key => $value) {
                $img_in[]=$this->getWechatMedia($value); 
        }
    }
        $in['uniacid']      =$_W['uniacid'];
        $in['school_id']    =$_SESSION['school_id'];
        $in['class_id']     =$result['class_id'];
        $in['send_uid']     =$uid;
        $in['send_content'] =$_POST['content'];
        if($img_in)
        $in['send_image']   =serialize($img_in);
        $in['send_status']  =1;
        $in['add_time']     =time();
        pdo_insert('lianhu_send',$in);
        message('分享成功',$this->createMobileUrl('line'),'success');
}