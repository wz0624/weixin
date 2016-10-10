<?php
global $_W,$_GPC;
$this->checkMobile();
require_once(MODULE_ROOT.'/module/Activity.class.php');
require_once(MODULE_ROOT.'/module/Order.class.php');
$order = new Order();
$act = new Activity();
$filters = array();
$filters['status'] = 3;
$pindex = intval($_GPC['page']);
$pindex = max($pindex, 1);
$psize = 20;
$total = 0;
$ds = $order->getMyMaster($filters,$pindex, $psize, $total);
$pager = pagination($total, $pindex, $psize);
include $this->template('mymaster');