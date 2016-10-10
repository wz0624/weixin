<?php 
defined('IN_IA') or exit('Access Denied');
$teacher=$this->teacher_qx('no');
$school_uniacid=" and ".$this->where_uniacid_school;
$model=$_GPC['model'] ? $_GPC['model'] :"class";
$cid=$_GPC['cid'];
set_time_limit(0);
if($teacher=='teacher'){
			$uid=$_W['uid'];
			$t_id  =pdo_fetchcolumn("select teacher_id from {$table_pe}lianhu_teacher where fanid={$uid} {$school_uniacid}");
			$t_name=pdo_fetchcolumn("select teacher_realname from {$table_pe}lianhu_teacher where fanid={$uid} {$school_uniacid}");
			$list  =pdo_fetchall("select class.* from {$table_pe}lianhu_class class  where class.status=1 and class.teacher_id={$t_id} {$school_uniacid}");
            $course_list=$this->teacherCourse($t_id);
}else{
			$list=pdo_fetchall("select class.* from {$table_pe}lianhu_class class  where class.status=1  {$school_uniacid}");
			$t_name="管理员";
            $course_list=$this->returnAllEfficeCourse();
}
if($cid){
	$class=pdo_fetch("select * from {$table_pe}lianhu_class where class_id=:cid {$school_uniacid} ",array(':cid'=>$_GPC['cid']));
	if(!$class) message('没有找到此班级',$this->createWebUrl('line'),'error');
}
##发布作业
if($ac=='new'){
    if($_GPC['submit'] && $_GPC['class_ids'] ){
       if($_GPC['img'])
         $in['img']    =serialize($_GPC['img']);
       $in['content']  =$_GPC['content'];
       $in['uniacid']  =$_W['uniacid'];
       $in['school_id']=$_SESSION['school_id'];
       $in['course_id']=$_GPC['course_id'];
       $in['teacher_id']=$t_id;
       $in['add_time'] =time();
       $que_num=false;
       foreach ($_GPC['class_ids'] as $key => $value) {
           if($value){
               $in['class_id']=$value;
               pdo_insert('lianhu_homework',$in);
               $hid=pdo_insertid();
               $que_num=$this->send_class_msg($hid,$que_num);
           }
       }
        message("作业发布成功，进入消息发布通道请勿关闭网页",$this->createWebUrl('sendToMsg',array('que_num'=>$que_num)),'success');        
    }
    $ac ='list';
}

if($ac=='old'){
    if($t_id){
        $where          =" teacher_id = :tid and ";
        $params[":tid"] =$t_id;
    }
     $where          .="class_id=:cid";
     $params[":cid"] =$cid;
     $list=pdo_fetchall("select * from {$table_pe}lianhu_homework where {$where} ",$params);
}
if($ac=='edit'){
   $result=pdo_fetch("select * from {$table_pe}lianhu_homework where homework_id=:hid ",array(":hid"=>$_GPC['hid']) );
   if($result['img']) $images=unserialize($result['img']);
   if($_GPC['submit'] && $_GPC['hid'] ){
       if($_GPC['img'])
             $in['img']    =serialize($_GPC['img']);
       $in['content']  =$_GPC['content'];
       $in['status']   =$_GPC['status'];
       $in['course_id']=$_GPC['course_id'];
       pdo_update('lianhu_homework',$in,array('homework_id'=>$_GPC['hid']));
        message("编辑",$this->createWebUrl('homework'),'success');        
    }
}
 
