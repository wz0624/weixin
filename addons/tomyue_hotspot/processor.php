<?php
/**
 * 	云热点模块处理程序
 *
 * @author 云热点团队
 * @url http://012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Tomyue_HotspotModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微赞文档来编写你的代码
		$aa = $this->message['from'];
		
		$ak = $this->module['config']['accesskey'];
		$sk = $this->module['config']['secretkey'];
		//$restult = $aa.'用户发送微信wifi请求;ak/sk:'.$ak.'/'.$sk;

		/*$url = "http://test.zjyouth.cn/api";*/
		$url = "http://www.012wz.com/api";
		/*$param = "category=sms&action=send&mobile=$mobile&content=$content&login=testsms&password=smspwd";*/
		$param = "ak=$ak&sk=$sk&openid=".$aa;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$param);
		$ret = curl_exec($ch);
		if(curl_errno($ch)){
			print_r(curl_error($ch));
			/*echo curl_error($ch);*/
		}
		curl_close($ch);
		$user = json_decode($ret); 		
		
		$news = array();

		if($user->status=='false'){
			$news[] = $row = array(
			'picurl'=>$user->picture,
			'url'=>'http://www.baidu.com/',
			'description'	=>$user->result,
			'title'	=>'欢迎您使用微信wifi认证',
			);
		}
		
		if($user->status=='success'){			
			$media = pdo_fetch("SELECT * FROM ".tablename('hotspot_reply')." WHERE uniacid = :uniacid LIMIT 1",array(':uniacid'=>$_W['uniacid']));
			/*$news[] = $row = array(			
				'title'	=>'欢迎您使用微信wifi认证',
				'picurl'=>'http://img5.imgtn.bdimg.com/it/u=1039565320,4275072659&fm=21&gp=0.jpg',
				'url'=>'http://www.baidu.com/',
				'description'	=>"您的验证码是:".$user->randcode."\n"."请点击输入正确的验证码.即可上网!",
				);*/
			$news[] = $row = array(			
				'title'	=>$media['title'],
				'picurl'=>$_W['attachurl'].$media['picture'],
				'url'=>'http://www.baidu.com/?akfrom=wechat&skopenid='.$aa,
				'description'	=>$this->message['nickname']."您的验证码是:".$user->randcode."\n"."请点击输入正确的验证码.即可上网!",
				);			
		}

		if($user->status=='normal'){
			$news[] = $row = array(			
				'title'	=>$media['title'],
				'picurl'=>$_W['attachurl'].$media['picture'],
				'url'=>'http://www.baidu.com/?akfrom=wechat&skopenid='.$aa,
				'description'	=>"您已经登录过了,你之前的验证码是:".$user->randcode."\n"."请点击即可上网!",
				);
		}

		return $this->respNews($news);
	}
}