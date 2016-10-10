<?php
/**
 * 红包墙模块定义
 *
 * @author gl5512968
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Gandl_wallModule extends WeModule {
	
	/**
	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
			$this->saveSettings($dat);
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}
	**/


	/**
    public function fieldsFormDisplay($rid = 0) {
        global $_W;
        $setting = $_W['account']['modules'][$this->_saveing_params['mid']]['config'];
        include $this->template('rule');
    }

    public function fieldsFormSubmit($rid = 0) {
        global $_GPC, $_W;
        if (!empty($_GPC['title'])) {
            $data = array(
                'title' => $_GPC['title'],
                'description' => $_GPC['description'],
                'picurl' => $_GPC['thumb-old'],
                'url' => create_url('mobile/module/list', array('name' => 'shopping', 'weid' => $_W['weid'])),
            );
            if (!empty($_GPC['thumb'])) {
                $data['picurl'] = $_GPC['thumb'];
                file_delete($_GPC['thumb-old']);
            }
            $this->saveSettings($data);
        }
        return true;
    }
	**/

    public function settingsDisplay($settings) {
        global $_GPC,$_FILES, $_W;
        
        if (checksubmit()) {
            $cfg = array(
                'mchid' => $_GPC['mchid'],
                'appid' => $_GPC['appid'],
                'key' => $_GPC['key'],
                'ip' => $_GPC['ip'],
                'bd_ak' => $_GPC['bd_ak'],
				'qn_ak' => $_GPC['qn_ak'],
				'qn_sk' => $_GPC['qn_sk'],
				'qn_bucket' => $_GPC['qn_bucket'],
				'qn_api' => $_GPC['qn_api']
            );

			// 判断是否有上传
			load()->func('file');
			if(!empty($_FILES['cert_rootca']['tmp_name'])){
				$cert_rootca=file_upload($_FILES['cert_rootca']);
				$cfg['cert_rootca']=$cert_rootca;
			}else{
				$cfg['cert_rootca']=$settings['cert_rootca'];
			}
			if(!empty($_FILES['cert_cert']['tmp_name'])){
				$cert_cert=file_upload($_FILES['cert_cert']);
				$cfg['cert_cert']=$cert_cert;
			}else{
				$cfg['cert_cert']=$settings['cert_cert'];
			}
			if(!empty($_FILES['cert_key']['tmp_name'])){
				$cert_key=file_upload($_FILES['cert_key']);
				$cfg['cert_key']=$cert_key;
			}else{
				$cfg['cert_key']=$settings['cert_key'];
			}


            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
		
        load()->func('tpl');
		include $this->template('setting');
    }





}