<?php
defined('IN_IA') or exit('Access Denied');
$teacher_info=$this->teacher_mobile_qx();
$_W['uid']=$teacher_info['fanid'];
$result=pdo_fetch("select tea.* ,users.username,users.uid from {$table_pe}lianhu_teacher tea left join ".tablename('users')." users on  users.uid=tea.fanid where tea.teacher_id={$teacher_info['teacher_id']}");
if($_GPC['submit']){
	$up['teacher_telphone']=$_GPC['teacher_telphone'];
	$up['teacher_email']=$_GPC['teacher_email'];
    
    if(!strstr($_GPC['img_value'],'images') && $_GPC['img_value'])
        $up['teacher_img'] = $this->getWechatMedia($_POST['img_value'],1,false);  
          
    if(!strstr($_GPC['img_value_qr'],'images')  && $_GPC['img_value_qr'])
        $up['weixin_code'] = $this->getWechatMedia($_POST['img_value_qr'],1,false);  
        
	$up['teacher_introduce']=$_GPC['teacher_introduce'];				
	$up['teacher_realname'] =$_GPC['teacher_realname'];				
	mc_update($_W['member']['uid'],array('nickname'=>$up['teacher_realname']));
    if ($up['teacher_img'])
	    mc_update($_W['member']['uid'],array('avatar'=>$_W['attachurl'].$up['teacher_img']));
    pdo_update('lianhu_teacher',$up,array('teacher_id'=>$teacher_info['teacher_id']));
	message('修改成功',$this->createMobileUrl('tea_data'),'success');
}