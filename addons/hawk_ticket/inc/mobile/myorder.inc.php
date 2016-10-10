<?php
global $_W,$_GPC;
$this->checkMobile();
load()->model('mc');
$fansinfo = mc_fansinfo($_W['fans']['from_user']);
if($fansinfo['follow']!=1){
    $url = $this->module['config']['follow'];
	header("Location:{$url}");
	exit();
}
require_once(MODULE_ROOT.'/module/Activity.class.php');
require_once(MODULE_ROOT.'/module/Order.class.php');
$status = intval($_GPC['status']);
if(empty($status)){
    $status = 2;
}
if(!in_array($status,array(2,3))){
  $status=2;
}
$order = new Order();
$act = new Activity();
$filters = array();
$filters['status'] = $status;

$pindex = intval($_GPC['page']);
$pindex = max($pindex, 1);
$psize = 20;
$total = 0;
$ds = $order->getMyOrders($filters,$pindex, $psize, $total);
$pager = pagination($total, $pindex, $psize);
if(is_array($ds)){
    foreach($ds as $k=>&$v){
        $v['act'] = $act->getOne($v['actid']);
    }
}
$pars = array();
$pars['status']=3;
$check = $order->getMyMaster($pars);
if($check){
    $show=1;
}
include $this->template('myorder');