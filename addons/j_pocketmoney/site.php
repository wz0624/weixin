<?php
/**
 * 捷讯派红包模块微站定义
 *
 * @author 捷讯设计
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class J_pocketmoneyModuleSite extends WeModuleSite {
	
	public function doMobileAjax(){
		global $_W,$_GPC;
		if(!$_W['isajax'])die(json_encode(array('success'=>false,'msg'=>'无法获取系统信息,请重新打开再尝试')));
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if($operation=='getlocation'){
			$location=$_GPC['latLng'];
			$target=explode(";",$_GPC['target']);
			$key="V7IBZ-W6T3F-MPCJO-JFIJN-EWVFZ-FPF4K";
			$url="http://apis.map.qq.com/ws/geocoder/v1/?location=".$location."&key=".$key."&get_poi=0";
			load()->func('communication');
			$result = file_get_contents($url);
			$content = json_decode($result,true);
			if ($content['status'] == 0) { //获取成功
				$address = $content['result']['address_component'];
				$data[0]=$address['province'];
				$data[1]=$address['city'];
				$data[2]=$address['district'];
				$isok=true;
				for($i=0;$i<count($target);$i++){
					if($target[$i]!=$data[$i]){
						$isok=false;
						break;
					}
				}
				die(json_encode(array('success'=>$isok,'msg'=>"抱歉，活动只允许[".implode("-",$target)."人]参加哦!")));
			}
			die(json_encode(array('success'=>false,'msg'=>'无法获取系统信息,请重新打开再尝试')));
		}
		if($operation=='getmoreuser'){
			$rid=intval($_GPC['rid']);
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('j_pocketmoney_records')." WHERE rid = '$rid' and weid='{$_W['uniacid']}'");
			$allpage= $total % $psize==0 ? $total / $psize :($total/$psize)+1;
			if($pindex>$allpage)die(json_encode(array('success'=>true,'item'=>$ary)));
			
			$sql = "SELECT * FROM ".tablename('j_pocketmoney_records')."  WHERE rid = '$rid' and weid='{$_W['uniacid']}' order by id desc LIMIT ".($pindex - 1) * $psize.",{$psize}";
			$list = pdo_fetchall($sql);
			$ary=array();
			foreach($list as $row){
				$ary[]=array(
					'headimgurl'=>$row['headimgurl'],
					'nickname'=>$row['nickname'],
					'created'=> date('m/d H:i',$row['created']),
					'kouhao'=>$row['kouhao'],
					'fee'=>sprintf('%.2f', $row['fee']/100),
				);
			}
			
			die(json_encode(array('success'=>true,'item'=>$ary)));
		}
	}
	public function doMobileOauth(){
		global $_W,$_GPC;
 		$code = $_GPC['code'];
		load()->func('communication');
		$r_id=intval($_GPC['r_id']);
		$reply=pdo_fetch('select * from '.tablename('j_pocketmoney_reply').' where rid=:rid order BY id DESC LIMIT 1',array(':rid'=>$r_id));
		if(!empty($code)) {
			$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$reply['appid']."&secret=".$reply['secret']."&code={$code}&grant_type=authorization_code";
			$ret = ihttp_get($url);
			if(!is_error($ret)) {
				$auth = @json_decode($ret['content'], true);
				if(is_array($auth) && !empty($auth['openid'])) {
					$url='https://api.weixin.qq.com/sns/userinfo?access_token='.$auth['access_token'].'&openid='.$auth['openid'].'&lang=zh_CN';
					$ret = ihttp_get($url);
					$auth = @json_decode($ret['content'], true);
					$insert=array(
							'weid'=>$_W['uniacid'],
							'openid'=>$auth['openid'],
							'nickname'=>$auth['nickname'],
							'language'=>$auth['language'],
							'sex'=>$auth['sex'],
							'headimgurl'=>$auth['headimgurl'],
							'country'=>$auth['country'],
							'province'=>$auth['province'],
							'city'=>$auth['city'],
							'remark'=>$auth['remark'],
							'rid'=>$r_id,
						);
					$fans=pdo_fetch('select * from '.tablename('j_pocketmoney_fans').' where rid=:rid AND  openid=:openid',array(':rid'=>$r_id,':openid'=>$auth['openid']));
					if($fans==false){
						$insert['upid']=$_GPC['user_fid'];//这里录入上下级关系
						if($_W['account']['key']==$reply['appid'])$insert['from_user']=$auth['openid'];
						$insert['subscribe']=1;
						$insert['vnums']=1;
						$insert['subscribe_time']=time();
						pdo_insert('j_pocketmoney_fans',$insert);
						
						//增加下线的，增加一个链接，这里多一个
						if(!empty($_GPC['user_fid'])){
							pdo_query('update '.tablename('j_pocketmoney_fans').' set unums=unums+1 where id='.$_GPC['user_fid']);
						}
					}else{
						$insert['from_user']=$fans['from_user'];
						$insert['subscribe']=1;
						$insert['subscribe_time']=time();
						pdo_update('j_pocketmoney_fans',$insert,array('id'=>$fans['id']));						
					}
					isetcookie('ssc_openid'.$r_id, $auth['openid'], 30 * 86400);
					$forward = $_W['siteroot'].$this->createMobileurl('index',array('r_id'=>$r_id));
					header('location: ' .$forward);
					exit;
				}else{
					die('微信 授权失败');
				}
			}else{
				die('微信授权失败');
			}
		}else{
			$forward = $_W['siteroot'].$this->createMobileurl('index',array('r_id'=>$r_id));
			header('location: ' .$forward);
			exit;
		}
	}
	
	public function doMobileIndex() {
		global $_W,$_GPC;
		$cfg = $this->module['config'];
		if(isset($_GPC['r'])){
			$r=intval($_GPC['r']);
			if(TIMESTAMP-$r>(60*$cfg['key_wordtime']) && $cfg['key_wordtime'])message('链接已失效，请重新触发进入哦');
		}
		$r_id=intval($_GPC['r_id']);
		
		if(!$r_id)message('非法进入，请重新在页面触发进入');
		if($_GET['r_id']!=$_GPC['r_id'] && $_GET['r_id'])$r_id=$_GET['r_id'];
		isetcookie('r_id', $r_id, 600);
		
		$reply=pdo_fetch('select * from '.tablename('j_pocketmoney_reply').' where rid=:rid order BY id DESC LIMIT 1',array(':rid'=>$r_id));
		if(empty($reply))message('活动不存在或已删除！');
		if($reply['gametype']==1){
			$this->doMobileShake();
			exit();
		}
		if($reply['gametype']==2){
			$this->doMobileIndexc();
			exit();
		}
		if($reply['comefrom']){
			if(!$_SERVER['HTTP_REFERER'])message('非法进入');
			if($reply['comefrom']!=$_SERVER['HTTP_REFERER']){
				if(!strpos(strtolower('m='.$reply['comefrom']),strtolower($_SERVER['HTTP_REFERER']))){
					message('非法进入');
				}
			}
		}
		if(TIMESTAMP<$reply['starttime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['starttime']).'开始,到时再来哦';
			message($msg,$reply['smurl'], 'success');
		}
		if(TIMESTAMP>$reply['endtime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['endtime']).'结束啦,下周再来吧';
			message($msg,$reply['smurl'], 'success');
		}
		
		if($reply['remainfee']<=0){
			message("红包已经抢玩了哦~下次记得早点出手哦！",$reply['smurl'], 'success');
		}
		if(!empty($_GPC['fid'])){
			//这里是获取上级，这里是查看浏览量，记录上下级关系
 			pdo_query('update '.tablename('j_pocketmoney_fans').' set vnums=vnums+1 where id='.$_GPC['fid']);
			isetcookie('user_fid', $_GPC['fid'], 30 * 86400);
			$_GPC['user_fid']=$_GPC['fid'];
 		}
		//授权不存在,其他的如果不是这个入口，没有ssc_openid一律错误
		if(empty($_GPC['ssc_openid'.$r_id])){
			if(empty($_GPC['openid'])){
				$callback = urlencode($_W['siteroot'] .'app'.str_replace("./","/",$this->createMobileurl('oauth',array('r_id'=>$r_id))));
  				$forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$reply['appid']."&redirect_uri={$callback}&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
				header('location: ' . $forward);
				exit();
			}else{
				message('错误');
			}
		}else{
			$openid=$_GPC['ssc_openid'.$r_id];
		}
		
		
		$fans=pdo_fetch('select * from '.tablename('j_pocketmoney_fans').' where rid=:rid AND  openid=:openid',array(':rid'=>$r_id,':openid'=>$openid));
		if(empty($fans['nickname'])){
 			//如果没有昵称，直接重新获取授权
			$callback = urlencode($_W['siteroot'] .'app'.str_replace("./","/",$this->createMobileurl('oauth')));
			$forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$reply['appid']."&redirect_uri={$callback}&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
			header('location: ' . $forward);
			exit();
		}
		//判断是否是粉丝
		#if($_W['account']['key']==$reply['appid'])$_W['fans']['from_user']=$openid;
		
		//如果没有$_W['fans']['from_user'];这里看上去是重复操作，但是如果第一次授权了，第二次进来没授权，这里有必要
		if(!empty($_W['fans']['from_user'])&&empty($fans['from_user'])){
			$insert=array(
				'from_user'=>$_W['fans']['from_user'],
				'subscribe'=>1,//首次为录入from_user,$_W['fans']['from_user']不为空的时候，判断其为已经关注了
				'subscribe_time'=>time(),
			);
			pdo_update('j_pocketmoney_fans',$insert,array('id'=>$fans['id']));
			$fans['from_user']=$_W['fans']['from_user'];
			$fans['subscribe']=1;
			$fans['subscribe_time']=time();
		}
		$from_user = !empty($_W['fans']['from_user']) ? $_W['fans']['from_user'] : $fans['from_user'];
		$follow = pdo_fetch('select follow from '.tablename('mc_mapping_fans').' where openid=:openid LIMIT 1',array(':openid'=>$from_user));
		
		if((empty($fans['from_user'])||$fans['subscribe']==0||$follow['follow'] <> 1) && !$_W['fans']['from_user']){
			//没有关注
			$status=-2;
		}else{
			$_W['user']=$fans;
			//是否首次领取
			$isfirstrecord=pdo_fetchcolumn('select count(*) from '.tablename('j_pocketmoney_records').' where rid=:rid AND from_user=:from_user order by id desc LIMIT 1',array(':rid'=>$r_id,':from_user'=>$openid));
			
			if($isfirstrecord==0){
				//第一次参与的粉丝
				$status=1;
			}else{
				//是否有未领取成功；
				$record=pdo_fetch('select * from '.tablename('j_pocketmoney_records').' where rid=:rid AND from_user=:from_user and completed=0 order by id desc LIMIT 1',array(':rid'=>$r_id,':from_user'=>$openid));
				if(empty($record)){
					//查看是否有分享
					$status=2;
				}else{
					//有就是重新发送
					$status=1;
					$restatus=1;
				}
			}
		}
		//$status=-3;//红包领完了，注释掉
		$list=pdo_fetchall('select * from '.tablename('j_pocketmoney_records').' where weid=:weid order by id desc limit 10',array(':weid'=>$_W['uniacid']));
		//会员数据为空
		if($fans==false){
			$fans=array(
				'headimgurl'=>$_W["siteroot"].'attachment/headimg_'.$_W['uniacid'].'.jpg',
				'nickname'=>$_W['account']['name'],
			);
		}
		include $this->template('index');
	}
	/**
	*积分接入
	* 
	*	
	*/
	public function doMobileIndexc() {
		global $_W,$_GPC;
		
		if(!$r_id)message('非法进入，请重新在页面触发进入');
		if($_GET['r_id']!=$_GPC['r_id'] && $_GET['r_id'])$r_id=$_GET['r_id'];
		isetcookie('r_id', $r_id, 600);
		
		$reply=pdo_fetch('select * from '.tablename('j_pocketmoney_reply').' where rid=:rid order BY id DESC LIMIT 1',array(':rid'=>$r_id));
		if(empty($reply))message('活动不存在或已删除！');
		if(TIMESTAMP<$reply['starttime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['starttime']).'开始,到时再来哦';
			message($msg,$reply['smurl'], 'success');
		}
		if(TIMESTAMP>$reply['endtime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['endtime']).'结束啦,下周再来吧';
			message($msg,$reply['smurl'], 'success');
		}
		if($reply['remainfee']<=0){
			message("已经抢玩了哦~下次记得早点出手哦！",$reply['smurl'], 'success');
		}
		//授权不存在,其他的如果不是这个入口，没有ssc_openid一律错误
		if(empty($_GPC['ssc_openid'.$r_id])){
			if(empty($_GPC['openid'])){
				$callback = urlencode($_W['siteroot'] .'app'.str_replace("./","/",$this->createMobileurl('oauth',array('r_id'=>$r_id))));
  				$forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$reply['appid']."&redirect_uri={$callback}&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
				header('location: ' . $forward);
				exit();
			}else{
				message('错误');
			}
		}else{
			$openid=$_GPC['ssc_openid'.$r_id];
		}
		
		$fans=pdo_fetch('select * from '.tablename('j_pocketmoney_fans').' where rid=:rid AND  openid=:openid',array(':rid'=>$r_id,':openid'=>$openid));
		if(empty($fans['nickname'])){
 			//如果没有昵称，直接重新获取授权
			$callback = urlencode($_W['siteroot'] .'app'.str_replace("./","/",$this->createMobileurl('oauth')));
			$forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$reply['appid']."&redirect_uri={$callback}&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
			header('location: ' . $forward);
			exit();
		}
		//如果没有$_W['fans']['from_user'];这里看上去是重复操作，但是如果第一次授权了，第二次进来没授权，这里有必要
		if(!empty($_W['fans']['from_user'])&&empty($fans['from_user'])){
			$insert=array(
				'from_user'=>$_W['fans']['from_user'],
				'subscribe'=>1,//首次为录入from_user,$_W['fans']['from_user']不为空的时候，判断其为已经关注了
				'subscribe_time'=>time(),
			);
			pdo_update('j_pocketmoney_fans',$insert,array('id'=>$fans['id']));
			$fans['from_user']=$_W['fans']['from_user'];
			$fans['subscribe']=1;
			$fans['subscribe_time']=time();
		}
		$from_user = !empty($_W['fans']['from_user']) ? $_W['fans']['from_user'] : $fans['from_user'];
		$uid = pdo_fetchcolumn('select uid from '.tablename('mc_mapping_fans').' where openid=:openid LIMIT 1',array(':openid'=>$from_user));
		if(empty($uid))message("本次活动只针对会员开展，详情请参阅说明！",$reply['smurl'], 'error');
		$fansUser = pdo_fetch('select * from '.tablename('mc_members').' where uid=:uid and uniacid=:uniacid ',array(':uid'=>$uid,':uniacid'=>$_W['uniacid']));
		if(empty($fansUser))message("本次活动只针对会员开展，详情请参阅说明！",$reply['smurl'], 'error');
		if($reply['groupid']){
			if($reply['groupid']!=$fansUser['groupid'])message("本次只针对特定级别的会员，你所属的会员级别无法参与！",$reply['smurl'], 'error');
		}
		if($fansUser[$reply['credittype']]<$reply['credit'] && $reply['credit'] && $reply['credittype']){
			message("亲，每次抽取需要".$reply['credit']."积分哦，你只有".$fansUser[$reply['credittype']]."，无法参与活动哦",$reply['smurl'], 'error');
		}
		
		$_W['user']=$fans;
		//是否首次领取
		$isfirstrecord=pdo_fetchcolumn('select count(*) from '.tablename('j_pocketmoney_records').' where rid=:rid AND from_user=:from_user order by id desc LIMIT 1',array(':rid'=>$r_id,':from_user'=>$openid));
		
		if($isfirstrecord==0){
			$status=1;
		}else{
			$record=pdo_fetch('select * from '.tablename('j_pocketmoney_records').' where rid=:rid AND from_user=:from_user and completed=0 order by id desc LIMIT 1',array(':rid'=>$r_id,':from_user'=>$openid));
			if(empty($record)){
				$status=2;
			}else{
				//有就是重新发送
				$status=1;
				$restatus=1;
			}
		}
		$list=pdo_fetchall('select * from '.tablename('j_pocketmoney_records').' where weid=:weid order by id desc limit 10',array(':weid'=>$_W['uniacid']));
		//会员数据为空
		if($fans==false){
			$fans=array(
				'headimgurl'=>$_W["siteroot"].'attachment/headimg_'.$_W['uniacid'].'.jpg',
				'nickname'=>$_W['account']['name'],
			);
		}
		
		include $this->template('indexc');
	}
	/**
	*通过摇一摇周边接入
	* 
	*	
	*/
	public function doMobileShake() {
		global $_W,$_GPC;
		$ticket=$_GPC['ticket'];
		$need_poi=$_GPC['need_poi'];
		$r_id=$_GPC['r_id'];
		if($ticket){
			isetcookie('ticket'.$_W['uniacid'],$ticket, 30 * 86400);
			isetcookie('need_poi'.$_W['uniacid'],$need_poi, 30 * 86400);
		}else{
			$ticket=$_GPC['ticket'.$_W['uniacid']];
			$need_poi=$_GPC['need_poi'.$_W['uniacid']];
		}
		load()->func('communication');
		$data=$this->_getMoiblePage($ticket,$need_poi);
		$openid_self=$data["data"]['openid'];
		if(!$openid_self)message("这个是现场红包哦，请到活动现场参与活动哦");
		
		$reply=pdo_fetch('select * from '.tablename('j_pocketmoney_reply').' where rid=:rid order BY id DESC LIMIT 1',array(':rid'=>$r_id));
		if($reply==false)message('请先设置规则');
		$follow = pdo_fetchcolumn('select follow from '.tablename('mc_mapping_fans').' where openid=:openid LIMIT 1',array(':openid'=>$openid_self));
		if($follow<>1){
			message("必须是我们的粉丝才可以领取哦~赶紧去关注吧",$reply['gzurl'], 'success');
		}
		if(TIMESTAMP<$reply['starttime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['starttime']).'开始,到时再来哦';
			message($msg,$reply['smurl'], 'success');
		}
		if(TIMESTAMP>$reply['endtime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['endtime']).'结束啦,下周再来吧';
			message($msg,$reply['smurl'], 'success');
		}
		if($reply['remainfee']<=0){
			message("红包已经抢玩了哦~下次记得早点出手哦！",$reply['smurl'], 'success');
		}
		
		if(empty($_GPC['ssc_openid'.$r_id])){
			$callback = urlencode($_W['siteroot'] .'app'.str_replace("./","/",$this->createMobileurl('oauth',array('r_id'=>$r_id))));
			$forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$reply['appid']."&redirect_uri={$callback}&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
			header('location:'. $forward);
			exit();
		}else{
			$openid=$_GPC['ssc_openid'.$r_id];
		}
		$fans=pdo_fetch('select * from '.tablename('j_pocketmoney_fans').' where rid=:rid AND  openid=:openid',array(':rid'=>$r_id,':openid'=>$openid));
		if(empty($fans['nickname'])){
 			//如果没有昵称，直接重新获取授权
			$callback = urlencode($_W['siteroot'] .'app'.str_replace("./","/",$this->createMobileurl('oauth',array('r_id'=>$r_id))));
			$forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$reply['appid']."&redirect_uri={$callback}&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
			header('location: ' . $forward);
			exit();
		}
		
		//如果没有$_W['fans']['from_user'];这里看上去是重复操作，但是如果第一次授权了，第二次进来没授权，这里有必要
		if(!empty($_W['fans']['from_user'])&&empty($fans['from_user'])){
			$insert=array(
				'from_user'=>$_W['fans']['from_user'],
				'subscribe'=>1,//首次为录入from_user,$_W['fans']['from_user']不为空的时候，判断其为已经关注了
				'subscribe_time'=>time(),
			);
			pdo_update('j_pocketmoney_fans',$insert,array('id'=>$fans['id']));		
			$fans['from_user']=$_W['fans']['from_user'];
			$fans['subscribe']=1;
			$fans['subscribe_time']=time();
		}
		
		$from_user = !empty($_W['fans']['from_user']) ? $_W['fans']['from_user'] : $fans['from_user'] ;
		$_W['user']=$fans;
		
		$isfirstrecord=pdo_fetchcolumn('select count(*) from '.tablename('j_pocketmoney_records').' where rid=:rid AND from_user=:from_user order by id desc LIMIT 1',array(':rid'=>$r_id,':from_user'=>$openid));
		if($isfirstrecord==0){
			//第一次参与的粉丝
			$status=1;
		}else{
			//是否有未领取成功；
			$record=pdo_fetch('select * from '.tablename('j_pocketmoney_records').' where rid=:rid AND from_user=:from_user and completed=0 order by id desc LIMIT 1',array(':rid'=>$r_id,':from_user'=>$openid));
			if(empty($record)){
				//查看是否有分享
				$status=2;
			}else{
				//有就是重新发送
				$status=1;
				$restatus=1;
			}
		}
		
		$list=pdo_fetchall('select * from '.tablename('j_pocketmoney_records').' where rid=:rid order by id desc limit 10',array(':rid'=>$r_id));
		//会员数据为空
		if($fans==false){
			$fans=array(
				'headimgurl'=>$_W["siteroot"].'attachment/headimg_'.$_W['uniacid'].'.jpg',
				'nickname'=>$_W['account']['name'],
			);
		}
		$cfg = $this->module['config'];
		include $this->template('shake');
	}
	/**
	* 领取第一次次红包
	* 
	* 	
	*这里有小小缺陷	
	*/
	public function doMobilesend(){
		global $_W,$_GPC;
		$r_id=intval($_GPC['r_id']);
		
		$reply=pdo_fetch('select * from '.tablename('j_pocketmoney_reply').' where rid=:rid order BY id DESC LIMIT 1',array(':rid'=>$r_id));
		if($reply==false){
			die(json_encode(array('message'=>'请先设置规则')));
		}
		if(TIMESTAMP<$reply['starttime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['starttime']).'开始,到时再来哦';
			die(json_encode(array('message'=>$msg)));
		}
		if(TIMESTAMP>$reply['endtime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['endtime']).'结束啦,下周再来吧';
			die(json_encode(array('message'=>$msg)));
		}
		if($reply['remainfee']<=0){
			$msg="红包已经抢玩了哦~下次记得早点出手哦！";
			die(json_encode(array('message'=>$msg)));
		}
		$openid=$_GPC['ssc_openid'.$r_id];
		if(empty($openid)){
			die(json_encode(array('message'=>'非法入口')));
		}
		$fans=pdo_fetch('select * from '.tablename('j_pocketmoney_fans').' where rid=:rid AND openid=:openid',array(':rid'=>$r_id,':openid'=>$openid));
		if($fans==false){
			die(json_encode(array('message'=>'您无权领取红包，请先关注我们哦')));
		}
		$fans['subscribe']=1;
		$_W['user']=$fans;
		//领红包以授权的openid为准
		$recordcount=pdo_fetchcolumn('select count(*) from '.tablename('j_pocketmoney_records').' where from_user=:from_user AND rid=:rid and completed=1',array(':rid'=>$r_id,':from_user'=>$openid));
		if($recordcount>=$reply['maxnums'])die(json_encode(array('message'=>'每人最多领取'.$reply['maxnums'].'次哦')));
		
		$record=pdo_fetch('select * from '.tablename('j_pocketmoney_records').' where from_user=:from_user AND rid=:rid and completed=0 order by id desc limit 1',array(':rid'=>$r_id,':from_user'=>$openid));
		
		if($record==false){
			//领取状态改为1，先锁死，避免重复领取
			pdo_update('j_pocketmoney_fans',array('issend'=>0),array('id'=>$fans['id']));
			if($fans['issend']==0){
				//这里可以根据条件控制发放金额
				if($record==false){
					$_W['fee']=rand($reply['firstmin'],$reply['firstmax']);
					
					if(intval($_W['fee'])>=intval($reply['remainfee'])){
						$_W['fee']=$reply['remainfee'];
					}
					
					$remain=intval($reply['remainfee']-$_W['fee']);
					pdo_update('j_pocketmoney_reply',array('remainfee'=>$remain),array('id'=>$reply['id']));
				}else{
					$_W['fee']=$record['fee'];
				}
				$_desc=$reply['packremark'];
				$procResult=$this->_sendpack($fans['openid'],0,2,$_desc,$r_id);
				if($procResult['errno']==0){
					die(json_encode(array('type'=>'success','message'=>'成功领取'.sprintf('%.2f', $_W['fee'] / 100).'元红包 请查看微信支付零钱包')));
				}else{
					pdo_update('j_pocketmoney_fans',array('issend'=>0),array('id'=>$fans['id']));
					die(json_encode(array('type'=>'false','message'=>$procResult['error'])));
				}
			}else{
				die(json_encode(array('type'=>'false','message'=>'别急，红包正在发送中。')));
			}
		}else{
			pdo_update('j_pocketmoney_fans',array('issend'=>1),array('id'=>$fans['id']));
			if($fans['issend']==0){
				$_W['fee']=$record['fee'];
				$_desc=$reply['packremark'];
				$procResult=$this->_sendpack($fans['openid'],$record['id'],0,$_desc,$r_id);
				if($procResult['errno']==0){
					pdo_update('j_pocketmoney_records',array('completed'=>1),array('id'=>$record['id']));
					die(json_encode(array('type'=>'success','message'=>'成功领取'.sprintf('%.2f', $_W['fee'] / 100).'元红包 请查看微信支付零钱包')));
				}else{
					pdo_update('j_pocketmoney_fans',array('issend'=>0),array('id'=>$fans['id']));
					die(json_encode(array('type'=>'false','message'=>$procResult['error'])));
				}
			}
		}
	}
 	/**
	* 领取二次红包
	* 
	* 	二次领取的时候，会员一定存在
	*	会员没有关注无法领取
	*/
	public function doMobilesend2(){
		global $_W,$_GPC;
		$r_id=intval($_GPC['r_id']);
		$reply=pdo_fetch('select * from '.tablename('j_pocketmoney_reply').' where rid=:rid order BY id DESC LIMIT 1',array(':rid'=>$r_id));
		$openid=$_GPC['ssc_openid'.$r_id];
		$fans=pdo_fetch('select * from '.tablename('j_pocketmoney_fans').' where rid=:rid AND  openid=:openid',array(':rid'=>$r_id,':openid'=>$openid));
		if($fans==false){
			die(json_encode(array('message'=>'您无权领取红包')));
		}
		if(TIMESTAMP<$reply['starttime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['starttime']).'开始,到时再来哦';
			die(json_encode(array('type'=>'false','message'=>$msg)));
		}	
		if(TIMESTAMP>$reply['endtime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['endtime']).'结束啦,下周再来吧';
			die(json_encode(array('type'=>'false','message'=>$msg)));
		}
		if($reply['remainfee']<=0){
			$msg="红包已经抢玩了哦~下次记得早点出手哦！";
			die(json_encode(array('message'=>$msg)));
		}
		if($fans['subscribe']==0){
			die(json_encode(array('message'=>'没有关注公共号')));
		}
		$recordcount=pdo_fetchcolumn('select count(*) from '.tablename('j_pocketmoney_records').' where  from_user=:from_user AND rid=:rid ',array(':rid'=>$r_id,':from_user'=>$fans['openid']));
		
		//判断有没有权利领取红包
		/*参数说明
		vnums:查看
		unums:下线人数
		hnums:发放下线红包数
		*/
		$neednums=intval($reply['neednums']);
		if($neednums==0){
			$neednums=10;
		}
		if($fans['unums']<($neednums * ($fans['hnums']+1))){
			die(json_encode(array('message'=>'邀请的好友不足，无法领取')));
		}else{
			$_W['user']=$fans;
 			$record=pdo_fetch('select * from '.tablename('j_pocketmoney_records').' where  from_user=:from_user AND rid=:rid  order by id desc limit 1',array(':rid'=>$r_id,':from_user'=>$openid));
			$_W['fee']=rand($reply['secondmin'],$reply['secondmax']);
			if($_W['fee']>$reply['remainfee'])$_W['fee']=$reply['remainfee'];
			pdo_update('j_pocketmoney_reply',array('remainfee'=>$reply['remainfee']-$_W['fee']),array('id'=>$reply['id']));
			//领取的红包个数+1
			pdo_update('j_pocketmoney_fans',array('hnums'=>$fans['hnums']+1),array('id'=>$fans['id']));
			$procResult=$this->_sendpack($fans['openid'],0,2,$reply['packremark'],$r_id);
			if($procResult['errno']==0){
				die(json_encode(array('type'=>'success','message'=>'成功领取'.sprintf('%.2f', $_W['fee'] / 100).'元红包 请查看微信支付零钱包')));
			}else{
				//领取失败的时候
				pdo_update('j_pocketmoney_fans',array('hnums'=>$fans['hnums']),array('id'=>$fans['id']));
				die(json_encode(array('type'=>'success','message'=>$procResult['errno'])));
			}
		}
	}
	/**
	* 现场版领取二次红包
	* 
	* 	二次领取的时候，会员一定存在
	*	会员没有关注无法领取
	*/
	public function doMobilesend3(){
		global $_W,$_GPC;
		$r_id=intval($_GPC['r_id']);
		$reply=pdo_fetch('select * from '.tablename('j_pocketmoney_reply').' where rid=:rid order BY id DESC LIMIT 1',array(':rid'=>$r_id));
		$openid=$_GPC['ssc_openid'.$r_id];
		$fans=pdo_fetch('select * from '.tablename('j_pocketmoney_fans').' where rid=:rid AND  openid=:openid',array(':rid'=>$r_id,':openid'=>$openid));
		if($fans==false){
			die(json_encode(array('message'=>'您无权领取红包')));
		}
		if(TIMESTAMP<$reply['starttime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['starttime']).'开始,到时再来哦';
			die(json_encode(array('type'=>'false','message'=>$msg)));
		}	
		if(TIMESTAMP>$reply['endtime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['endtime']).'结束啦,下周再来吧';
			die(json_encode(array('type'=>'false','message'=>$msg)));
		}
		if($reply['remainfee']<=0){
			$msg="红包已经抢玩了哦~下次记得早点出手哦！";
			die(json_encode(array('message'=>$msg)));
		}
		if($fans['subscribe']==0){
			die(json_encode(array('message'=>'没有关注公共号')));
		}
		$recordcount=pdo_fetchcolumn('select count(*) from '.tablename('j_pocketmoney_records').' where  from_user=:from_user AND rid=:rid ',array(':rid'=>$r_id,':from_user'=>$fans['openid']));
		
		if($recordcount>=$reply['maxnums']){
			die(json_encode(array('message'=>'每人最多领取'.$reply['maxnums'].'次哦')));
		}else{
			$_W['user']=$fans;
			$_W['fee']=rand($reply['secondmin'],$reply['secondmax']);
			if($_W['fee']>$reply['remainfee'])$_W['fee']=$reply['remainfee'];
			pdo_update('j_pocketmoney_reply',array('remainfee'=>$reply['remainfee']-$_W['fee']),array('id'=>$reply['id']));
			//领取的红包个数+1
			pdo_update('j_pocketmoney_fans',array('hnums'=>$fans['hnums']+1),array('id'=>$fans['id']));
			$procResult=$this->_sendpack($fans['openid'],0,2,$reply['packremark'],$r_id);
			if($procResult['errno']==0){
				die(json_encode(array('type'=>'success','message'=>'成功领取'.sprintf('%.2f', $_W['fee'] / 100).'元红包 请查看微信支付零钱包')));
			}else{
				//领取失败的时候
				pdo_update('j_pocketmoney_fans',array('hnums'=>$fans['hnums']),array('id'=>$fans['id']));
				die(json_encode(array('type'=>'success','message'=>$procResult['errno'])));
			}
		}
	}
	/**
	* 积分抽取
	* 
	* 	
	*	
	*/
	public function doMobileJfsend(){
		global $_W,$_GPC;
		$r_id=intval($_GPC['r_id']);
		$uid=intval($_GPC['uid']);
		if(!$_W['openid'] || $uid)die(json_encode(array('message'=>'必须是本站会员方可参与哦')));
		$reply=pdo_fetch('select * from '.tablename('j_pocketmoney_reply').' where rid=:rid order BY id DESC LIMIT 1',array(':rid'=>$r_id));
		if($reply==false){
			die(json_encode(array('message'=>'请先设置规则')));
		}
		if(TIMESTAMP<$reply['starttime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['starttime']).'开始,到时再来哦';
			die(json_encode(array('message'=>$msg)));
		}
		if(TIMESTAMP>$reply['endtime']){
			$msg='活动在'.date('Y-m-d H:i',$reply['endtime']).'结束啦,下周再来吧';
			die(json_encode(array('message'=>$msg)));
		}
		if($reply['remainfee']<=0){
			$msg="红包已经抢玩了哦~下次记得早点出手哦！";
			die(json_encode(array('message'=>$msg)));
		}
		$fansUser = pdo_fetch('select * from '.tablename('mc_members').' where uid=:uid  ',array(':uid'=>$uid));
		if(empty($fansUser))die(json_encode(array('message'=>'本次活动只针对会员开展')));
		if($reply['groupid']){
			if($reply['groupid']!=$fansUser['groupid'])die(json_encode(array('message'=>'本次只针对特定级别的会员，你所属的会员级别无法参与！')));
		}
		if($fansUser[$reply['credittype']]<$reply['credit'] && $reply['credit'] && $reply['credittype']){
			die(json_encode(array('message'=>"每次抽取需要".$reply['credit']."积分哦，你只有".$fansUser[$reply['credittype']]."，无法参与活动哦")));
		}
		
		$openid=$_GPC['ssc_openid'.$r_id];
		if(empty($openid)){
			die(json_encode(array('message'=>'非法入口')));
		}
		$fans=pdo_fetch('select * from '.tablename('j_pocketmoney_fans').' where rid=:rid AND openid=:openid',array(':rid'=>$r_id,':openid'=>$openid));
		if($fans==false){
			die(json_encode(array('message'=>'您无权领取红包，请先关注我们哦')));
		}
		$fans['subscribe']=1;
		$_W['user']=$fans;
		//领红包以授权的openid为准
		$recordcount=pdo_fetchcolumn('select count(*) from '.tablename('j_pocketmoney_records').' where from_user=:from_user AND rid=:rid and completed=1',array(':rid'=>$r_id,':from_user'=>$openid));
		if($recordcount>=$reply['maxnums'])die(json_encode(array('message'=>'每人最多领取'.$reply['maxnums'].'次哦')));
		
		$record=pdo_fetch('select * from '.tablename('j_pocketmoney_records').' where from_user=:from_user AND rid=:rid and completed=0 order by id desc limit 1',array(':rid'=>$r_id,':from_user'=>$openid));
		load()->model('mc');
		if($record==false){
			//领取状态改为1，先锁死，避免重复领取
			pdo_update('j_pocketmoney_fans',array('issend'=>0),array('id'=>$fans['id']));
			if($fans['issend']==0){
				//这里可以根据条件控制发放金额
				if($record==false){
					$_W['fee']=rand($reply['firstmin'],$reply['firstmax']);
					if($_W['fee']>$reply['remainfee'])$_W['fee']=$reply['remainfee'];
					
					pdo_update('j_pocketmoney_reply',array('remainfee'=>$reply['remainfee']-$_W['fee']),array('id'=>$reply['id']));
				}else{
					$_W['fee']=$record['fee'];
				}
				$_desc=$reply['packremark'];
				$procResult=$this->_sendpack($fans['openid'],0,2,$_desc,$r_id);
				if($procResult['errno']==0){
					mc_credit_update($uid,$reply['credittype'],($reply['credit']*-1),array('','积分抽奖'));
					die(json_encode(array('type'=>'success','message'=>'成功领取'.sprintf('%.2f', $_W['fee'] / 100).'元红包 请查看微信支付零钱包')));
				}else{
					pdo_update('j_pocketmoney_fans',array('issend'=>0),array('id'=>$fans['id']));
					die(json_encode(array('type'=>'false','message'=>$procResult['error'])));
				}
			}else{
				die(json_encode(array('type'=>'false','message'=>'别急，红包正在发送中。')));
			}
		}else{
			pdo_update('j_pocketmoney_fans',array('issend'=>1),array('id'=>$fans['id']));
			if($fans['issend']==0){
				$_W['fee']=$record['fee'];
				$_desc=$reply['packremark'];
				$procResult=$this->_sendpack($fans['openid'],$record['id'],0,$_desc,$r_id);
				if($procResult['errno']==0){
					pdo_update('j_pocketmoney_records',array('completed'=>1),array('id'=>$record['id']));
					mc_credit_update($uid,$reply['credittype'],($reply['credit']*-1),array('','积分抽奖'));
					die(json_encode(array('type'=>'success','message'=>'成功领取'.sprintf('%.2f', $_W['fee'] / 100).'元红包 请查看微信支付零钱包')));
				}else{
					pdo_update('j_pocketmoney_fans',array('issend'=>0),array('id'=>$fans['id']));
					die(json_encode(array('type'=>'false','message'=>$procResult['error'])));
				}
			}
		}
	}
	public function doWebAdd() {
		global $_GPC, $_W;
		
		include $this->template('adv_add');
	}
	public function doWebUser() {
		//这个操作被定义用来呈现 规则列表
		global $_GPC, $_W;
		$table='j_pocketmoney_fans';
		$rid=intval($_GPC['id']);
		$op=empty($_GPC['op'])?'display':$_GPC['op']; 
		if($_GPC['op']=='post'){  
			$field=array('listorder','title','desc');  
			$id=intval($_GPC['uid']);
			if($_W['ispost']){  
				//保存数据  
				foreach($field as $v){  
					$insert[$v]=$_GPC[$v];  
				}  
				if($id>0){  
					$temp=pdo_update($table, $insert, array('id' => $id,'weid'=>$_W['uniacid']));  
				}else{  
					$insert['weid']=$_W['uniacid'];  
					$temp=pdo_insert($table,$insert);  
				}  
				if($temp===false){                
					message('抱歉，数据操作失败！','', 'error');                
				}else{  
					message('更新数据成功！', $this->createWeburl('user',array('id'=>$rid)), 'success');
				}  
			}  
			if($id>0){  
				$item=pdo_fetch('select * from '.tablename($table).' where id=:id',array(':id'=>$id));  
				$rid=$item['rid'];
			}
			if($item==false){  
				//初始数值  
				$item=array(  
					'listorder'=>0  
				);    
			}  
		}elseif($op=='delete'){  
			$id=intval($_GPC['uid']);  
			if(empty($id))message('参数错误，请确认操作');
			$item=pdo_fetch('select * from '.tablename($table).' where id=:id',array(':id'=>$id));  
			pdo_delete('j_pocketmoney_records',array('from_user'=>$item['from_user'],'rid'=>$item['rid'],));
			pdo_delete('j_pocketmoney_records',array('from_user'=>$item['openid'],'rid'=>$item['rid'],));
			pdo_delete('j_pocketmoney_fans',array('id'=>$item['id'],));
			message('删除数据成功！',$this->createWeburl('user',array('id'=>$item['rid'])), 'success');
			  
		}elseif($op=='display'){  
			$where="WHERE rid=".$rid." ";
			$pindex = max(1, intval($_GPC['page']));  
			$psize = 20;  
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($table) . $where);  
			$start = ($pindex - 1) * $psize;  
			$where .= "  order by `id` desc   LIMIT {$start},{$psize}";  
			$list = pdo_fetchall("SELECT * FROM ".tablename($table)." ".$where);  
			$pager = pagination($total, $pindex, $psize);  
			
		}         
		include $this->template('adv_user');
	}
	
	public function doWebRecord() {
		//这个操作被定义用来呈现 规则列表
		global $_GPC, $_W;
		$table='j_pocketmoney_records';  
		$op=empty($_GPC['op'])?'display':$_GPC['op'];  
		$rid=intval($_GPC['rid']);
		$reply=pdo_fetch('select * from '.tablename('j_pocketmoney_reply').' where rid=:rid order BY id DESC LIMIT 1',array(':rid'=>$rid));
		if($_GPC['op']=='post'){  
			$field=array('listorder','title','desc');  
			$id=intval($_GPC['id']);
			if($_W['ispost']){  
				//保存数据  
				foreach($field as $v){  
					$insert[$v]=$_GPC[$v];  
				}  
				if($id>0){  
					$temp=pdo_update($table, $insert, array('id' => $id,'rid'=>$rid));  
				}else{  
					$insert['weid']=$_W['uniacid'];  
					$temp=pdo_insert($table,$insert);  
				}  
				if($temp===false){                
					message('抱歉，数据操作失败！','', 'error');                
				}else{  
					message('更新数据成功！', $this->createWeburl('cate'), 'success');  
				}  
			}  
			if($id>0){  
				$item=pdo_fetch('select * from '.tablename($table).' where rid=:rid AND id=:id',array(':rid'=>$rid,':id'=>$id));  
			}     
			if($item==false){  
				//初始数值  
				$item=array(  
					'listorder'=>0  
				);    
			}  
		}elseif($op=='delete'){
			$id=intval($_GPC['id']);  
			if(empty($id))message('参数错误，请确认操作');
			$item=pdo_fetch('select * from '.tablename($table).' where id=:id',array(':id'=>$id));
			pdo_update("j_pocketmoney_reply",array('remainfee'=>$reply['remainfee']+$item['fee']),array('rid'=>$item['rid']));
			$temp = pdo_delete($table,array('id'=>$id));
			if($temp==false){  
				message('抱歉，刚才修改的数据失败！','', 'error');                
			}else{  
				message('删除数据成功！',$this->createWebUrl('record',array('rid'=>$rid)), 'success');
			}
		}elseif($op=='display'){  
		
			$where="WHERE weid=".$_W['uniacid']." ";  
			if($_GPC['completed']==-1){
				$where.=' AND completed=0 ';
			}else{
				$where.=' AND completed=1 ';
			}
			$pindex = max(1, intval($_GPC['page']));  
			$psize = 20;  
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($table) . $where);  
			$start = ($pindex - 1) * $psize;  
			$where .= "  order by `id` desc   LIMIT {$start},{$psize}";  
			$list = pdo_fetchall("SELECT * FROM ".tablename($table)." ".$where);  
			$pager = pagination($total, $pindex, $psize);  
			
			$total_fee = pdo_fetchcolumn('SELECT sum(fee) FROM ' . tablename($table).' WHERE rid='.$rid);  
		
		}         
		include $this->template('adv_records');  
	}
	public function doWebAdvert() {
		global $_GPC, $_W;
		$rid=intval($_GPC['rid']);
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$category = pdo_fetchall("SELECT * FROM ".tablename('j_pocketmoney_ad')." WHERE weid = '{$_W['uniacid']}' order by id desc");
		} elseif ($operation == 'post') {
			load()->func('tpl');
			$id = intval($_GPC['id']);
			if(!empty($id)) {
				$category = pdo_fetch("SELECT * FROM ".tablename('j_pocketmoney_ad')." WHERE id = '$id'");
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) message('抱歉，请输入广告名称！');
				$data = array(
					'weid' => $_W['uniacid'],
					'title' => $_GPC['title'],
					'thumb' => $_GPC['thumb'],
					'description' => $_GPC['description'],
					'url' => $_GPC['url'],
				);
				if (!empty($id)) {
					pdo_update('j_pocketmoney_ad', $data, array('id' => $id));
				} else {
					pdo_insert('j_pocketmoney_ad', $data);
				}
				message('更新广告成功！', $this->createWebUrl('advert', array('op' => 'display')), 'success');
			}
		} elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			pdo_delete('j_pocketmoney_ad', array('id' => $id));
			message('广告删除成功！', $this->createWebUrl('advert', array('op' => 'display',)), 'success');
		}
		include $this->template('advert');
	}
	public function doWebAjax() {
		global $_GPC, $_W;
		if(!$_W['isajax'])die(json_encode(array('success'=>false,'msg'=>'无法获取系统信息,请重新打开再尝试')));
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if($operation=='getlocation'){
			$location=$_GPC['latLng'];
			$key="V7IBZ-W6T3F-MPCJO-JFIJN-EWVFZ-FPF4K";
			$url="http://apis.map.qq.com/ws/geocoder/v1/?location=".$location."&key=".$key."&get_poi=0";
			load()->func('communication');
			$result = file_get_contents($url);
			$content = json_decode($result,true);
			if ($content['status'] == 0) { //获取成功
				$address = $content['result']['address_component'];
				$data = array(
					'p' => $address['province'],
					'c' => $address['city'],
					'd' => $address['district'],
					's' => $address['street'],
				);
				die(json_encode(array('success'=>true,'item'=>$data)));
			}
			die(json_encode(array('success'=>false,'msg'=>'无法获取系统信息,请重新打开再尝试')));
		}
	}
	/**
	* 发送红包核心函数
	*
	* @return array
	* 返回的红包发送结果
 	*/
	private function _sendpack($_openid,$rid=0,$_status=0,$_desc='',$r_id=0){
		global $_W;
		$reply=pdo_fetch('select * from '.tablename('j_pocketmoney_reply').' where rid=:rid order BY id DESC LIMIT 1',array(':rid'=>$r_id));
		if(empty($_openid)){
			return false;
		}
		$fee = empty($_W['fee'])?1:$_W['fee'];	
 		$url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $pars = array();
		$pars['mch_appid'] =$reply['appid'];
		$pars['mchid']=$reply['mchid'];		
		$pars['nonce_str'] =random(32);	
		$pars['partner_trade_no'] =time().random(3,1);	
		$pars['openid'] =$_openid;
		$pars['check_name'] ='NO_CHECK' ;
		$pars['amount'] =$fee;		
		$pars['desc'] =(empty($_desc)?'没什么，就是想送你一个红包':$_desc);
		$pars['spbill_create_ip'] =$reply['ip'];
		
        ksort($pars, SORT_STRING);
        $string1 = '';
        foreach ($pars as $k => $v) {
            $string1 .= "{$k}={$v}&";
        }
        $string1 .= "key=".$reply['signkey'];
        $pars['sign'] = strtoupper(md5($string1));
        $xml = array2xml($pars);
        $extras = array();
        $extras['CURLOPT_CAINFO'] =  '../addons/j_pocketmoney/cert_2/'.$r_id.'/rootca.pem';
        $extras['CURLOPT_SSLCERT'] ='../addons/j_pocketmoney/cert_2/'.$r_id.'/apiclient_cert.pem';
        $extras['CURLOPT_SSLKEY'] ='../addons/j_pocketmoney/cert_2/'.$r_id.'/apiclient_key.pem';
		$procResult = null;
		load()->func('communication');
        $resp = ihttp_request($url, $xml, $extras);
		//return var_dump($resp);
        if (is_error($resp)) {
            $procResult = $resp;
        } else {
			$arr=json_decode(json_encode((array) simplexml_load_string($resp['content'])), true);
            $xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
            $dom = new \DOMDocument();
            if ($dom->loadXML($xml)) {
                $xpath = new \DOMXPath($dom);
                $code = $xpath->evaluate('string(//xml/return_code)');
                $ret = $xpath->evaluate('string(//xml/result_code)');
                if (strtolower($code) == 'success' && strtolower($ret) == 'success') {
                    $procResult =  array('errno'=>0,'error'=>'success');
                } else {
                    $error = $xpath->evaluate('string(//xml/err_code_des)');
                    $procResult = array('errno'=>-2,'error'=>$error);
                }
            } else {
				$procResult = array('errno'=>-1,'error'=>'未知错误');				
            }
        }
		//以上为支付部分，以下为记录日志
		$rec = array();
		$rec['log'] = $error;
		$rec['weid']=$_W['uniacid'];
		$rec['from_user']=$_openid;
		$rec['fee']=$fee;
		$rec['rid']=$r_id;
		$rec['created']=time();
		$rec['province']=$_W['user']['province'];
		$rec['city']=$_W['user']['city'];		
		$rec['nickname']=$_W['user']['nickname'];		
		$rec['headimgurl']=$_W['user']['headimgurl'];		
		$rec['kouhao']=$this->_getkouhao();
		$rec['status']=$_status;
        if ($procResult['errno']!=0) {
			if($rid==0){
				$rec['completed']=$procResult['errno'];
				pdo_insert('j_pocketmoney_records',$rec);
			}else{
				pdo_update('j_pocketmoney_records',$rec,array('id'=>$rid));
			}
        } else {
			$rec['completed']=1;
			if($rid>0){
				$rec['created']=time();
			}
			if($rid==0){
				pdo_insert('j_pocketmoney_records',$rec);
			}else{
				pdo_update('j_pocketmoney_records',$rec,array('id'=>$rid));
			}
			$_from_user=pdo_fetchcolumn('select from_user from '.tablename('j_pocketmoney_fans').' where openid=:openid order BY id DESC LIMIT 1',array(":openid"=>$_openid));
			if($_from_user){
				$cfg = $this->module['config'];
				$_fee=sprintf('%.2f', $fee/ 100);
				$str=$cfg['get_msg']?$cfg['get_msg']:"恭喜，你已经成功领取|#金额#|元红包，请到微信钱包查收！";
				$sendstr=str_replace("|#金额#|",$_fee,$str);
				$sendstr=str_replace("|#昵称#|",$_W['user']['nickname'],$sendstr);
				$this->sendtext($sendstr,$_from_user);
			}
        }
		return $procResult;
	}
	/**
	* 会员难道红包的口号
	*
	* @return str
	* 返回的领取到红包的口号
 	*/	
	private function _getkouhao(){
		global $_GPC, $_W;
		$cfg = $this->module['config'];
		$kouhao=explode("|$|",$cfg['key_kouhao']);
		array_filter($kouhao);
		/*$kouhao=array(
			'红包到手，赶紧买个馒头去，饿死偶了^^','红包、红包，我最爱','俺送礼你出钱,楼猪真是最好滴人','感谢 感谢 我也顺手送好友！','发财啦 发财啦!!',	'终于凑够钱，这就整二两牛杂','手机抖抖，红包到手 哈哈~~','恭喜发财 小伙伴们一起来','红包在手，江山我有','一毛钱都是爱，一分钱俺也不嫌少:)','谢谢老板','看好吧,真有天上掉钱耶~',
		);*/
		$kouhaostr=$kouhao[rand(0,count($kouhao)-1)];
		if(empty($kouhaostr)){
			return $kouhao[0];
		}else{
			return $kouhaostr;
		}
	}
	/**
	* 发送客服消息
	* $access_token= account_weixin_token($_W['account']);
	* 当用户接到到一条模板消息，会给公共平台api发送一个xml文件【待处理】
	*/	
	public function sendtext($text,$openid){
		$data = array(
		  "touser"=>$openid,
		  "msgtype"=>"text",
		  "text"=>array("content"=>urlencode($text))
		);
		$access_token=$this->_fetch_token();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$access_token;
		load()->func('communication');
		$r=ihttp_post($url,urldecode(json_encode($data)));
		if($r['code']==200){
			return $r['content'];
		}else{
			return 'err';
		}
	}
	/**
	* 获取主号ACCESS_TOKEN
	*
	* @return str
	* 返回当前公众号的ACCESS_TOKEN
 	*/
	private function _fetch_token() {
		global $_GPC, $_W;
		load()->func('communication');
		$Jetsumtoken="";
		$account=pdo_fetch("SELECT * FROM ".tablename('account_wechats')." WHERE uniacid = :uniacid",array(':uniacid'=>$_W['uniacid']));
		$acccount_acc=iunserializer($account['access_token']);
		if(is_array($acccount_acc) && !empty($acccount_acc['token']) && !empty($acccount_acc['expire']) && $acccount_acc['expire'] > TIMESTAMP) {
			$Jetsumtoken=$acccount_acc['token'];
		} else {
			if (empty($account['key']) || empty($account['secret']))return 1;
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$account['key']}&secret={$account['secret']}";
			$content = ihttp_get($url);
			if(is_error($content))return 2;
			$token = @json_decode($content['content'], true);
			if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['expires_in'])) {
				$errorinfo = substr($content['meta'], strpos($content['meta'], '{'));
				$errorinfo = @json_decode($errorinfo, true);
				return 3;
			}
			$record = array();
			$record['token'] = $token['access_token'];
			$record['expire'] = TIMESTAMP + $token['expires_in'];
			$row = array();
			$row['access_token'] = iserializer($record);
			pdo_update('account_wechats', $row, array('acid' => $_W['account']['acid']));
			$Jetsumtoken= $record['token'];
		}
		return $Jetsumtoken;
	}
	/**
	* 获取主号ACCESS_TOKEN
	*
	* @return str
	* 返回当前公众号的ACCESS_TOKEN
 	*/
	private function _getMoiblePage($ticket="",$need_poi=""){
		global $_GPC, $_W;
		$token=$this->_fetch_token();
		if(empty($token))return 4;
		load()->func('communication');
		$url="https://api.weixin.qq.com/shakearound/user/getshakeinfo?access_token=".$token;
		$data = array('ticket'=>$ticket,'need_poi'=>$need_poi);
		$data_string = json_encode($data);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);
		$result = curl_exec($ch);
		$token = @json_decode($result, true);
		return $token;
	}
}