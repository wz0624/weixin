<?php
/**
 * 微外卖模块微站定义
 * @author strday
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
global $_W, $_GPC;
$store = checkstore();
$sid = $store['id'];
$do = 'reserve';
$op = trim($_GPC['op']) ? trim($_GPC['op']) : 'index';
$title = '预定';
checkauth();
$store = get_store($sid, array('is_reserve'));

if($store['is_reserve'] == 2) {
	message('商家已经关闭预定功能', $this->createMobileUrl('store', array('sid' => $sid)), 'error');
}
if($op == 'index') {
	$categorys = pdo_fetchall('select * from ' . tablename('str_tables_category') . ' where uniacid = :uniacid and sid = :sid', array(':uniacid' => $_W['uniacid'], ':sid' => $sid));
	$data = pdo_getall('str_reserve', array('uniacid' => $_W['uniacid'], 'sid' => $sid));
	if(!empty($data)) {
		$reserves = array();
		foreach($data as $da) {
			$reserves[$da['table_cid']][] = $da['time'];
		}
	}
	include $this->template('reserve');
}

if($op == 'post') {
	$cid = intval($_GPC['cid']);
	$category = pdo_get('str_tables_category', array('uniacid' => $_W['uniacid'], 'id' => $cid));
	if(empty($category)) {
		message('桌台类型不存在', $this->createMobileUrl('store', array('sid' => $sid)), 'error');
	}
	$date = trim($_GPC['date']);
	$time = trim($_GPC['time']);
	$cart = get_order_cart($sid);
	if($_GPC['f'] == 'dish') {
		//从菜品页面跳转过来
		$cart = set_order_cart($sid);
	}
	include $this->template('reserve');
}

if($op == 'order') {
	if(!$_W['isajax']) {
		message(error(-1, '非法访问'), '', 'ajax');
	}
	$data['uniacid'] = $_W['uniacid'];
	$data['sid'] = $sid;
	$data['uid'] = $_W['member']['uid'];
	$data['groupid'] = $_W['member']['groupid'];
	$data['openid'] = $_W['openid'];
	$data['order_type'] = intval($_GPC['type']); //3:只订座, 4:提前点餐

	$data['mobile'] = trim($_GPC['mobile']);
	$data['username'] = trim($_GPC['realname']);
	$data['note'] = trim($_GPC['note']);
	$data['pay_type'] = '';
	$cart = get_order_cart($sid);
	if($cart['num'] == 0 && $data['order_type'] == 4) {
		message(error(-1, '菜品为空'), '', 'ajax');
	}
	$data['num'] = $cart['num'];
	$data['price'] = $cart['price'];
	$data['card_fee'] = $cart['price'];
	$data['groupid'] = $cart['groupid'];
	$data['addtime'] = TIMESTAMP;
	$data['status'] = 1;
	$data['is_notice'] = 0;
	$data['grant_credit'] = $cart['grant_credit'];;
	$data['is_grant'] = 0;
	$data['reserve_time'] = strtotime($_GPC['date'] . ' ' . $_GPC['time']);
	$data['table_id'] = intval($_GPC['cid']);
	if($data['table_id'] > 0 && $data['order_type'] == 3) {
		$table_cate = pdo_get('str_tables_category', array('uniacid' => $_W['uniacid'], 'sid' => $sid, 'id' => $data['table_id']));
		$data['price'] = $table_cate['reservation_price'];
	}
	pdo_insert('str_order', $data);
	$id = pdo_insertid();
	set_order_log($id, $sid, '用户提交订单');

	set_order_user($sid, $mobile, $realname);
	if(!empty($cart['data'])) {
		$ids_str = implode(',', array_keys($cart['data']));
		$dish_info = pdo_fetchall('SELECT id,title,price,grant_credit,total FROM ' . tablename('str_dish') ." WHERE uniacid = :aid AND sid = :sid AND id IN ($ids_str)", array(':aid' => $_W['uniacid'], ':sid' => $sid), 'id');
		foreach($cart['data'] as $k => $v) {
			$k = intval($k);
			$v = intval($v);
			pdo_query('UPDATE ' . tablename('str_dish') . " set sailed = sailed + {$v} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $k));
			//更新库存
			if($dish_info[$k]['total'] != -1 && $dish_info[$k]['total'] > 0) {
				pdo_query('UPDATE ' . tablename('str_dish') . " set total = total - {$v} WHERE uniacid = :aid AND id = :id", array(':aid' => $_W['uniacid'], ':id' => $k));
			}
			$stat = array();
			if($k && $v) {
				$stat['oid'] = $id;
				$stat['uniacid'] = $_W['uniacid'];
				$stat['sid'] = $sid;
				$stat['dish_id'] = $k;
				$stat['dish_num'] = $v;
				$stat['dish_title'] = $dish_info[$k]['title'];
				$stat['dish_price'] = ($v * dish_group_price($dish_info[$k]['price']));
				$stat['addtime'] = TIMESTAMP;
				pdo_insert('str_stat', $stat);
			}
		}
	}
	//是否打印订单
	init_print_order($sid, $id, 'order');
	del_order_cart($sid);
	message(error(0, $id), '', 'ajax');
}

