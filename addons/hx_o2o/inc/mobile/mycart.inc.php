<?php
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$op = $_GPC['op'];
if ($op == 'add') {
	$productid = intval($_GPC['product_id']);
	$product = pdo_fetch("SELECT id, catid,type, price FROM " . tablename($this->t_product) . " WHERE id = :id", array(':id' => $productid));
	if (empty($product)) {
		die(json_encode(array('status'=>0,'message'=>'抱歉，该商品不存在或是已经被删除！')));
	}
	$price = $product['price'];
	$row = pdo_fetch("SELECT id, total FROM " . tablename($this->t_cart) . " WHERE from_user = :from_user AND uniacid = '{$_W['uniacid']}' AND productid = :productid", array(':from_user' => $_W['fans']['from_user'], ':productid' => $productid));
	if ($row == false) {
		//不存在
		$data = array(
			'uniacid' => $_W['uniacid'],
			'productid' => $productid,
			'producttype' => $product['type'],
			'price' => $price,
			'from_user' => $_W['fans']['from_user'],
			'total' => 1,
		);
		pdo_insert($this->t_cart, $data);
	} else {
		//累加最多限制购买数量
		$t = 1 + $row['total'];
		$data = array(
			'price' => $price,
			'total' => $t,
		);
		pdo_update($this->t_cart, $data, array('id' => $row['id']));
	}
	//返回数据
	$carttotal = $this->getCartTotal();
	$result = array(
		'status' => 1,
		'message' => "成功加入购物车",
		'total' => $carttotal
	);
	die(json_encode($result));
} else if ($op == 'clear') {
	pdo_delete($this->t_cart, array('from_user' => $_W['fans']['from_user'], 'uniacid' => $_W['uniacid']));
	die(json_encode(array("status" => 1)));
} else if ($op == 'remove') {
	$id = intval($_GPC['cart_id']);
	pdo_delete($this->t_cart, array('from_user' => $_W['fans']['from_user'], 'uniacid' => $_W['uniacid'], 'id' => $id));
	die(json_encode(array("status" => 1)));
} else if ($op == 'update') {
	$id = intval($_GPC['cart_id']);
	$num = intval($_GPC['quantity']);
	$sql = "update " . tablename($this->t_cart) . " set total=$num where id=:id";
	pdo_query($sql, array(":id" => $id));
	die(json_encode(array("status" => 1)));
}else if($op == 'checkout'){
	$flag = 1;$price = 0;
	foreach ($_GPC['products'] as $key => $value) {
		$product = pdo_fetch("SELECT id,price FROM ".tablename($this->t_product)." WHERE id=:id limit 1",array(":id" => $value['product_id']));
		if (empty($product)) {
			$flag = 0;
			pdo_delete($this->t_cart, array('productid'=>$value['product_id']));
			break;
		}
		$price = $price + $product['price'] * $value['quantity'];
		unset($product);
	}
	if ($_GPC['type'] == 2) {
		$type = 2;
	}else{
		$type = 1;
	}
	if ($flag = 1) {
		$data = array(
			'uniacid' => $_W['uniacid'],
			'from_user' => $_W['fans']['from_user'],
			'type' => $type,
			'ordersn' => date('md') . random(4, 1),
			'price' => $price,
			'status' => 0,
			'createtime' => TIMESTAMP
			);
		pdo_insert($this->t_order, $data);
		$orderid = pdo_insertid();
		foreach ($_GPC['products'] as $key => $value) {
			$product = pdo_fetch("SELECT id,price FROM ".tablename($this->t_product)." WHERE id=:id limit 1",array(":id" => $value['product_id']));
			$insert = array(
				'uniacid' => $_W['uniacid'],
				'orderid' => $orderid,
				'productid' => $value['product_id'],
				'price' => $product['price'],
				'total' => $value['quantity'],
				'createtime' => TIMESTAMP
				);
			pdo_insert($this->t_order_product, $insert);
			unset($insert);unset($product);
			if ($_GPC['from'] == 'cart') {
				pdo_delete($this->t_cart, array("uniacid" => $_W['uniacid'], "from_user" => $_W['fans']['from_user'],'productid'=>$value['product_id']));
			} 
		}
		die(json_encode(array("status" => 1,'orderid'=>$orderid)));
	}else{
		die(json_encode(array("status" => 0,'message' => "检出购物车商品失败")));
	}	
} else {
	$list = pdo_fetchall("SELECT * FROM " . tablename($this->t_cart) . " WHERE  uniacid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}'");
	$totalprice = 0;
	if (!empty($list)) {
		foreach ($list as &$item) {
			$product = pdo_fetch("SELECT  name, thumb, price FROM " . tablename($this->t_product) . " WHERE id=:id limit 1", array(":id" => $item['productid']));
			$item['product'] = $product;
			$item['totalprice'] = (floatval($product['price']) * intval($item['total']));
			$totalprice += $item['totalprice'];
		}
		unset($item);
	}
	$title = "我的购物车";
	include $this->template('cart');
}
?>