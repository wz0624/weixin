<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
if (empty($_W['fans']['nickname'])) {
	mc_oauth_userinfo();
}
$op = $_GPC['op'];
$orderid = intval($_GPC['orderid']);
$order = pdo_fetch("SELECT * FROM " . tablename($this->t_order) . " WHERE uniacid=:uniacid AND id=:orderid AND from_user=:from_user",array(':uniacid'=>$_W['uniacid'],':orderid'=>$orderid,':from_user'=>$_W['openid']));

if ($op == 'date') {
	if (!empty($_GPC['date'])) {
		if ($_GPC['date'] == date('Ymd')) {
			$timestart = strtotime(date('Y-m-d').'10:00');
			$timeend = strtotime(date('Y-m-d').'22:00');
			$j = 0;
			for ($i=$timestart; $i <= $timeend; $i+=1800) { 
				$times[$j]['time'] = date('H:i',$i);
				if (time() > $i - 3600) {
					$times[$j]['enable'] = 0;
				}else{
					$times[$j]['enable'] = 1;
				}
				$j ++;
			}
			//print_r($times);
			die(json_encode(array('status'=>1,'schedule'=>$times)));
		}else{
			die('{"status":1,"schedule":[{"time":"10:00","enable":"1"},{"time":"10:30","enable":"1"},{"time":"11:00","enable":"1"},{"time":"11:30","enable":"1"},{"time":"12:00","enable":"1"},{"time":"12:30","enable":"1"},{"time":"13:00","enable":"1"},{"time":"13:30","enable":"1"},{"time":"14:00","enable":"1"},{"time":"14:30","enable":"1"},{"time":"15:00","enable":"1"},{"time":"15:30","enable":"1"},{"time":"16:00","enable":"1"},{"time":"16:30","enable":"1"},{"time":"17:00","enable":"1"},{"time":"17:30","enable":"1"},{"time":"18:00","enable":"1"},{"time":"18:30","enable":"1"},{"time":"19:00","enable":"1"},{"time":"19:30","enable":"1"},{"time":"20:00","enable":"1"},{"time":"20:30","enable":"1"},{"time":"21:00","enable":"1"},{"time":"21:30","enable":"1"},{"time":"22:00","enable":"1"}]}');
		}
	}
}elseif ($op == 'checktime') {
	if (empty($order)) {
		die(json_encode(array("status" => 0,'message'=>'操作失败，请重新选择产品！')));
	}
	if (time() > strtotime($_GPC['date'] . $_GPC['time']) - 3600) {
		die(json_encode(array("status" => 0,'message'=>'当前时间段不可预约')));
	}else{
		pdo_update($this->t_order, array('date'=>$_GPC['date'],'time'=>$_GPC['time'],'addressid'=>intval($_GPC['addressid'])), array('id'=>$orderid));
		die(json_encode(array("status" => 1)));
	}
}elseif($op == 'chooseshop'){
	if (empty($order)) {
		message('操作失败，请重新选择产品！',referer(),'error');
	}
	$citys = pdo_fetchall("SELECT id,city FROM " . tablename($this->t_shops) . " WHERE uniacid=:uniacid GROUP BY city",array(':uniacid'=>$_W['uniacid']));
	$list = pdo_fetchall("SELECT id,name,thumb_sm,score,tel,address,description FROM " . tablename($this->t_shops) . " WHERE uniacid=:uniacid and enabled=1 ORDER BY displayorder DESC",array(':uniacid'=>$_W['uniacid']));
	if (!empty($list)) {
		foreach ($list as &$value) {
			$html = '';
			for ($i=0; $i < $value['score']; $i++) { 
				$html .= '<i class="fa fa-heart"></i> ';
			}
			$value['url'] = $this->createMobileUrl('shop',array('op'=>'detail', 'shopid'=>$value['id']));
			$value['score_html'] = $html;
		}
	}
	$title="预约店铺";
	include $this->template('chooseshop');
}elseif ($op == 'checkshop') {
	if (empty($order)) {
		die(json_encode(array("status" => 0,'message'=>'操作失败，请重新选择产品！')));
	}
	$shopid = intval($_GPC['shop_id']);
	$shop = pdo_fetch("SELECT id FROM ".tablename($this->t_shops)." WHERE uniacid=:uniacid AND id=:id AND enabled=1",array(':uniacid'=>$_W['uniacid'],':id'=>$shopid));
	if (!empty($shop)) {
		pdo_update($this->t_order, array('shopid'=>$shopid), array('id'=>$orderid));
		die(json_encode(array("status" => 1)));
	}else{
		die(json_encode(array("status" => 0,'message'=>'门店不存在')));
	}
}else{
	if (empty($order)) {
		message('操作失败，请重新选择产品！',referer(),'error');
	}
	$addressid = intval($_GPC['addressid']);
	if (!empty($addressid)) {
		$address = pdo_fetch("SELECT * FROM ".tablename($this->t_address)." WHERE uniacid=:uniacid AND from_user=:from_user AND id=:id",array(':uniacid'=>$_W['uniacid'],':from_user'=>$_W['openid'],':id'=>$addressid));
	}else{
		$address = pdo_fetch("SELECT * FROM ".tablename($this->t_address)." WHERE uniacid=:uniacid AND from_user=:from_user ORDER BY used DESC,id DESC",array(':uniacid'=>$_W['uniacid'],':from_user'=>$_W['openid']));
	}
	$from_url = base64_encode($this->createMobileUrl('appointment',array('orderid'=>$orderid)));
	$title="预约时间";
	include $this->template('appointment');
}

?>