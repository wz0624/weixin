<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
if (empty($_W['fans']['nickname'])) {
	mc_oauth_userinfo();
}
$from_url = base64_decode($_GPC['from_url']);
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$address = pdo_fetchall("SELECT * FROM " . tablename($this->t_address) . " WHERE uniacid = :uniacid AND from_user = :from_user", array(':uniacid' => $_W['uniacid'],':from_user' => $_W['openid']));
	$title = '地址列表';
}elseif ($operation == 'choose') {
	$address = pdo_fetchall("SELECT * FROM " . tablename($this->t_address) . " WHERE uniacid = :uniacid AND from_user = :from_user", array(':uniacid' => $_W['uniacid'],':from_user' => $_W['openid']));
	$title = '选择地址';
}elseif ($operation == 'post') {
	$citys = pdo_fetchall("SELECT id,city FROM " . tablename($this->t_shops) . " WHERE uniacid=:uniacid GROUP BY city",array(':uniacid'=>$_W['uniacid']));
	$addressid = intval($_GPC['addressid']);
	if (!empty($addressid)) {
		$address = pdo_fetch("SELECT * FROM ".tablename($this->t_address)." WHERE uniacid=:uniacid AND from_user=:from_user AND id=:id",array(':uniacid'=>$_W['uniacid'],':from_user' => $_W['openid'],':id'=>$addressid));
		$title = '修改地址';
	}else{
		$title = '新增地址';
	}
}elseif ($operation == 'delete') {
	$addressid = intval($_GPC['addressid']);
	if (!empty($addressid)) {
		$address = pdo_fetch("SELECT * FROM ".tablename($this->t_address)." WHERE uniacid=:uniacid AND from_user=:from_user AND id=:id",array(':uniacid'=>$_W['uniacid'],':from_user' => $_W['openid'],':id'=>$addressid));
		if (!empty($address)) {
			pdo_delete($this->t_address, array('id'=>$addressid));
			die(json_encode(array('status'=>1)));
		}
	}
	die(json_encode(array('status'=>0,'message'=>"删除失败")));
}elseif ($operation == 'add') {
	if (!empty($_GPC['Address'])) {
		$city_id = intval($_GPC['Address']['city_id']);
		$city = pdo_fetch("SELECT city FROM ".tablename($this->t_shops)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$city_id));
		if (!empty($city)) {
			$data = array(
				'uniacid' => $_W['uniacid'],
				'from_user' => $_W['openid'],
				'city' => $city['city'],
				'address' => $_GPC['Address']['address'],
				'lat' => $_GPC['Address']['lat'],
				'lng' => $_GPC['Address']['lng'],
				'room' => $_GPC['Address']['room'],
				'consignee' => $_GPC['Address']['consignee'],
				'mobile' => $_GPC['Address']['mobile'],
				);
			if ($_GPC['Address']['used'] == 1) {
				pdo_update($this->t_address, array('used' => 0), array('uniacid' => $_W['uniacid'], 'from_user' => $_W['openid']));
				$data['used'] = 1;
			}
			if (!empty($_GPC['Address']['id'])) {
				pdo_update($this->t_address, $data,array('id'=>intval($_GPC['Address']['id'])));
				$addressid = intval($_GPC['Address']['id']);
			}else{
				pdo_insert($this->t_address, $data);
				$addressid = pdo_insertid();
			}
			die(json_encode(array('status'=>1,'addressid'=>$addressid)));
		}
	}
	die(json_encode(array('status'=>0,'message'=>"添加失败")));
}elseif ($operation == 'check') {
	$addressid = intval($_GPC['address_id']);
	if (!empty($addressid)) {
		$address = pdo_fetch("SELECT * FROM ".tablename($this->t_address)." WHERE uniacid=:uniacid AND from_user=:from_user AND id=:id",array(':uniacid'=>$_W['uniacid'],':from_user' => $_W['openid'],':id'=>$addressid));
		if (!empty($address)) {
			die(json_encode(array('status'=>1,'addressid'=>$addressid)));
		}
	}
	die(json_encode(array('status'=>0,'message'=>"当前地址不可用")));
}
include $this->template('address');
?>