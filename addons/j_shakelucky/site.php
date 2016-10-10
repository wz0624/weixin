<?php

/**

 * 捷讯求缘分模块微站定义

 *

 * @author 捷讯设计

 * @url http://bbs.012wz.com/

 */

defined('IN_IA') or exit('Access Denied');

include('../addons/j_shakelucky/jetsum_function.php');

class J_shakeluckyModuleSite extends WeModuleSite {

	

	public function doMobileAjax() {

		global $_GPC, $_W;

		if(!$_W['isajax'] || !$_W['openid'])die();

		$id=intval($_GPC['id']);

		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

		if($operation=='winning'){

			if(empty($id))die();

			$rid=$id;

			$item = pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_reply')." WHERE rid = '".$rid."' ");

			$list = pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_award')." WHERE rid = '$rid' and total>0 and absolute=1 ORDER BY id asc");

			//=======//

			//=======//

			//=======//

			if($item['starttime']>TIMESTAMP)die(json_encode(array('err'=>1,'msg'=>'游戏还没有开始哦')));

			if($item['endtime']<TIMESTAMP)die(json_encode(array('err'=>2,'msg'=>'游戏已结束了哦')));

			if($item['status']!=1)die(json_encode(array('err'=>3,'msg'=>'游戏已结束了哦')));

			

			if($item['onlyone']){

				$isHit=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' and from_user='".$_W['openid']."' and isprize>0");

				if($isHit)if(!$all_prize)die(json_encode(array('err'=>5,'msg'=>'您已经中奖了哦~留机会给别人吧')));

			}

			

			$play_count=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' and from_user='".$_W['openid']."' ");

			if(!empty($play_count)){

				$last_time=pdo_fetchcolumn("SELECT createtime FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' and from_user='".$_W['openid']."' order by id desc limit 1");

				if(TIMESTAMP-$last_time<5)die(json_encode(array('err'=>1,'msg'=>'摇动时间间隔过短')));

				$s_time=strtotime(date('Y-m-d')." 00:00:00");

				$e_time=strtotime(date('Y-m-d')." 23:59:59");

				$play_count=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' and from_user='".$_W['openid']."' and createtime>=$s_time and createtime<=$e_time ");

				

				if($play_count>=$item['maxlottery']){

					$cfg = $this->module['config'];

					if($cfg['is_sharehelp']){

						$inventime=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_invent')." WHERE rid = '".$rid."' and inventor='".$_W['openid']."' and status=1");

						//助力使用完

						if($inventime>=$item['sharehelp'])die(json_encode(array('err'=>4,'msg'=>'每人每日最多可以参与'.$item["maxlottery"].'次。呼唤小伙伴来助您一臂之力吧！')));

						$inventime=pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_invent')." WHERE rid = '".$rid."' and inventor='".$_W['openid']."' and status=0 order by id asc");

						if(empty($inventime))die(json_encode(array('err'=>4,'msg'=>'每人每日最多可以参与'.$item["maxlottery"].'次。呼唤小伙伴来助您一臂之力吧！')));

						pdo_update("j_shakelucky_invent",array("status"=>1),array("id"=>$inventime['id']));

					}else{

						die(json_encode(array('err'=>4,'msg'=>'每人每日最多可以参与'.$item["maxlottery"].'次。呼唤小伙伴来助您一臂之力吧！')));

					}

				}

			}

			$all_prize=pdo_fetchcolumn("SELECT sum(total) FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' and isprize=1 ");

			if(!$all_prize)die(json_encode(array('err'=>5,'msg'=>'亲，您来晚了~奖品都被抢光了哦！')));

			//====

			$prize_arr=array();

			$i=1;

			foreach($list as $row){

				$data=array(

					"id"=>$i,

					"sid"=>$row['id'],

					"title"=>$row['title'],

					"is"=> $row['isprize'],

					"probalilty"=>$row['probalilty'],

					"absolute"=>$row['absolute'],

				);

				array_push($prize_arr,$data);

				$i++;

			}

			$arr=array();

			foreach ($prize_arr as $key => $val) { 

				$arr[$val['id']] = $val['probalilty']; 

			}

			$proSum = array_sum($arr); 

			$result="";

			foreach ($arr as $key => $proCur) { 

				$randNum = mt_rand(1, $proSum); 

				if ($randNum <= $proCur) { 

					$result = $key; 

					break; 

				} else { 

					$proSum -= $proCur; 

				}

			}

			$res = $prize_arr[$result-1];

			$prizeItem = pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_award')." WHERE id = '".$res['sid']."' ");

			load()->model('mc');

			$avatar="";

			$nickname="";

			$gender="";

			$profile=j_member_fetch();

			$avatar=$profile['avatar'];

			$nickname=$profile['nickname'];

			$gender=$profile['gender'];

			$realname=$profile['realname'];

			$mobile=$profile['mobile'];

			$data=array(

				'rid'=>$rid,

				"isprize"=> $prizeItem['isprize'],

				"prizeid"=> $prizeItem['id'],

				'from_user'=>$_W['openid'],

				'nickname'=>$nickname,

				'gender'=>$gender,

				'avatar'=>$avatar,

				'realname'=>$realname,

				'mobile'=>$mobile,

				'weid'=>$_W['uniacid'],

				'createtime'=>TIMESTAMP,

			);

			pdo_insert('j_shakelucky_winner', $data);

			$wid = pdo_insertid();

			pdo_update('j_shakelucky_award',array('total'=>$prizeItem['total']-1),array('id'=>$prizeItem['id']));

			$all_prize=pdo_fetchcolumn("SELECT sum(total) FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' and isprize=1 ");
			$all_prize2=pdo_fetchcolumn("SELECT sum(othernum) FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' and isprize=1 ");
			
			die(urldecode(json_encode(array(

				'id'=>$prizeItem['id'],

				'wid'=>$wid,

				"isprize"=> $prizeItem['isprize'],

				'title'=>urlencode($prizeItem['title']),

				'sponsor'=>urlencode($prizeItem['sponsor']),

				'description'=>urlencode($prizeItem['description']),

				'remain'=>$all_prize+$all_prize2,

			))));

		}

		if($operation=='updateinfo'){

			$fromuser = $_W['openid'];

			$realname=$_GPC['realname'];

			$mobile=$_GPC['mobile'];

			$data=array(

				'mobile'=>$_GPC['mobile'],

				'realname'=>$_GPC['realname'],

			);

			load()->model('mc');

			j_member_update(array('mobile'=>$_GPC['mobile'],'realname'=>$_GPC['realname'],));

			die(json_encode(array('success'=>true)));

		}

		if($operation=='loginmobile'){

			$realname=$_GPC['realname'];

			$rid=intval($_GPC['rid']);

			$sid=trim($_GPC['sid']);

			$code=$_GPC['code'];

			if(!$rid || !$code || !$sid)die(json_encode(array('success'=>false,"msg"=>"参数不能为空")));

			if($sid=='1111'){

				$item=pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_reply')." WHERE rid =:rid and code =:code ",array(":rid"=>$rid,":code"=>$code,));

				if(empty($item))die(json_encode(array('success'=>false,"msg"=>"游戏号或验证码错误！")));

			}else{

				$item=pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_sponsor')." WHERE rid =:rid and password =:code and id=:id and status=1",array(":rid"=>$rid,":id"=>$sid,":code"=>$code,));

				if(empty($item))die(json_encode(array('success'=>false,"msg"=>"游戏号或验证码错误！")));

			}

			die(json_encode(array('success'=>true)));

		}

		if($operation=='getuserprize'){

			$code=urldecode($_GPC['code']);

			$sid=intval($_GPC['sid']);

			if(!$code)die(json_encode(array('success'=>false,"msg"=>"参数不能为空")));

			$content=encrypt($code, 'D', "www.yfjs-design.com");

			$ary=explode("|#|",$content);

			if(count($ary)!=2)die(json_encode(array('success'=>false,"msg"=>"编码错误".$code)));

			$openid=$ary[0];

			$rid=$ary[1];

			$condition='';

			if($sid){

				$sponsorprizes=pdo_fetchcolumn("SELECT prizes FROM ".tablename('j_shakelucky_sponsor')." WHERE id = '$sid'");

				$condition=' and prizeid in('.$sponsorprizes.')';

			}

			$item=pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_winner')." WHERE isprize=1 and from_user =:openid and rid=:rid $condition order by status asc , prizeid asc,id desc",array(":rid"=>$rid,":openid"=>$openid,));

			$prizelist=pdo_fetchall("SELECT id,title FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' order by id asc");

			$prizeAry=array();

			foreach($prizelist as $row){

				$prizeAry[$row['id']]=$row['title'];

			}

			$temp=array();

			foreach($item as $row){

				$temp[]=array(

					"id"=>$row['id'],

					"prize"=>$prizeAry[$row['prizeid']],

					"endtime"=>date("m/d H:i",$row['endtime']),

					"status"=>$row['status'],

				);

			}

			if(empty($item))die(json_encode(array('success'=>false,"msg"=>"没有中奖纪录")));

			die(json_encode(array('success'=>true,"item"=>$temp)));

		}

		if($operation=="dealprize"){

			$id=intval($_GPC['id']);

			$data=array(

				"status"=>1,

				"endtime"=>TIMESTAMP,

			);

			pdo_update("j_shakelucky_winner",$data,array('id'=>$id));

			die(json_encode(array('success'=>true,'time'=>date('m/d H:i',TIMESTAMP))));

		}

		

	}

