<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
load()->func('tpl');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$shops = pdo_fetchall("SELECT * FROM " . tablename($this->t_shops) . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$status = $_GPC['status'];
	$condition = " o.uniacid = :uniacid";
	$paras = array(':uniacid' => $_W['uniacid']);
	if (empty($starttime) || empty($endtime)) {
		$starttime = strtotime('-1 month');
		$endtime = TIMESTAMP;
	}
	if (!empty($_GPC['time'])) {
		$starttime = strtotime($_GPC['time']['start']);
		$endtime = strtotime($_GPC['time']['end']) + 86399;
		$condition .= " AND o.createtime >= :starttime AND o.createtime <= :endtime ";
		$paras[':starttime'] = $starttime;
		$paras[':endtime'] = $endtime;
	}
	if (!empty($_GPC['paytype'])) {
		$condition .= " AND o.paytype = '{$_GPC['paytype']}'";
	} elseif ($_GPC['paytype'] === '0') {
		$condition .= " AND o.paytype = '{$_GPC['paytype']}'";
	}
	if (!empty($_GPC['shopid'])) {
		$condition .= " AND o.shopid = '{$_GPC['shopid']}'";
	}
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND o.ordersn LIKE '%{$_GPC['keyword']}%'";
	}
	if ($status != '') {
		$condition .= " AND o.status = '" . intval($status) . "'";
	}
	$sql = 'SELECT COUNT(*) FROM ' . tablename($this->t_order) . ' AS `o` WHERE ' . $condition;
	$total = pdo_fetchcolumn($sql, $paras);
	if ($total > 0) {
		if ($_GPC['export'] != 'export') {
			$limit = ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
		} else {
			$limit = '';
			$condition = " o.uniacid = :uniacid";
			$paras = array(':uniacid' => $_W['uniacid']);
		}
		$sql = 'SELECT * FROM ' . tablename($this->t_order) . ' AS `o` WHERE ' . $condition . ' ORDER BY
					`o`.`status` DESC, `o`.`createtime` DESC ' . $limit;

		$list = pdo_fetchall($sql,$paras);
		$pager = pagination($total, $pindex, $psize);

		$paytype = array (
			'0' => array('css' => 'default', 'name' => '未支付'),
			'1' => array('css' => 'danger','name' => '余额支付'),
			'2' => array('css' => 'info', 'name' => '在线支付'),
			'3' => array('css' => 'warning', 'name' => '到店支付')
		);
		$orderstatus = array (
			'0' => array('css' => 'danger', 'name' => '待付款'),
			'1' => array('css' => 'info', 'name' => '进行中'),
			'2' => array('css' => 'warning', 'name' => '已完成'),
			'3' => array('css' => 'success', 'name' => '已取消')
		);
		foreach ($list as &$value) {
			$s = $value['status'];
			$value['statuscss'] = $orderstatus[$value['status']]['css'];
			$value['status'] = $orderstatus[$value['status']]['name'];
			$address = pdo_fetch("SELECT * FROM " . tablename($this->t_address) . " WHERE id = :id", array(':id' => $value['addressid']));
			$value['consignee'] = $address['consignee'];
			$value['mobile'] = $address['mobile'];
			$value['address'] = $address['city'] . $address['address'] . $address['room'];
			if ($s < 1) {
				$value['css'] = $paytype[$s]['css'];
				$value['paytype'] = $paytype[$s]['name'];
				continue;
			}
			$value['css'] = $paytype[$value['paytype']]['css'];
			if ($value['paytype'] == 2) {
				if (empty($value['transid'])) {
					$value['paytype'] = '支付宝支付';
				} else {
					$value['paytype'] = '微信支付';
				}
			} else {
				$value['paytype'] = $paytype[$value['paytype']]['name'];
			}
			unset($address);
		}

		if ($_GPC['export'] != '') {
			/* 输入到CSV文件 */
			$html = "\xEF\xBB\xBF";

			/* 输出表头 */
			$filter = array(
				'ordersn' => '订单号',
				'consignee' => '姓名',
				'mobile' => '电话',
				'paytype' => '支付方式',
				'price' => '价格',
				'status' => '状态',
				'createtime' => '下单时间',
				'address' => '地址信息'
			);

			foreach ($filter as $key => $title) {
				$html .= $title . "\t,";
			}
			$html .= "\n";
			foreach ($list as $k => $v) {
				foreach ($filter as $key => $title) {
					if ($key == 'createtime') {
						$html .= date('Y-m-d H:i:s', $v[$key]) . "\t, ";
					} else {
						$html .= $v[$key] . "\t, ";
					}
				}
				$html .= "\n";
			}


			/* 输出CSV文件 */
			header("Content-type:text/csv");
			header("Content-Disposition:attachment; filename=全部数据.csv");
			echo $html;
			exit();

		}
	}

} elseif ($operation == 'detail') {
	$id = intval($_GPC['id']);
	$item = pdo_fetch("SELECT * FROM " . tablename($this->t_order) . " WHERE id = :id", array(':id' => $id));
	if (empty($item)) {
		message("抱歉，订单不存在!", referer(), "error");
	}
	if (checksubmit('finish')) {
		pdo_update($this->t_order, array('status' => 2, 'remark' => $_GPC['remark']), array('id' => $id));
		message('订单操作成功！', referer(), 'success');
	}
	if (checksubmit('cancel')) {
		pdo_update($this->t_order, array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
		message('取消完成订单操作成功！', referer(), 'success');
	}
	if (checksubmit('cancelpay')) {
		pdo_update($this->t_order, array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
		message('取消订单付款操作成功！', referer(), 'success');
	}
	if (checksubmit('confrimpay')) {
		pdo_update($this->t_order, array('status' => 1, 'paytype' => 2, 'remark' => $_GPC['remark']), array('id' => $id));
		message('确认订单付款操作成功！', referer(), 'success');
	}
	if (checksubmit('close')) {
		$item = pdo_fetch("SELECT transid FROM " . tablename($this->t_order) . " WHERE id = :id", array(':id' => $id));
		pdo_update($this->t_order, array('status' => 3, 'remark' => $_GPC['remark']), array('id' => $id));
		message('订单关闭操作成功！', referer(), 'success');
	}
	if (checksubmit('open')) {
		pdo_update($this->t_order, array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
		message('开启订单操作成功！', referer(), 'success');
	}
	// 订单取消
	if (checksubmit('cancelorder')) {
		if ($item['status'] == 1) {
			load()->model('mc');
			$memberId = mc_openid2uid($item['from_user']);
			mc_credit_update($memberId, 'credit2', $item['price'], array($_W['uid'], '微商城取消订单退款说明'));
		}
		pdo_update($this->t_order, array('status' => '3'), array('id' => $item['id']));
		message('订单取消操作成功！', referer(), 'success');
	}

	// 收货地址信息
	$item['user'] = pdo_fetch("SELECT * FROM " . tablename($this->t_address) . " WHERE id = :id", array(':id' => $item['addressid']));
	$item['shop'] = pdo_fetch("SELECT * FROM " . tablename($this->t_shops) . " WHERE id = :id", array(':id' => $item['shopid']));
	$goods = pdo_fetchall("SELECT g.*, o.total,o.price as orderprice FROM " . tablename($this->t_order_product) .
					" o left join " . tablename($this->t_product) . " g on o.productid=g.id " . " WHERE o.orderid='{$id}'");
	$item['goods'] = $goods;
} elseif ($operation == 'delete') {
	/*订单删除*/
	$orderid = intval($_GPC['id']);
	if (pdo_delete($this->t_order, array('id' => $orderid))) {
		message('订单删除成功', $this->createWebUrl('order', array('op' => 'display')), 'success');
	} else {
		message('订单不存在或已被删除', $this->createWebUrl('order', array('op' => 'display')), 'error');
	}
}
include $this->template('order');
?>