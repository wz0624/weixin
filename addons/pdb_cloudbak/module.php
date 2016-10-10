<?php
/**
 * 云备份模块定义
 *
 * @author PHP大巴
 * @url http://www.phpdb.net/
 */
defined('IN_IA') or exit('Access Denied');
require_once (__DIR__ . '/kuaipan/core.php');

class Pdb_cloudbakModule extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
	}

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			// print_r($_POST);exit;
			//字段验证, 并获得正确的数据$dat
			
			$data = array();
			$data['consumer_key'] = trim($_GPC['consumer_key']);
			$data['consumer_secret'] = trim($_GPC['consumer_secret']);
			$data['ext_dirs'] = trim($_GPC['ext_dirs']);
			$data['ext_files'] = trim($_GPC['ext_files']);
			$data['host'] = trim($_GPC['host']);
			$data['user'] = trim($_GPC['user']);
			$data['password'] = trim($_GPC['password']);
			$data['dbname'] = trim($_GPC['dbname']);
			$data['auth_code'] = trim($_GPC['auth_code']);
			// print_r($data);exit;
			$this->saveSettings($data);
			
			/* $is_auth_msg = '';
			if ($_GPC['auth_code']){
				$rs = sendAuthcode();
				// print_r($rs);exit;
				if ($rs->is_block == 1){
					 message($rs->msg, 'refresh','error');
				}
				
				if ($rs->is_auth == 1){
					$is_auth_msg = '，该域名已经授权成功！';
				}
				
			} */
			
			 message('设置保存成功'.$is_auth_msg, 'refresh');
		}
		
		// print_r($settings);exit;
		if (!$settings){
			//设定默认值：
			// $settings['host'] = 'localhost:3306';
			// $settings['user'] = 'root';
			$settings['ext_dirs'] = 'tmp,bak,';
			$settings['ext_files'] = 'zip,rar,tmp,';
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}

}