	public function doMobileCancellation(){

		//手机兑奖

		global $_GPC, $_W;

		

		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

		$rid=intval($_GPC['rid']);

		if($operation=="ok"){

			$item = pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_reply')." WHERE weid = '".$_W['uniacid']."' and rid=:rid ",array(':rid'=>$rid));

			

			$num_playtime = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' ");

			$num_prizetime = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' and isprize=1");

			$num_gettime = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' and isprize=1 and status>0 ");

			$num_playmen = count(pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' group by from_user "));

			$num_prizeall=pdo_fetchcolumn("SELECT sum(remain) FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' and isprize=1 ");

			$num_prizeremain=pdo_fetchcolumn("SELECT sum(total) FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' and isprize=1 ");

			

			$prizelist=pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' and isprize=1 order by id asc");

		}

		include $this->template('cancellation');

	}

	public function doMobileCancellation2(){

		//手机兑奖

		global $_GPC, $_W;

		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

		$rid=intval($_GPC['rid']);

		$sid=intval($_GPC['sid']);

		if(!$sid || !$rid || !$_W['openid'])message('非法进入~！');

		$sponsor = pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_sponsor')." WHERE id = '$sid'");

		

		if($operation=="ok"){

			$item = pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_reply')." WHERE weid = '".$_W['uniacid']."'  ",array('rid'=>$rid));

			

			$num_playtime = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' ");

			$num_prizetime = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' and isprize=1");

			$num_gettime = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' and isprize=1 and status>0 ");

			$num_playmen = count(pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' group by from_user "));

			$num_prizeall=pdo_fetchcolumn("SELECT sum(remain) FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' and isprize=1 ");

			$num_prizeremain=pdo_fetchcolumn("SELECT sum(total) FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' and isprize=1 ");

			

			$prizelist=pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_award')." WHERE id in(".$sponsor['prizes'].")  order by id asc");

		}

		include $this->template('cancellation2');

	}

	public function doMobileEnter() {

		//手机游戏页面

		global $_GPC, $_W;

		//if(!$_W['openid'])message('抱歉，只能用微信打开!','','error');

		$id=intval($_GPC['id']);

		//echo $_GPC['gamge_id'];

		$item = pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_reply')." WHERE rid = '".$id."' ");

		if(empty($item))message('活动已经结束了哦~！');

		if($item['status']==2)die(header("Location:".$this->createMobileUrl("result",array('id'=>$id))));

		if($item['status']==3)message('还没开始摇哦~不要心急~！',$this->createMobileUrl("result",array('id'=>$id)),'error');

		if($item['starttime']>TIMESTAMP)message('还没开始摇哦~不要心急~！',$this->createMobileUrl("result",array('id'=>$id)),'error');

		if($item['endtime']<TIMESTAMP)message('本轮活动已经结束了，下次再来吧！',$this->createMobileUrl("result",array('id'=>$id)),'error');

		$cfg = $this->module['config'];

		//***活动平台对接***//

		if($item['fid']){

			if(!$_W['openid'])message('请在公众平台触发进入游戏！');

			$condition="";

			if($item['fstatus'])$condition.=" and status=2 ";

			if($item['fattend'])$condition.=" and attend=1 ";

			$count= pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_activity_winner')." WHERE aid = '".$item['fid']."' and from_user='".$_W['openid']."' $condition");

			if(!intval($count))message('抱歉，您无法参与本次游戏！');

		}

		

		//***助力记录-借用鉴权***//

		//判断是否已经参与游戏，参与过的无法助力；

		$isjoin=0;

		if($cfg['is_sharehelp']){

			if($_W['openid'])$isjoin = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_winner')." WHERE from_user =:from_user ",array(":from_user"=>$_W['openid']));

			

			if(!$isjoin){

				if($_GPC['fid'] && !$_COOKIE['openid_oath']){

					setcookie("gamge_id",$id);

					setcookie("gamge_inventor",$_GPC['fid']);

					header("Location:".$this->createMobileUrl('autooath'));

					die();

				}

				if($_COOKIE['gamge_inventor'] && $_COOKIE['openid_oath']){

					//该会员是否已经助力其他人

					$count=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_invent')." WHERE rid =:id and from_user='".$_COOKIE['openid_oath']."'",array(":id"=>intval($id)));

					//被助力的人总助力次数

					$count2=pdo_fetchcolumn("SELECT count(*) FROM ".tablename('j_shakelucky_invent')." WHERE rid =:id and inventor='".$_COOKIE['gamge_inventor']."'",array(":id"=>intval($id)));

					if(!intval($count) && $count2<$item['sharehelp']){

						pdo_insert("j_shakelucky_invent",array('rid'=>$id,'weid'=>$_W['uniacid'],'from_user'=>$_COOKIE['openid_oath'],'inventor'=>$_COOKIE['gamge_inventor'],'createtime'=>TIMESTAMP,));

					}

				}

			}

		}

		//======//

		$statr_time=strtotime($item['gamestarttime']);

		$end_time=strtotime($item['gameendtime']);

		$now_time=strtotime(date("H:i",TIMESTAMP));

		if($statr_time>$now_time)message('游戏在每天的'.$item['gamestarttime']."到".$item['gameendtime']."哦。您来太早了，晚点再来吧！",$this->createMobileUrl("result",array('id'=>$id)),'error');

		if($end_time<$now_time)message('游戏在每天的'.$item['gamestarttime']."到".$item['gameendtime']."哦。您来太早了，晚点再来吧！",$this->createMobileUrl("result",array('id'=>$id)),'error');

		$profile=pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_winner')." WHERE from_user = '".$_W['openid']."' ");

		$keyword=pdo_fetchcolumn("SELECT name FROM ".tablename('rule')." WHERE id = '".$id."' ");

		if(empty($profile))$profile=j_member_fetch();

		include $this->template('index');

	}

	

	public function doMobileAutooath() {

		//借用并且同步数据

		global $_GPC, $_W;

		$cfg = $this->module['config'];

		$appid=$cfg['auto_appid'];

		$appsecret=$cfg['auto_appsecret'];

		$backurl=$_W['siteurl'];

		if(!$appid || !$appsecret)die(header("location:".$backurl));

		//echo $_COOKIE['gamge_inventor'];



		if(!isset($_GPC['code'])){

			$url=$_W['siteurl']."&back=".$backurl;

			header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect");

		}else{

			$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$_GPC['code']."&grant_type=authorization_code";

			load()->func('communication');

			$content = ihttp_get($url);

			$token = @json_decode($content['content'], true);

			$url="https://api.weixin.qq.com/sns/userinfo?access_token=".$token['access_token']."&openid=".$token['openid']."&lang=zh_CN";

			$content = ihttp_get($url);

			$profile = @json_decode($content['content'], true);

			setcookie('openid_oath',$profile['openid'],time() + 86400);

			

			die(header("location:".$this->createMobileUrl('enter',array('id'=>$_COOKIE['gamge_id']))));

		}

	}

	

	public function doMobileResult() {

		//这个操作被定义用来呈现 功能封面

		global $_GPC, $_W;

		$id=intval($_GPC['id']);

		if(!$_W['openid'])message('抱歉，只能用微信打开!','','error');

		$profile=j_member_fetch();

		$item = pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_reply')." WHERE rid = '".$id."' ");

		if(empty($item))die("错误！游戏已删除");

		if(empty($item))message('活动已经结束了哦~！');

		/*if($item['status']==2)message('活动已经结束了哦~！');

		if($item['status']==3)message('还没开始摇哦~不要心急~！');

		if($item['starttime']>TIMESTAMP)message('还没开始摇哦~不要心急~！');

		if($item['endtime']<TIMESTAMP)message('唉，本轮活动已经结束了，下次再来吧！');*/

		

		$list_prize=pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$id."' and isprize>0 ORDER BY remain asc,id asc");

		$prizeAry=array();

		$prizeSonAry=array();

		foreach($list_prize as $row){

			$prizeAry[$row['id']]=$row['title'];

			$prizeSonAry[$row['id']]=$row['sponsor'];

		}

		$list_isprize=pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$id."' and isprize=1 and nickname<>'' group by from_user ORDER BY id desc limit 0,10");

		$list_record=pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$id."' and isprize=1 and from_user='".$_W['openid']."'  ORDER BY id desc");

		

