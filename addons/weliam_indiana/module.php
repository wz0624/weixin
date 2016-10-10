<?php
/**
 * 一元夺宝模块定义
 *
 */
defined('IN_IA') or exit('Access Denied');
require IA_ROOT. '/addons/weliam_indiana/defines.php';
class weliam_indianaModule extends WeModule {
	public function settingsDisplay($settings) {
		// 声明为全局才可以访问到.
		global $_W, $_GPC;
		$styles = array();
		$dir = IA_ROOT . "/addons/weliam_indiana/template/mobile/";
		if ($handle = opendir($dir)) {
			while (($file = readdir($handle)) !== false) {
				if ($file != ".." && $file != ".") {
					if (is_dir($dir . "/" . $file)) {
						$styles[] = $file;
					} 
				} 
			} 
			closedir($handle);
		}
		if(checksubmit()) {
			// $_GPC 可以用来获取 Cookies,表单中以及地址栏参数
			$dat = array(
                'share_title' => $_GPC['share_title'],
                'share_image' => $_GPC['share_image'],
                'share_desc' => $_GPC['share_desc'],
                
                'm_pay'=>$_GPC['m_pay'],
                'm_send'=>$_GPC['m_send'],
                'm_suc'=>$_GPC['m_suc'],
                'm_ref'=>$_GPC['m_ref'],
                'm_money'=>$_GPC['m_money'],
                
				'style'=>$_GPC['style'],
                'sname'=>$_GPC['sname'],
                'copyright'=>$_GPC['copyright'],
                'instruction'=>$_GPC['instruction'],
                'content' => htmlspecialchars_decode($_GPC['content']),
                //返利
            	'level'=>$_GPC['level'],
            	'level1'=>$_GPC['level1'],
            	'level2'=>$_GPC['level2'],
            	'level3'=>$_GPC['level3'],
            	'credit1'=>$_GPC['credit1'],
            	'credit2'=>$_GPC['credit2'],
            	'creditstatus' => $_GPC['creditstatus'],
            	'invitepicarr' => $_GPC['invitepicarr'],
            	
				//ping++支付参数
				'paytype' => $_GPC['paytype'],
				'App_ID' => $_GPC['App_ID'],
				'Secret_Key' => $_GPC['Secret_Key'],
				'Publishable_Key' => $_GPC['Publishable_Key'],
				'PUBLIC_KEY' => $_GPC['PUBLIC_KEY'],
				'isalipay' => $_GPC['isalipay'],
				'iswxpay' => $_GPC['iswxpay'],
				'isjdpay' => $_GPC['isjdpay'],
				'isbfbpay' => $_GPC['isbfbpay'],
				'ispayee' => $_GPC['ispayee'],
				
				//关注参数设置
				'followed_image'=>$_GPC['followed_image'],
				'followed_isbuy'=>$_GPC['followed_isbuy'],
				'credit1_followed'=>$_GPC['credit1_followed'],
				'iscredit1_followed'=>$_GPC['iscredit1_followed'],
				'duobao_followed'=>$_GPC['duobao_followed'],
				'followed_message'=>$_GPC['followed_message'],
				'buy_followed'=>!empty($_GPC['buy_followed'])?$_GPC['buy_followed']:0, 		//购买返积分
				
				//云支付
				'wxpaystatus' => $_GPC['wxpaystatus'],
				'yunpay_partner' => $_GPC['yunpay_partner'],
				'yunpay_key' => $_GPC['yunpay_key'],
				'yunpay_seller_email' => $_GPC['yunpay_seller_email'],
				
				//默认购买显示次数
				'showtype' => !empty($_GPC['showtype'])?$_GPC['showtype']:1,

				//判断是否进行夺宝码重新分配后的更新
				'isdb_change' => !empty($_GPC['isdb_change'])?$_GPC['isdb_change']:0,
			);
			$paydata['yunpay']=array(
				//云支付
				'switch' => 1,
				'partner' => $_GPC['yunpay_partner'],
				'key' => $_GPC['yunpay_key'],
				'seller_email' => $_GPC['yunpay_seller_email'],
			);
			if (!$this->saveSettings($dat)) {
				message('保存信息失败','referer','error');
			} else {
				$settings = uni_setting($_W['uniacid'], array('payment'));			
				if(!in_array($paydata,$settings)){
					$settings['payment']['yunpay']= $paydata['yunpay'];
					$data = iserializer($settings['payment']);		
					pdo_update('uni_settings', array('payment' => $data), array('uniacid' => $_W['uniacid']));
				}
				message('保存信息成功','referer','success');
			}
		}
		
		// 模板中需要用到 "tpl" 表单控件函数的话, 记得一定要调用此方法.
		load()->func('tpl');
		
		//这里来展示设置项表单
		include $this->template('setting');
	}

}