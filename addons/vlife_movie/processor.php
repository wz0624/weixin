<?php
/**
 * 微生活影讯模块处理程序
 */
defined('IN_IA') or exit('Access Denied');

class Vlife_movieModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W, $_GPC;
		$imgurl = $_W['siteroot']."addons/vlife_movie/include/img";
        $openid = $_W['openid'];
        $weid = $_W['uniacid'];
		$content = $this->message['content'];
		$settings=$this->module['config'];
		$bannerurl = $attachurl.$settings['movie_banner'];
			if(empty($bannerurl)){$bannerurl = $imgurl."/banner.jpg";}
		$city = $settings['movie_city'];
		$api = $settings['movie_api'];
		
 		//这里定义此模块进行消息处理时的具体过程
		$movie_url = "http://op.juhe.cn/onebox/movie/pmovie?key=".$api."&city=".$city;
		
		$result = file_get_contents ( $movie_url );
		$result = json_decode ( $result, true );
		$adurl = $settings['movie_ad'];
			if(empty($adurl)){$adurl = $result ['result']['m_url'];}
			
		foreach ( $result ['result']['data'][0]['data'] as $info ) {
			$PosterUrlList = $info ['iconaddress'];
			if($PosterUrlList){
				$PicUrl = $PosterUrlList;
				}
			if(empty($PicUrl)){
			$PicUrl = MODULE_URL."images/no_pic.jpg";
			}
					$movie [0] = array (
							'title' => $result ['result']['title'],
							'description' => $info ['name'],
							'picurl' => $bannerurl,
							'url' => $adurl
						);			
					
					$movie [] = array (
							'title' => $info ['tvTitle']."\n".$info ['subHead'],
							'description' => $info ['story']['data']['storyBrief'],
							'picurl' => $PicUrl,
							'url' => $info ['more']['data'][4]['link']
						);
					
				}
				//$this->replyNews ( $movie );
				return $this->respNews($movie);
		//这里定义此模块进行消息处理时的具体过程
	}
}