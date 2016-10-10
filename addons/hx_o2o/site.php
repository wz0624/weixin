<?php
/**
 * O2O预约模块微站定义
 *
 * @author 华轩科技
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
define('ROOT_PATH', str_replace('site.php', '', str_replace('\\', '/', __FILE__)));
define('INC_PATH',ROOT_PATH.'inc/');
define('CSS_PATH', '../addons/hx_o2o/template/style/css/');
define('JS_PATH', '../addons/hx_o2o/template/style/js/');
define('IMG_PATH', '../addons/hx_o2o/template/style/images/');
class Hx_o2oModuleSite extends WeModuleSite {
	public $t_category = 'hx_o2o_category';
	public $t_shops = 'hx_o2o_shops';
	public $t_order = 'hx_o2o_order';
	public $t_order_product = 'hx_o2o_order_product';
	public $t_product = 'hx_o2o_products';
	public $t_cart = 'hx_o2o_cart';
	public $t_address = 'hx_o2o_address';
	public function __construct(){

	}
	public function getCartTotal() {
		global $_W;
		$cartotal = pdo_fetchcolumn("select sum(total) from " . tablename($this->t_cart) . " where uniacid = '{$_W['uniacid']}' and from_user='{$_W['fans']['from_user']}'");
		return empty($cartotal) ? 0 : $cartotal;
	}

	public function payResult($params) {
		global $_W;

		$fee = intval($params['fee']);
		$data = array('status' => $params['result'] == 'success' ? 1 : 0);
		$paytype = array('credit' => '1', 'wechat' => '2', 'alipay' => '2', 'delivery' => '3');

		// 卡券代金券备注
		if (!empty($params['is_usecard'])) {
			$cardType = array('1' => '微信卡券', '2' => '系统代金券');
			$data['paydetail'] = '使用' . $cardType[$params['card_type']] . '支付了' . ($params['fee'] - $params['card_fee']);
			$data['paydetail'] .= '元，实际支付了' . $params['card_fee'] . '元。';
		}

		$data['paytype'] = $paytype[$params['type']];
		if ($params['type'] == 'wechat') {
			$data['transid'] = $params['tag']['transaction_id'];
		}
		if ($params['type'] == 'delivery') {
			$data['status'] = 1;
		}
		$order = pdo_fetch("SELECT * FROM " . tablename($this->t_order) . " WHERE id = '{$params['tid']}'");
		$order_product = pdo_fetch("SELECT productid FROM ".tablename($this->t_order_product)." WHERE orderid=:orderid",array(':orderid'=>$order['id']));
		$order_product_info = pdo_fetch("SELECT * FROM ".tablename($this->t_product)." WHERE id=:id",array(':id'=>$order_product['productid']));
		pdo_update($this->t_order, $data, array('id' => $params['tid']));
		$order_address = pdo_fetch("SELECT * FROM ".tablename($this->t_address)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$order['addressid']));
		$order_shop = pdo_fetch("SELECT * FROM ".tablename($this->t_shops)." WHERE uniacid=:uniacid AND id=:id",array(':uniacid'=>$_W['uniacid'],':id'=>$order['shopid']));
		$settings = $this->module['config'];
		if (!empty($settings['kfid']) && !empty($settings['k_templateid'])) {
			$kfirst = empty($settings['kfirst']) ? '您有一个新的预约订单' : $settings['kfirst'];
			$kfoot = empty($settings['kfoot']) ? '请及时处理，点击可查看详情' : $settings['kfoot'];
			$kurl = $_W['siteroot'] . 'app' . str_replace('./', '/', $this->createMobileUrl('orderlist',array('op'=>'detailadmin','orderid'=>$params['tid'])));
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
					'value' => $params['type'] == 3 ? '到店支付' : '在线支付',
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
				'keyword4' => array(//测速
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
		if ($params['from'] == 'return') {
			if ($params['type'] == $credit) {
				message('支付成功！', $this->createMobileUrl('orderlist',array('op'=>'detail','orderid'=>$params['tid'])), 'success');
			} else {
				message('支付成功！', '../../app/' . $this->createMobileUrl('orderlist',array('op'=>'detail','orderid'=>$params['tid'])), 'success');
			}
		}
	}
}