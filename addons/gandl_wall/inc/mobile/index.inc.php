<?php
//session_start();
//$_SESSION['__:proxy:openid'] = 'oyIjYt9lQx9flMXl9F9NiAqrJd3g';
//debug
global $_W, $_GPC;

$this->_doMobileAuth();
$user=$this->_user;
$is_user_infoed=$this->_is_user_infoed;

$this->_doMobileInitialize();
$cmd=$this->_cmd;
$wall=$this->_wall;
$wall_status=$this->_wall_status;
$mine=$this->_mine;


$cmd=$_GPC['cmd']; // 请求命令

if($cmd=='help'){
	/**
	if($wall_status==2){
		returnError('活动已然结束，别白费劲了');
	}
	**/
	
	// 生成帮助完成后的跳转地址：如果有piid则转向红包页面，如果没有piid则转向红包墙主页
	$redirect=$_W['siteroot'] . 'app/' . substr($this->createMobileUrl('index',array('pid'=>pencode($wall['id']))), 2);
	$piid = $_GPC['piid'];
	if(!empty($piid)){
		$piid=pdecode($piid);
		if(!empty($piid)){
			$redirect=$_W['siteroot'] . 'app/' . substr($this->createMobileUrl('piece',array('pid'=>pencode($wall['id']),'piid'=>pencode($piid))), 2);
		}
	}

	
	// 帮助码目前为用户参与记录ID加密
	$help = $_GPC['help'];
	if(empty($help)){
		return $this->returnError('你从哪来？要到哪去？',$redirect);
	}
	$help=pdecode($help);
	if(empty($help)){
		return $this->returnError('朋友，苦海无涯，回头是岸',$redirect);
	}
	$help = intval($help);
	if($help<=0){
		return $this->returnError('你是逗逼请来的黑客吗？',$redirect);
	}
	
	// 自己不能给自己助力
	if($help==$mine['id']){
		return $this->returnMessage('自己不能帮自己恢复哦~',$redirect,'info');
	}


	// 判断被帮用户是否存在
	$help_user = pdo_fetch("select * from " . tablename('gandl_wall_user') . " where uniacid=:uniacid and wall_id=:wall_id and id=:id ", array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':id' => $help));
	if(empty($help_user)){
		return $this->returnMessage('没成功，再试试呢~',$redirect,'info');
	}
	
	// 判断我是否帮过这个朋友
	$helped = pdo_fetch("select * from " . tablename('gandl_wall_user_help') . " where uniacid=:uniacid and wall_id=:wall_id and help=:help and helper_id=:helper_id ", array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':help' => $help,':helper_id'=>$mine['user_id']));
	if(!empty($helped)){
		return $this->returnMessage('您之前已经帮过Ta了哦~',$redirect,'info');
	}
	
	// 实施帮助
	// 重置被帮助者冷却时间
	$ret1=pdo_query('UPDATE '.tablename('gandl_wall_user') .' SET rob_next_time=0 where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':id' => $help));
	if(false===$ret1){
		return $this->returnMessage('失败啦，重新试试呢~',$redirect,'info');
	}
	// 记录帮助记录
	pdo_insert('gandl_wall_user_help', array(
		'uniacid'=>$_W['uniacid'],
		'wall_id'=>$wall['id'],
		'help'=>$help,
		'helper_id'=>$mine['user_id'],
		'create_time'=>time()
	));
	
	/**
	$status='success';
	$info='感谢您的帮助';
	include $this->template('help');
	exit();
	**/
	return $this->returnSuccess('感谢您的帮助~',$redirect);

}else if($cmd=='pieces'){
	// 筛选条件
	// 状态：1：有效，2：无效
	$status=$_GPC['status'];
	$status=($status==2)?2:1;

	$start=$_GPC['start'];// st(start):当前已加载记录数(按类型累计)
	if(!isset($start) || empty($start) || intval($start<=0)){
		$start=0;
	}else{
		$start=intval($start);
	}
	$limit=10;
	
	$more=1;
	
	
	// 如果是管理员，可以查看被关闭的内容，如果非管理员，不可以
	$filt='';
	/*** 应用户要求，关闭的内容管理员也不可见if(!($mine['admin']>0)){	***/
		$filt=' and (op is null or op=0 )';
	/***}***/

	$list=array();

	/** 审核相关 **/
	$list3=array();
	if($mine['admin']>0 && $start==0){ // 如果当前是管理员，第一页顶部需显示未审核的广告
		$list3 = pdo_fetchall("select id,user_id,model,total_amount,total_num,content,images,password,publish_time,hot_time,top_level,rob_start_time,rob_users,views,status,op,pay from " . tablename('gandl_wall_piece') . " where uniacid=:uniacid  and wall_id=:wall_id and op=-1 ORDER BY create_time DESC ",  array(':uniacid' => $_W['uniacid'],':wall_id'=>$wall['id']));
		$list=array_merge($list,$list3);  
	}


	if($status==1){
		// 有效信息排序规则：置顶级别，发布时间（发布越新越靠前）
		$list1 = pdo_fetchall("select id,user_id,model,total_amount,total_num,content,images,password,publish_time,hot_time,top_level,rob_start_time,rob_users,views,status,op from " . tablename('gandl_wall_piece') . " where uniacid=:uniacid  and wall_id=:wall_id and status=1 ".$filt." ORDER BY top_level DESC,create_time DESC limit ".$start.",".$limit." ",  array(':uniacid' => $_W['uniacid'],':wall_id'=>$wall['id']));
		// 如果有效的查完，则将状态切换到无效
		if(empty($list1) || count($list1)<$limit){
			$status=2;
			$start=0;
		}
		$list=array_merge($list,$list1);  
	}
	
	// 如果status为2，则继续查询无效的内容
	if($status==2){
		// 最多只显示30条无效的记录
		if($start<30){
			// 无效信息排序规则：发布时间（发布越新越靠前）
			$list2 = pdo_fetchall("select id,user_id,total_amount,total_num,content,images,password,publish_time,hot_time,top_level,rob_start_time,rob_users,views,status,op from " . tablename('gandl_wall_piece') . " where uniacid=:uniacid  and wall_id=:wall_id and status=2  ".$filt."  ORDER BY create_time DESC limit ".$start.",".$limit." ",  array(':uniacid' => $_W['uniacid'],':wall_id'=>$wall['id']));	
		}
		// 如果无效的也查完，则将更多置0
		if(empty($list2) || count($list2)<$limit){
			$more=0;
		}
		$list=array_merge($list,$list2);  
	}
	
	/** 审核相关 **/
	$start+=(count($list)-count($list3));

	// 数据业务处理
	if(!empty($list)){
		 // 附加用户等信息
		$uids=array();
		foreach($list as $v){
			$uids[]=$v['user_id'];
		}
		$vp_users=$this->vp_users($uids, 'id,user_id,nickname,avatar');
		load()->model('mc');
		$users = mc_fetch($uids, array('nickname','avatar'));
		for($i=0;$i<count($list);$i++){
			$uid=$list[$i]['user_id'];
			$vp_user=$vp_users[$uid];
			if(!empty($vp_user) && !empty($vp_user['nickname'])){
				$vp_user['avatar']=VP_IMAGE_URL($vp_user['avatar']);
				$list[$i]['_user']=$vp_user;
			}else{
				$user=$users[$uid];
				$user['avatar']=VP_AVATAR($user['avatar'],'s');
				$list[$i]['_user']=$user;
			}
			$list[$i]['_url']=$_W['siteroot'] . 'app/' . substr($this->createMobileUrl('piece',array('pid'=>pencode($wall['id']),'piid'=>pencode($list[$i]['id']))), 2);
			if(!empty($list[$i]['images'])){
				$images=iunserializer($list[$i]['images']);
				for($j=0;$j<count($images);$j++){
					$images[$j]=VP_IMAGE_URL($images[$j],'s');
				}
				$list[$i]['images']=$images;
			}
			/**
			if(!empty($list[$i]['password'])){
				$list[$i]['_password']=1;
				$list[$i]['password']='';
			}
			**/
		}
	}

	$this->returnSuccess('',array(
		'status'=>$status,
		'start'=>$start,
		'more'=>$more,
		'list'=>$list,
		'now'=>time()// 下传递服务器时间用于倒计时
	));

}else if($cmd=='rank_piece'){
	// 获取土豪榜
	$list = pdo_fetchall("select id,user_id,send_times,send_total,send_last_time from " . tablename('gandl_wall_user') . " where uniacid=:uniacid  and wall_id=:wall_id and send_total>0 and black=0 ORDER BY send_total DESC limit 0,20 ",  array(':uniacid' => $_W['uniacid'],':wall_id'=>$wall['id']));

	// 数据业务处理
	if(!empty($list)){
		 // 附加用户信息
		$uids=array();
		foreach($list as $v){
			$uids[]=$v['user_id'];
		}
		$vp_users=$this->vp_users($uids, 'id,user_id,nickname,avatar,who,home');
		load()->model('mc');
		$users = mc_fetch($uids, array('nickname','avatar'));
		for($i=0;$i<count($list);$i++){
			$uid=$list[$i]['user_id'];
			$vp_user=$vp_users[$uid];
			if(!empty($vp_user) && !empty($vp_user['nickname'])){
				$vp_user['avatar']=VP_IMAGE_URL($vp_user['avatar']);
				$list[$i]['_user']=$vp_user;
			}else{
				$user=$users[$uid];
				$user['avatar']=VP_AVATAR($user['avatar'],'s');
				$list[$i]['_user']=$user;
			}
		}
	}

	include $this->template('rank_piece');
}else if($cmd=='rank_rob'){
	// 获取抢钱榜
	$list = pdo_fetchall("select id,user_id,rob_times,rob_total,rob_last_time from " . tablename('gandl_wall_user') . " where uniacid=:uniacid  and wall_id=:wall_id and rob_total>0 and black=0 ORDER BY rob_total DESC limit 0,20 ",  array(':uniacid' => $_W['uniacid'],':wall_id'=>$wall['id']));

	// 数据业务处理
	if(!empty($list)){
		 // 附加用户信息
		$uids=array();
		foreach($list as $v){
			$uids[]=$v['user_id'];
		}
		load()->model('mc');
		$users = mc_fetch($uids, array('nickname','avatar'));
		for($i=0;$i<count($list);$i++){
			$user=$users[$list[$i]['user_id']];
			$user['avatar']=VP_AVATAR($user['avatar'],'s');
			$list[$i]['_user']=$user;
		}
	}

	include $this->template('rank_rob');
}else{

	if($wall['static']==1){
		// 统计访问量和发出现金数【缓存:首页每刷新N次，重新统计一次】
		$wall_refreshs_cache_key='gandl_wall_wall_refreshs:'.$wall['id'];
		$wall_refreshs_cache = cache_load($wall_refreshs_cache_key);
		if(empty($wall_refreshs_cache) || $wall_refreshs_cache==0){
			$wall_refreshs_cache=1;
		}else{
			$wall_refreshs_cache+=1;
		}

		$wall_static_cache_key='gandl_wall_wall_static:'.$wall['id'];
		$wall_static_cache = cache_load($wall_static_cache_key);
		if(empty($wall_static_cache) || empty($wall_static_cache['views']) || $wall_refreshs_cache>=5){ // 首页每刷新5次,重新统计一次
			$static=pdo_fetch("select sum(total_amount) as money,sum(views) as views from " . tablename('gandl_wall_piece') . " where status>0 and uniacid=:uniacid  and wall_id=:wall_id ",  array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id']));
			$online=pdo_fetchcolumn("select COUNT(id) from " . tablename('gandl_wall_user') . " where  uniacid=:uniacid  and wall_id=:wall_id and last_active_time>:last_active_time ", array(':uniacid' => $_W['uniacid'],':wall_id' => $wall['id'],':last_active_time'=>time()-3600)); // 1小时内活动过的用户认为是在线
			
			$wall_static_cache=array(
				'views'=>$static['views']+$wall['fake_user'],
				'money'=>($static['money']+$wall['fake_money'])/100,
				'online'=>$online+$wall['fake_online'],
			);

			cache_write($wall_static_cache_key, $wall_static_cache);

			$wall_refreshs_cache=0;
		}
		cache_write($wall_refreshs_cache_key, $wall_refreshs_cache);
	}




	// 生成我的专属分享链接
	$share_url=$_W['siteroot'] . 'app/' . substr($this->createMobileUrl('index',array('pid'=>pencode($wall['id']),'src'=>pencode($mine['id']))), 2);

	include $this->template('index');
}


?>