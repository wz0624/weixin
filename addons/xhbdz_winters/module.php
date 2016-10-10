<?php
/**
 * 红包大战模块定义
 *

 * @author Weizan Inc.

 */
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Xhbdz_WintersModule extends WeModule
{
public function settingsDisplay($settings) {
		
		// 声明为全局才可以访问到.
		global $_W, $_GPC;
		
		if ($_GPC['op'] == 'qrtru'){
		    if(!pdo_query('TRUNCATE TABLE `ims_xhbdz_poster`')){	        
		           message('清除海报缓存成功','','success');
    		    }else {
    		        message('清除海报缓存失败','','error');
    		    }
		}
		
		if(checksubmit()) {
			
			// $_GPC 可以用来获取 Cookies,表单中以及地址栏参数
			$data = $_GPC['data'];
			
			// message() 方法用于提示用户操作提示
			empty($data['name']) && message('平台名称');
			empty($data['logo']) && message('未填写首页轮播');
			
			//字段验证, 并获得正确的数据$dat
			if (!$this->saveSettings($data)) {
				message('保存信息失败','','error');
			} else {
				message('保存信息成功','','success');
			}
		}
		
		// 模板中需要用到 "tpl" 表单控件函数的话, 记得一定要调用此方法.
		load()->func('tpl');
		
		//这里来展示设置项表单
		include $this->template('setting');
	}

    
}