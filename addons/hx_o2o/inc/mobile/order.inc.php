<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
$op = $_GPC['op'];
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
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
	$order_address = pdo_fetch("SELECT * FROM ".tablename($this->t_address)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$order['addressid']));
	$order_shop = pdo_fetch("SELECT * FROM ".tablename($this->t_shops)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$order['shopid']));
	$order_shop['score_html'] = '';
	for ($i=0; $i < $order_shop['score']; $i++) { 
		$order_shop['score_html'] .= '<i class="fa fa-heart"></i> ';
	}
	$title="确认订单";
	include $this->template('order');
}elseif ($operation == 'check') {
	$orderid = intval($_GPC['orderid']);
	$payment = intval($_GPC['payment']);
	$order = pdo_fetch("SELECT * FROM " . tablename($this->t_order) . " WHERE uniacid=:uniacid AND id=:orderid AND from_user=:from_user",array(':uniacid'=>$_W['uniacid'],':orderid'=>$orderid,':from_user'=>$_W['openid']));
	if (empty($order)) {
		die(json_encode(array('status'=>'-1')));
	}
	if ($payment == 2) { //在线支付
		die(json_encode(array('status'=>1,'paytype'=>2)));
	}else{ //到店支付
		$order_product = pdo_fetch("SELECT productid FROM ".tablename($this->t_order_product)." WHERE orderid=:orderid",array(':orderid'=>$order['id']));
		$order_product_info = pdo_fetch("SELECT * FROM ".tablename($this->t_product)." WHERE id=:id",array(':id'=>$order_product['productid']));
		$order_address = pdo_fetch("SELECT * FROM ".tablename($this->t_address)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$order['addressid']));
		$order_shop = pdo_fetch("SELECT * FROM ".tablename($this->t_shops)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$order['shopid']));
		$settings = $this->module['config'];
		if (!empty($settings['kfid']) && !empty($settings['k_templateid'])) {
			$kfirst = empty($settings['kfirst']) ? '您有一个新的预约订单' : $settings['kfirst'];
			$kfoot = empty($settings['kfoot']) ? '请及时处理，点击可查看详情' : $settings['kfoot'];
			$kurl = $_W['siteroot'] . 'app' . str_replace('./', '/', $this->createMobileUrl('orderlist',array('op'=>'detailadmin','orderid'=>$order['id'])));
			$kdata = array(
				'first' => array(
					'value' => $kfirst,
					'color' => '#ff510'
				),
				'keyword1' => array(
					'value' => $order['ordersn'],
					'color' => '#ff510'
				),
				'keyword2' => array(
					'value' => '预约订单',
					'color' => '#ff510'
				),
				'keyword3' => array(
					'value' => $order['price'] . '元',
					'color' => '#ff510'
				),
				'keyword4' => array(
					'value' => $order_address['consignee'],
					'color' => '#ff510'
				),
				'keyword5' => array(
					'value' => '到店支付',
					'color' => '#ff510'
				),
				'remark' => array(
					'value' => $kfoot ,
					'color' => '#ff510'
				),
			);
			$acc = WeAccount::create();
			$acc->sendTplNotice($settings['kfid'], $settings['k_templateid'], $kdata, $kurl, $topcolor = '#FF683F');
		}
		if (!empty($settings['m_templateid'])) {
			$mfirst = empty($settings['mfirst']) ? '您已经成功预约' : $settings['mfirst'];
			$mfoot = empty($settings['mfoot']) ? '为了完美的服务体验请提前安排好时间' : $settings['mfoot'];
			$murl = $_W['siteroot'] . 'app' . str_replace('./', '/', $this->createMobileUrl('orderlist',array('op'=>'detail','orderid'=>$order['id'])));
			$mdata = array(
				'first' => array(
					'value' => $mfirst,
					'color' => '#ff510'
				),
				'keyword1' => array(
					'value' => $order_product_info['name'],
					'color' => '#ff510'
				),
				'keyword2' => array(
					'value' => date('Y年m月d日 H:i',strtotime($order['date'].$order['time'])),
					'color' => '#ff510'
				),
				/*'keyword3' => array(
					'value' => $order['price'] . '元',
					'color' => '#ff510'
				),
				'keyword4' => array(
					'value' => '预约订单',
					'color' => '#ff510'
				),*/
				'remark' => array(
					'value' => $mfoot ,
					'color' => '#ff510'
				),
			);
			$acc = WeAccount::create();
			$acc->sendTplNotice($order['from_user'], $settings['m_templateid'], $mdata, $murl, $topcolor = '#FF683F');
		}
		pdo_update($this->t_order, array('status'=>1,'paytype'=>3), array('id'=>$orderid));
		die(json_encode(array('status'=>1,'paytype'=>3)));
	}
}elseif ($operation == 'fail') {
	include $this->template('fail');
}

?>