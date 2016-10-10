<?php 
defined('IN_IA') or exit('Access Denied');
$uid=$_W['member']['uid'];
$my_info=pdo_fetch("select * from ".tablename('mc_members')." where uid=:uid ",array(':uid'=>$uid));

if($_GPC['submit']){
    $up['nickname']=$_GPC['nickname'];
    $up['mobile']=$_GPC['mobile'];
    pdo_update('mc_members',$up,array('uid'=>$uid) );
    message("更新成功",$this->createMobileUrl('home'),'success');
}
