<?php
defined('IN_IA') or exit('Access Denied');
$teacher_info=$this->teacher_mobile_qx();
$_W['uid']=$teacher_info['fanid'];
$result=pdo_fetchall("select * from {$table_pe}lianhu_class where  status=1 and teacher_id={$teacher_info['teacher_id']}");
if(empty($result)){echo json_encode(array('msg'=>'您不是班主任'));exit();}
foreach ($result as $key => $value) {
	$class_id_arr[$key]=$value['class_id'];
}
$class_id_str=implode(',', $class_id_arr);
$student_list=pdo_fetchall("select * from {$table_pe}lianhu_student where class_id in({$class_id_str}) and status=1 and fanid !='' ");	
$mu_id=$this->module['config']['msg'];
$acid=pdo_fetchcolumn("select acid from ".tablename('account')." where uniacid={$_W['uniacid']}");
load()->classs('weixin.account');
$accObj= WeixinAccount::create($acid);
$i=0;
foreach ($student_list as $key => $value) {
	#遍历and发送
	$openid=pdo_fetchcolumn("select openid from ".tablename('mc_mapping_fans')." where fanid={$value['fanid']} ");
	$data=array(
		'first'=>array('value'=>'家长您好，您的孩子放学啦！'),
		'keyword1'=>array('value'=>'放学通知'),
		'keyword2'=>array('value'=>'放学通知'),
		'keyword3'=>array('value'=>$teacher_info['teacher_realname']),
		'keyword4'=>array('value'=>date("Y-m-d H:i:s",TIMESTAMP)),
		'remark'=>array('value'=>'祝您愉快！'),
		);
	$accObj->sendTplNotice($openid,$mu_id,$data);
	$i++;
}
echo json_encode(array('status'=>'yes'));
exit();