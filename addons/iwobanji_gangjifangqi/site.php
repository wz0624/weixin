<?php
/**
 * 官方示例模块微站定义
 *
 * @author 微赞团队
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Iwobanji_GangjifangqiModuleSite extends WeModuleSite {
	public $modulename = 'gangjifangqi';
    public $adtable='iwobanji_gangjifangqi';
	public function doWebManage(){
		global $_W,$_GPC;
		$weid = $_W['uniacid'];
		$adinfo=pdo_fetch('select * from '.tablename($this->adtable)." where weid={$weid}");		
		$syserPic=$_W['attachdir'].'qrcode_'.$weid.'.jpg';
		$data['url']=empty($adinfo['url'])?'http://7tea6c.com1.z0.glb.clouddn.com/qrcode_1.jpg':$adinfo['url'];
		$data['copyright']=empty($adinfo['copyright'])?'© 2010-2013 爱我班级 版权所有':$adinfo['copyright'];;
		$data['info']=empty($adinfo['info'])?'1、实力：象>狮>虎>狼>狗>猫>鼠（象可被鼠消灭）；2、玩法：点击移动红色棋子，每次按线点只能移一步；3、输赢：蓝（红）方全部棋子消灭红（蓝）方赢。红方20步内双方均无吃子红方输。':$adinfo['info'];
		$data['title']=empty($adinfo['title'])?'爱我班级赣极方棋':$adinfo['title'];
        $data['wxh']=empty($adinfo['wxh'])?'微信资料中查询，由一组字母组成':$adinfo['wxh'];
        $data['wxm']=empty($adinfo['wxm'])?'微信资料中查询，由一组汉字组成':$adinfo['wxm'];				
        $data['class']=empty($adinfo['class'])?'例如:高二（3）班':$adinfo['class'];
        $data['classkouling']=empty($adinfo['classkouling'])?' ':$adinfo['classkouling'];
        $data['classslogan']=empty($adinfo['classslogan'])?' ':$adinfo['classslogan'];
		$data['background_img']=empty($adinfo['background_img'])?'http://7tea6c.com1.z0.glb.clouddn.com/bjt.jpg':$adinfo['background_img'];
		$data['group_photo']=empty($adinfo['group_photo'])?'http://7tea6c.com1.z0.glb.clouddn.com/bjhy.jpg':$adinfo['group_photo'];
		if (checksubmit('submit')) {
			if (empty($_GPC['title'])) {
				message('请输入标题！');
			}
			$mData=array(
					'weid'=>$weid,
					'url' => $_GPC['url'],
					'copyright' => $_GPC['copyright'],						
					'info' => $_GPC['info'],
					'wxh' => $_GPC['wxh'],
					'wxm' => $_GPC['wxm'],
				    'class' => $_GPC['class'],
					'classkouling' => $_GPC['classkouling'],
					'classslogan' => $_GPC['classslogan'],
				    'background_img' => $_GPC['background_img'],
				    'group_photo' => $_GPC['group_photo'],
					'title' => $_GPC['title']						
			);		
			if (empty($adinfo))
			{
				pdo_insert($this->adtable, $mData);
			} else {
				pdo_update($this->adtable, $mData, array('id' => $adinfo['id']));
			}			
			message('信息更新成功！', $this->createWebUrl('manage'), 'success');
		}
		$previewUrl=$_W['siteroot'].'app/'.$this->createMobileUrl('manage');
		load()->func('tpl');
		include $this->template('manage');		
	}
	public function doMobileIndex(){
		global $_W,$_GPC;
		$weid = $_W['uniacid'];		
		$info=pdo_fetch('select * from '.tablename($this->adtable)." where weid={$weid}");
		include $this->template('index');
		
	}

	public function doMobilePay() {
		global $_W, $_GPC;
		//验证用户登录状态，此处测试不做验证
		checkauth();		
		$params['tid'] = date('YmdH');
		$params['user'] = $_W['member']['uid'];
		$params['fee'] = floatval($_GPC['price']);
		$params['title'] = '测试支付公众号名称';
		$params['ordersn'] = random(5,1);
		$params['virtual'] = false;
		
		if (checksubmit('submit')) {
			if ($_GPC['type'] == 'credit') {
				$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
				$credtis = mc_credit_fetch($_W['member']['uid']);
				//此处需要验证积分数量
				if ($credtis[$setting['creditbehaviors']['currency']] < $params['fee']) {
					message('抱歉，您帐户的余额不够支付该订单，请充值！', '', 'error');
				}
			}
		} else {
			$this->pay($params);
		}
	}
	
	/**
	 * 支付完成后更改业务状态
	 */
	public function payResult($params) {
		/*
		 * $params 结构
		 * 
		 * weid 公众号id 兼容低版本
		 * uniacid 公众号id
		 * result 支付是否成功 failed/success
		 * type 支付类型 credit 积分支付 alipay 支付宝支付 wechat 微信支付  delivery 货到付款
		 * tid 订单号
		 * user 用户id
		 * fee 支付金额
		 * 
		 * 注意：货到付款会直接返回支付失败，请在订单中记录货到付款的订单。然后发货后收取货款
		 */
		$fee = intval($params['fee']);
		$data = array('status' => $params['result'] == 'success' ? 1 : 0);
		//如果是微信支付，需要记录transaction_id。
		if ($params['type'] == 'wechat') {
			$data['transid'] = $params['tag']['transaction_id'];
		}
		//此处更改业务方面的记录，例如把订单状态更改为已付款
		//pdo_update('shopping_order', $data, array('id' => $params['tid']));
		
		//如果消息是用户直接返回（非通知），则提示一个付款成功
		if ($params['from'] == 'return') {
			if ($params['type'] == 'credit') {
				message('支付成功！', $this->createMobileUrl('index1'), 'success');
			} elseif ($params['type'] == 'delivery') {
				message('请您在收到货物时付清货款！', $this->createMobileUrl('index1'), 'success');
			} else {
				message('支付成功！', '../../' . $this->createMobileUrl('index1'), 'success');
			}
		}
	}


}