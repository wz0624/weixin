<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
$config = $this->module['config'];
$bg_img = !empty($config['member_bg']) ? $config['member_bg'] : './addons/hx_o2o/template/style/images/member_bg.jpg';
$title = '我的订单';
$op = $_GPC['op'];
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'getorder') {
	$status = intval($_GPC['status']);
	$pindex = max(1, intval($_GPC['page']));
	$psize = 5;
	if ($_GPC['type'] == 2) {
		$type = 2;
	}else{
		$type = 1;
	}
	$condition = ' WHERE `uniacid` = :uniacid AND `from_user`=:from_user AND `type` = :type AND `status` = :status';
	$params = array(':uniacid' => $_W['uniacid'],':from_user'=>$_W['openid'],':type'=>$type, ':status' => $status);
	$sql = 'SELECT COUNT(*) FROM ' . tablename($this->t_order) . $condition;
	$total = pdo_fetchcolumn($sql, $params);
	if (!empty($total)) {
		$sql = 'SELECT id,ordersn,shopid,date,time,price,createtime,status FROM ' . tablename($this->t_order) . $condition . ' ORDER BY `id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		$html = '';
		foreach ($list as $key => $value) {
			$order_shop = pdo_fetch("SELECT thumb_sm,name FROM ".tablename($this->t_shops)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$value['shopid']));
			$order_product = pdo_fetchall("SELECT p.name, p.thumb ,p.timeneed,p.price,o.total FROM " . tablename($this->t_order_product) . " o left join " . tablename($this->t_product) . " p on o.productid=p.id "
				. " WHERE o.orderid='{$value['id']}'");
			if ($value['status'] == 0) {
				$flag = '<span class="status confirm">待支付</span>';
			}elseif ($value['status'] == 1) {
				$flag = '<span class="status serving">进行中</span>';
			}elseif ($value['status'] == 2) {
				$flag = '<span class="status finish">已完成</span>';
			}else{
				$flag = '<span class="status cancel">已取消</span>';
			}
			$html .= '<div class="title"><span class="number">订单号：'.$value['ordersn'].'</span>'.$flag.'<span class="amount">￥<strong>'.$value['price'].'</strong></span></div>';
			$html .= '<div class="row"><div class="col s4 img"><img src="'.tomedia($order_shop['thumb_sm']).'" class="responsive-img"><span>门店: '.$order_shop['name'].'</span></div><div class="col s8 info"><ul>';
			foreach ($order_product as $k => $v) {
				$html .= '<li>' . $v['name'] . ' x' . $v['total'] . '</li>';
			}
			$html .= '</ul><div class="time"><span>下单时间：'.date('Y.m.d H:i',$value['createtime']).'</span><span>服务时间：'.date('Y.m.d H:i',strtotime($value['date'].$value['time'])).'</span></div><a href="'.$this->createMobileUrl('orderlist',array('op'=>'detail','orderid'=>$value['id'])).'" class="waves-effect waves-light btn right see_info"><i class="fa fa-arrow-right fa-1x"></i> 查看</a></div></div>';
			$html .= '<div class="split_line"></div>';
			unset($order_shop);unset($order_product);
		}
		$pager = pagination($total, $pindex, $psize);
		die(json_encode(array('status'=>1,'html'=>$html)));
	}else{
		die(json_encode(array('status'=>0)));
	}
}elseif ($operation == 'detail') {
	$orderid = intval($_GPC['orderid']);
	$order = pdo_fetch("SELECT * FROM " . tablename($this->t_order) . " WHERE uniacid=:uniacid AND id=:orderid AND from_user=:from_user",array(':uniacid'=>$_W['uniacid'],':orderid'=>$orderid,':from_user'=>$_W['openid']));
	if (empty($order)) {
		message('操作失败，请重新选择产品！',referer(),'error');
	}
	$order_product = pdo_fetchall("SELECT p.name, p.thumb ,p.timeneed,p.price,o.* FROM " . tablename($this->t_order_product) . " o left join " . tablename($this->t_product) . " p on o.productid=p.id "
				. " WHERE o.orderid='{$orderid}'");
	$total_price = pdo_fetchcolumn("SELECT sum(p.price*o.total) FROM " . tablename($this->t_order_product) . " o left join " . tablename($this->t_product) . " p on o.productid=p.id "
				. " WHERE o.orderid='{$orderid}'");
	$total_time = pdo_fetchcolumn("SELECT sum(p.timeneed*o.total) FROM " . tablename($this->t_order_product) . " o left join " . tablename($this->t_product) . " p on o.productid=p.id "
				. " WHERE o.orderid='{$orderid}'");
	$order_address = pdo_fetch("SELECT * FROM ".tablename($this->t_address)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$order['addressid']));
	$order_shop = pdo_fetch("SELECT * FROM ".tablename($this->t_shops)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$order['shopid']));
	$order_shop['score_html'] = '';
	for ($i=0; $i < $order_shop['score']; $i++) { 
		$order_shop['score_html'] .= '<i class="fa fa-heart"></i> ';
	}
	$title="订单详情";
	include $this->template('order-detail');
}elseif ($operation == 'detailadmin') {
	if ($_W['openid'] != $config['kfid']) {
		include $this->template('accesserror');exit();
	}
	$orderid = intval($_GPC['orderid']);
	$order = pdo_fetch("SELECT * FROM " . tablename($this->t_order) . " WHERE uniacid=:uniacid AND id=:orderid",array(':uniacid'=>$_W['uniacid'],':orderid'=>$orderid));
	if (empty($order)) {
		message('操作失败，请重新选择产品！',referer(),'error');
	}
	$order_product = pdo_fetchall("SELECT p.name, p.thumb ,p.timeneed,p.price,o.* FROM " . tablename($this->t_order_product) . " o left join " . tablename($this->t_product) . " p on o.productid=p.id "
				. " WHERE o.orderid='{$orderid}'");
	$total_price = pdo_fetchcolumn("SELECT sum(p.price*o.total) FROM " . tablename($this->t_order_product) . " o left join " . tablename($this->t_product) . " p on o.productid=p.id "
				. " WHERE o.orderid='{$orderid}'");
	$total_time = pdo_fetchcolumn("SELECT sum(p.timeneed*o.total) FROM " . tablename($this->t_order_product) . " o left join " . tablename($this->t_product) . " p on o.productid=p.id "
				. " WHERE o.orderid='{$orderid}'");
	$order_address = pdo_fetch("SELECT * FROM ".tablename($this->t_address)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$order['addressid']));
	$order_shop = pdo_fetch("SELECT * FROM ".tablename($this->t_shops)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$order['shopid']));
	$order_shop['score_html'] = '';
	for ($i=0; $i < $order_shop['score']; $i++) { 
		$order_shop['score_html'] .= '<i class="fa fa-heart"></i> ';
	}
	$title="订单详情";
	include $this->template('order-detail-admin');
}else{
	$type = intval($_GPC['type']);
	if ($type == 2) {
		$title = '我的护理疗程';
	}else{
		$title = '我的订单';
	}
	include $this->template('order-list');
}
?>