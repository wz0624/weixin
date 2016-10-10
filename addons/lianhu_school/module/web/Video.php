<?php 
defined('IN_IA') or exit('Access Denied');
$this->teacher_qx();//只有管理员能进入
if($ac=='list'){
    $list=pdo_fetchall("select * from {$table_pe}lianhu_video where 1=1 order by video_id desc");
    
}
if($ac=='new'){
    if($_GPC['submit']){
        $in['video_name']=$_GPC['video_name'];
        $in['video_url']=$_GPC['video_url'];
        $in['begin_time']=$_GPC['begin_time'];
        $in['end_time']=$_GPC['end_time'];
        $in['video_img']=$_GPC['video_img'];
        $in['status']=$_GPC['status'];
        $in['school_id']=$_SESSION['school_id'];
        $in['uniacid']=$_W['uniacid'];
        $in['add_time']=time();
        pdo_insert('lianhu_video',$in);
        message("视频添加成功",$this->createWebUrl('video'),'success');
    }
}
if($ac=='edit'){
    $id=$_GPC['id'];
    if(empty($id)){message('非法传输','','error');}
    $result=pdo_fetch("select * from {$table_pe}lianhu_video where video_id=:vid",array(':vid'=>$id));
    if($_GPC['submit']){
        $in['video_name']=$_GPC['video_name'];
        $in['video_url']=$_GPC['video_url'];
        $in['begin_time']=$_GPC['begin_time'];
        $in['end_time']=$_GPC['end_time'];
        $in['video_img']=$_GPC['video_img'];
        $in['status']=$_GPC['status'];
        // pdo_update('lianhu_video',$in,array('video_id'=>$id));
        pdo_update('lianhu_video',$in,array('video_id'=>$id));
        message('修改成功',$this->createWebUrl('video'),'success');					
    }    
}
