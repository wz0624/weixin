
<?php
/**
 * 微赞
 *
 */
defined('IN_IA') or exit('Access Denied');

class Stonefish_memberModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		load()->func('communication');
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		load()->func('tpl');
		$mconfig = pdo_fetch("SELECT * FROM ".tablename('stonefish_member_config')." WHERE uniacid = '{$_W['uniacid']}' order by id desc");
		if(empty($mconfig)){
			$mconfig['memberbgcolor'] = '#EBEBEB';
			$mconfig['membercolor'] = '#333333';
			$mconfig['membernavcolor'] = '#999999';
			$mconfig['membernavacolor'] = '#45C017';
			$mconfig['memberiocncolor'] = '#0EADFE';
			$mconfig['memberbntcolor'] = '#5ac5d4';
			$mconfig['memberbnttcolor'] = '#d0f2f7';
			$mconfig['smsstatus'] = '0';
		}
		//查询是否有活动模块权限
		$modules = uni_modules($enabledOnly = true);
		$modules_arr = array();
		$modules_arr = array_reduce($modules, create_function('$v,$w', '$v[$w["mid"]]=$w["name"];return $v;'));
		if(in_array('stonefish_branch',$modules_arr)){
		    $stonefish_branch = true;
		}
		if(in_array('stonefish_shopping',$modules_arr)){
		    $stonefish_shopping = true;
		}		
		//查询是否有活动模块权限
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
			$dat = array(
                'module_branch'   => $_GPC['module_branch'],
				'module_shopping' => $_GPC['module_shopping'],
				'mc_activity'     => $_GPC['mc_activity'],
				'mc_card'         => $_GPC['mc_card'],
				'mc_pay'          => $_GPC['mc_pay'],
				'smsverification' => $_GPC['smsverification'],
            );			
			$this->saveSettings($dat);
			//保存会员界面配置
			$data = array(
				'homebg' => $_GPC['homebg'],
				'memberbgcolor' => $_GPC['memberbgcolor'],
				'membercolor' => $_GPC['membercolor'],
				'membernavcolor' => $_GPC['membernavcolor'],
				'membernavacolor' => $_GPC['membernavacolor'],
				'memberiocncolor' => $_GPC['memberiocncolor'],
				'memberbntcolor' => $_GPC['memberbntcolor'],
				'memberbnttcolor' => $_GPC['memberbnttcolor'],
				'sharetitle' => $_GPC['sharetitle'],
				'sharepic' => $_GPC['sharepic'],
				'sharedesc' => $_GPC['sharedesc'],
				'shareurl' => $_GPC['shareurl'],
				'smsstatus' => $_GPC['smsstatus'],
			);
				if (!empty($mconfig['id'])) {
				    pdo_update('stonefish_member_config', $data, array('uniacid' => $_W['uniacid']));
			    } else {
				    $data['uniacid'] = $_W['uniacid'];
				    pdo_insert('stonefish_member_config', $data);
			    }

			//保存会员界面配置
			message('配置参数更新成功！', referer(), 'success');
		}
		//这里来展示设置项表单		
		include $this->template('settings');
	}

}
?>