<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
if (empty($_W['fans']['nickname'])) {
	mc_oauth_userinfo();
}
$orderid = intval($_GPC['orderid']);
$order = pdo_fetch("SELECT * FROM " . tablename($this->t_order) . " WHERE uniacid=:uniacid AND id=:orderid AND from_user=:from_user",array(':uniacid'=>$_W['uniacid'],':orderid'=>$orderid,':from_user'=>$_W['openid']));
if (empty($order)) {
	message('操作失败，请重新选择产品！',referer(),'error');
}
if ($order['status'] > 0) {
	message('订单已经支付或取消！',$this->createMobileUrl('orderlist'),'error');
}
$order_product = pdo_fetchall("SELECT p.name, p.thumb ,p.timeneed,p.price,o.* FROM " . tablename($this->t_order_product) . " o left join " . tablename($this->t_product) . " p on o.productid=p.id "
					. " WHERE o.orderid='{$orderid}'");
$total_price = pdo_fetchcolumn("SELECT sum(p.price*o.total) FROM " . tablename($this->t_order_product) . " o left join " . tablename($this->t_product) . " p on o.productid=p.id "
					. " WHERE o.orderid='{$orderid}'");
$total_time = pdo_fetchcolumn("SELECT sum(p.timeneed*o.total) FROM " . tablename($this->t_order_product) . " o left join " . tablename($this->t_product) . " p on o.productid=p.id "
					. " WHERE o.orderid='{$orderid}'");
$params['tid'] = $orderid;
$params['user'] = $_W['fans']['from_user'];
$params['fee'] = $total_price;
$params['title'] = "预约订单";
$params['ordersn'] = $order['ordersn'];
$title = "在线支付";
include $this->template('pay');
?>