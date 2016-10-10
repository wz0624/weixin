<?php
/**
 * 	云热点模块处理程序
 *
 * @author 云热点团队
 * @url http://012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Tomyue_HotspotModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W, $_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit('submit')) {
			//字段验证, 并获得正确的数据$dat
			$data = $_GPC['config'];
		
			empty($data['accesskey']) && message('请填写AccessKey');
			empty($data['secretkey']) && message('请填写SecretKey');
			empty($data['salt']) && message('请填写节点密钥');
			//Get uid Here

			
			header("Content-type: text/html; charset=utf-8");
			$url = "http://www.012wz.com/engine/init/we7";
			$user = $data;
			
			$user = json_encode($data);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,'user='.$user);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$ret = curl_exec($ch);
			if (curl_errno($ch)) {
			 return curl_error($ch);
			}
		
			$user = json_decode($ret,true);

			if($user['status']=='false') message('无效的AccessKey,SecretKey或节点密钥.');
			$data['uid'] = $user['uid'];	
			$data['bid'] = $user['bid'];	
			$this->saveSettings($data);
			message('配置参数更新成功！', referer(), 'success');
		}
		//这里来展示设置项表单
		include $this->template('settings');
	}

}