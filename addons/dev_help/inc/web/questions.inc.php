<?php
global $_W,$_GPC;
$categories = array('数据库操作','二维码','发送消息','$_W','token','表单','公众号','微信用户','创建URL链接','微信素材');
$res = array();
foreach ($categories as $key1 => $category) {
	$url = 'http://wechat888.cn/category/'.$category;
	$feed = $url.'/feed/';
	$resp = httpModel::http_request($feed);
	$obj = simplexml_load_string($resp, 'SimpleXMLElement', LIBXML_NOCDATA);
	$cat = array();
	$cat['name'] = $category;
	$cat['link'] = $url;
	$i = 0;
	foreach ($obj->channel->item as $key => $article) {
		$i++;
		$wp['title'] = (string)$article->title;
		$wp['link'] = (string)$article->link;
		$cat['article'][] = $wp;
		if($i > 3){
			break;
		}
	}
	$res[] = $cat;
}
class httpModel{
	public static function http_request($url, $data = null)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}

	public static function https_request($url, $data = null)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
}


include $this->template('questions');