		include(MODULE_ROOT.'/phpqrcode.php');

		$codeurl=$_W['openid']."_.png";

		$value = $_W['openid']."|#|".$id;

		$str=urlencode(encrypt($value, 'E', "www.yfjs-design.com"));

		QRcode::png($str, $codeurl, "L", 10,1);

		

		include $this->template('result');

	}

	

	//================//

	public function doWebAjax() {

		global $_GPC, $_W;

		if(!$_W['isajax'])die();

		$id=intval($_GPC['id']);

		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

		if($operation=='delaward'){

			if($id){

				pdo_delete('j_shakelucky_award',array('id'=>$id));

				die(json_encode(array('success'=>true)));

			}

		}

		if($operation=="dealprize"){

			$rid=intval($_GPC['rid']);

			$status=intval($_GPC['status']);

			$data=array(

				"status"=>$status,

				"endtime"=>TIMESTAMP,

			);

			if(!$status)unset($data['endtime']);

			pdo_update("j_shakelucky_winner",$data,array('id'=>$id));

			die(json_encode(array('success'=>true,'time'=>date('m/d H:i',TIMESTAMP))));

		}

	}

	public function doWebJoiner() {

		//这个操作被定义用来呈现 规则列表

		global $_GPC, $_W;

		

		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

		$rid=intval($_GPC['id']);

		$uid=intval($_GPC['uid']);

		$item = pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_reply')." WHERE rid = :rid",array(':rid'=>$rid));

		$where="";

		$order="  id asc ";

		

		if($_GPC['keyword'])$where.=" and (nickname like '%".$_GPC['keyword']."%' or mobile like '%".$_GPC['keyword']."%' or realname like '%".$_GPC['keyword']."%' )";

		if($_GPC['status'])$where.=" and status ='".($_GPC['status']-1)."' ";

		if($_GPC['isprize'])$where.=" and isprize ='".($_GPC['isprize']-1)."' ";

		$list = pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."'  $where order by status asc");

		$listuser = pdo_fetchall("SELECT *,count(*) as joinertime FROM ".tablename('j_shakelucky_winner')." WHERE rid = '".$rid."' $where  group by from_user order by id desc");

		//---------------

		$prizelist=pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' order by id asc");

		$prizeAry=array();

		foreach($prizelist as $row){

			$prizeAry[$row['id']]=$row;

		}

		$cfg = $this->module['config'];

		if($operation=='delete'){

			if(!empty($uid)){

				pdo_delete('j_shakelucky_winner',array('id'=>$uid));

				message('操作成功！',$this->createWebUrl('joiner',array('id'=>$rid)), 'success');

			}

		}

		if (checksubmit('deleteall')){

			pdo_delete('j_shakelucky_winner', " from_user IN  ('".implode("','", $_GPC['select'])."')");

			message('操作成功！',$this->createWebUrl('joiner',array('id'=>$rid)), 'success');

		}

		include $this->template('joiner');

	}

	

	public function doWebSponsor() {

		//这个操作被定义用来呈现 规则列表

		global $_GPC, $_W;

		$rid=intval($_GPC['id']);

		$item = pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_reply')." WHERE rid = :rid",array(':rid'=>$rid));

		$prizelist=pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_award')." WHERE rid = '".$rid."' and isprize=1 order by id asc");

		

		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

		if ($operation == 'display') {

			$list = pdo_fetchall("SELECT * FROM ".tablename('j_shakelucky_sponsor')." WHERE weid = '{$_W['uniacid']}' and rid='".$rid."' order by id desc");

			

		} elseif ($operation == 'post') {

			$id = intval($_GPC['uid']);

			if(!empty($id)) {

				$category = pdo_fetch("SELECT * FROM ".tablename('j_shakelucky_sponsor')." WHERE id = '$id'");

			}

			if (checksubmit('submit')) {

				if (empty($_GPC['title'])) message('抱歉，请输入名称！');

				$data = array(

					'rid' => $rid,

					'weid' => $_W['uniacid'],

					'title' => $_GPC['title'],

					'password' => trim($_GPC['password']),

					'status' => intval($_GPC['status']),

					'prizes' => implode(',',$_GPC['prizes']),

				);

				if (!empty($id)) {

					unset($data['rid']);

					pdo_update('j_shakelucky_sponsor', $data, array('id' => $id));

				} else {

					pdo_insert('j_shakelucky_sponsor', $data);

					$id = pdo_insertid();

				}

				message('更新成功！', $this->createWebUrl('sponsor', array('op' => 'display','id' => $rid)), 'success');

			}

		} elseif ($operation == 'delete') {

			$id = intval($_GPC['uid']);

			$category = pdo_fetch("SELECT id FROM ".tablename('j_shakelucky_sponsor')." WHERE id = '$id'");

			if (empty($category)) {

				message('抱歉，不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display','id' => $rid)), 'error');

			}

			pdo_delete('j_shakelucky_sponsor', array('id' => $id,));

			message('删除成功！', $this->createWebUrl('sponsor', array('op' => 'display','id' => $rid)), 'success');

		}

		include $this->template('sponsor');

	}

	

}