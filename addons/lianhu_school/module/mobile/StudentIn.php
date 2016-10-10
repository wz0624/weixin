<?php 
defined('IN_IA') or exit('Access Denied');
$hash_add_str='asdas;#sdf';
$this->teacher_mobile_qx();
$signPackage=$this->getSignPackage();

$student_re=pdo_fetch("select * from {$table_pe}lianhu_student where student_id=:id",array(':id'=>$_GPC['sid']));
$hash_str=sha1(md5($student_re['class_id'].$student_re['grade_id'].$student_re['xuhao'].$hash_add_str));
if($hash_str!=$_GPC['hash'])
    message("学生代码不正确，非法二维码",$this->createMobileUrl('teacenter'),'error');
    
$url=$this->createMobileUrl('studentIn',array('hash'=>$hash_str,'sid'=>$_GPC['sid'],'live_in'=>'in'));
$type='live_in=in';
if($_GPC['send']==1){
    $acid=pdo_fetchcolumn("select acid from ".tablename('account')." where uniacid={$_W['uniacid']}");
    load()->classs('weixin.account');
    $accObj= WeixinAccount::create($acid);
    
    $in['school_id']=$_SESSION['school_id'];
    $in['uniacid']=$_W['uniacid'];
    $in['teacher_id']=$_SESSION['teacher_id'];
    $in['addtime']=TIMESTAMP;
    $in['student_id']=$student_re['student_id'];
   pdo_insert('lianhu_student_live',$in);
    
    $mu_id=$this->module['config']['msg'];
	$openid=pdo_fetchcolumn("select openid from ".tablename('mc_mapping_fans')." where fanid={$student_re['fanid']} ");
	$openid1=pdo_fetchcolumn("select openid from ".tablename('mc_mapping_fans')." where fanid={$student_re['fanid1']} ");
	$openid2=pdo_fetchcolumn("select openid from ".tablename('mc_mapping_fans')." where fanid={$student_re['fanid2']} ");
	$data=array(
		'first'=>array('value'=>'家长您好，您的孩子到校啦！'),
		'keyword1'=>array('value'=>'到校通知'),
		'keyword2'=>array('value'=>'到校通知'),
		'keyword3'=>array('value'=>$teacher_info['teacher_realname']),
		'keyword4'=>array('value'=>date("Y-m-d H:i:s",TIMESTAMP)),
		'remark'=>array('value'=>'祝您愉快！'),
		);
    
	$accObj->sendTplNotice($openid,$mu_id,$data);
	$accObj->sendTplNotice($openid1,$mu_id,$data);
	$accObj->sendTplNotice($openid2,$mu_id,$data);
    $have_send=1;
}