<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
load()->func('tpl');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$title = "门店查询";
	$config = $this->module['config'];
	$shoplist_thumb = !empty($config['shoplist_thumb']) ? $config['shoplist_thumb'] : './addons/hx_o2o/template/style/images/shop_list.jpg';
	$citys = pdo_fetchall("SELECT id,city FROM " . tablename($this->t_shops) . " WHERE uniacid=:uniacid GROUP BY city",array(':uniacid'=>$_W['uniacid']));
	include $this->template('shop');
}elseif ($operation == 'getlist') {
	$city = $_GPC['city'];
	$list = pdo_fetchall("SELECT id,name,thumb_sm,score,tel,address,description FROM " . tablename($this->t_shops) . " WHERE uniacid=:uniacid and city=:city and enabled=1 ORDER BY displayorder DESC",array(':uniacid'=>$_W['uniacid'],':city'=>$city));
	if (!empty($list)) {
		foreach ($list as &$value) {
			$html = '';
			for ($i=0; $i < $value['score']; $i++) { 
				$html .= '<i class="fa fa-heart"></i> ';
			}
			$value['url'] = $this->createMobileUrl('shop',array('op'=>'detail', 'shopid'=>$value['id']));
			$value['thumb_sm'] = tomedia($value['thumb_sm']);
			$value['score_html'] = $html;
		}
		die(json_encode(array('status'=>1,'data'=>$list)));
	}else{
		die(json_encode(array('status'=>0,'message'=>"当前城市无门店")));
	}
}elseif($operation == 'detail'){
	$shopid = intval($_GPC['shopid']);
	$shop = pdo_fetch("SELECT * FROM ".tablename($this->t_shops)." WHERE uniacid=:uniacid and id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$shopid));
	if (empty($shop)) {
		message('店铺不存在.',$this->createMobileUrl('shop',array('op'=>'display')),'error');
	}
	$title = $shop['name'];
	include $this->template('shop');
}
?>