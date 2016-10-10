<?php 
defined('IN_IA') or exit('Access Denied');
$result=$this->mobile_from_find_student();
#更新
if($_GPC['img_value'] || $_GPC['parent_phone'] || $_GPC['parent_name']){
    $up['address']       = $_GPC['address'];
    $up['student_link']  = $_GPC['student_link'];
    $up['parent_name']   = $_GPC['parent_name'];
    $up['parent_phone']  = $_GPC['parent_phone'];
    if( ! strstr($_GPC['img_value'],'images') )
        $up['student_img'] = $this->getWechatMedia($_GPC['img_value'],1,false);
    pdo_update('lianhu_student',$up,array('student_id'=>$result['student_id']) );        
    message("更新成功",$this->createMobileUrl('real_xiangxi'),'success');
}