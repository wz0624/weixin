<?php
/**
 * 	云热点模块处理程序
 *
 * @author 云热点团队
 * @url http://012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Tomyue_HotspotModuleSite extends WeModuleSite {

	private $tb_media = "hotspot_reply";

	
	public function doWebMedia(){

		global $_W, $_GPC;

			$data['accesskey'] = $this->module['config']['accesskey'];
			$data['secretkey'] = $this->module['config']['secretkey'];		
			$data['uid'] = $this->module['config']['uid'];

			$data['salt'] = $this->module['config']['salt'];
			$data['bid'] = $this->module['config']['bid'];


		if(checksubmit()){

			$media = $_GPC['media'];
			
			empty($media['name']) && message('请填写店面名称!');
			empty($media['ssid']) && message('请填写SSID!');
			empty($media['shopid']) && message('请填写shopid!');
			empty($media['appid']) && message('请填写appid!');
			empty($media['secretkey']) && message('请填写secretkey!');
			

			$url = "http://www.012wz.com/engine/init/post";

			
			$data = array_merge($media,$data);
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
			
			
			$ret = json_decode($ret,true);
			if($ret['status']=='success'){
				message('保存成功!','','success');

			}else{
				message('保存失败,请检查通信密钥是否正确!');
			}	
		
			
		}	

		
		header("Content-type: text/html; charset=utf-8");
		$url = "http://www.012wz.com/engine/init/fetch";

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
	
		$media = json_decode($ret,true);
		load()->func('tpl');
		
		include $this->template('media');


	}

}