<?php
/**
 * 美图秀吧模块微站定义
 *
 */

defined('IN_IA') or exit('Access Denied');	

class Yuexiage_communityModuleSite extends WeModuleSite {
	
	public function doMobileCommunity() {
		//这个操作被定义用来呈现 功能封面
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		load()->func('tpl');
		//幻灯片
		$params = array();
		$list = pdo_fetchall("SELECT * FROM ".tablename('yuexiage_community_slide')." WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC, uniacid DESC", $params);
		
		//置顶标签
		$condition .= " AND deleted = 0";
		$condition .= " AND status = 1";
		$condition .= " AND top = 1";
		$tabs_top = pdo_fetchall("SELECT id,name,thumb FROM ".tablename('yuexiage_community_tabs')." WHERE weid = '{$_W['uniacid']}' $condition ORDER BY  id DESC ", $params);	

		$pindex = max(1, intval($_GPC['page']));
		$psize = 5;

		$condition = "";
		
		$condition .= " AND status = 1";
		if($_GPC['keyword']){
			$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
		}

		
		$contents = pdo_fetchall("SELECT id,title,description,thumb,author,createtime,click,linkurl,hits FROM ".tablename('yuexiage_community_contents')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY  createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);	
		include $this->template('index');
	}

	public function doMobileAjax_top(){
		global $_GPC, $_W;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 5;
		$condition = "";
		
		$condition .= " AND status = 1";

		if($_GPC['order']=='choice'){
			$condition .= " AND ishot = 1";
			$contents = pdo_fetchall("SELECT id,title,description,thumb,author,createtime,click,linkurl,hits FROM ".tablename('yuexiage_community_contents')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY  displayorder DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);	
		}

		if($_GPC['order']=='createtime'){
			$contents = pdo_fetchall("SELECT id,title,description,thumb,author,createtime,click,linkurl,hits FROM ".tablename('yuexiage_community_contents')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY  createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);	
		}

		if($_GPC['order']=='click'){
			$contents = pdo_fetchall("SELECT id,title,description,thumb,author,createtime,click,linkurl,hits FROM ".tablename('yuexiage_community_contents')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY  click DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);	
		}


		if(count($contents)){
			foreach ($contents as $key =>$value) {
				$contents[$key]['thumb'] = tomedia($value['thumb']);
				$contents[$key]['createtime'] = date("Y-m-d",$value['createtime']);
			}
			echo json_encode($contents);
		}else{
			echo "0";
		}
		
	}

	public function doMobileTabs_list(){
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$condition .= " AND deleted = 0";
		$condition .= " AND status = 1";
		$condition .= " AND id = $id";
		$tab = pdo_fetch("SELECT * FROM ".tablename('yuexiage_community_tabs')." WHERE weid = '{$_W['uniacid']}' $condition", $params);	
		
		$condition = "";
		$condition .= " AND ltc.name = '".$tab['name']."'";
		$condition .= " AND lc.status = 1";
		
		$order = $_GPC['order']?$_GPC['order']:'click';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 5;
		$cids = pdo_fetchall("SELECT lc.id,lc.title,lc.description,lc.thumb,lc.author,lc.createtime,lc.click,lc.linkurl,lc.hits FROM ".tablename('yuexiage_community_contents')." lc LEFT JOIN ".tablename('yuexiage_community_tabs_contents')." 
			ltc ON lc.id = ltc.cid WHERE lc.uniacid = '{$_W['uniacid']}' $condition ORDER BY  lc.".$order." DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
		$tab['contents'] = $cids;

		$cid_num = pdo_fetchall("SELECT lc.* FROM ".tablename('yuexiage_community_contents')." lc LEFT JOIN ".tablename('yuexiage_community_tabs_contents')." 
			ltc ON lc.id = ltc.cid WHERE lc.uniacid = '{$_W['uniacid']}' $condition", $params);

		$tab['num'] = count($cid_num);

		if($_W['isajax']){
			if(count($cids)){
				foreach ($cids as $key =>$value) {
					$cids[$key]['thumb'] = tomedia($value['thumb']);
					$cids[$key]['createtime'] = date("Y-m-d",$value['createtime']);
				}
			echo json_encode($cids);
			}else{
				echo "0";
			}
		}else{
			include $this->template('tabs_list');
		}
		
	}

	public function doMobileUser(){
		global $_GPC, $_W;
		load()->model('mc');
		$condition .= " AND createby = :createby AND status = 1";
		$params[':createby'] = $_GPC['uid']?$_GPC['uid']:$_W['member']['uid'];

		$contents = pdo_fetchall("SELECT * FROM ".tablename('yuexiage_community_contents')." WHERE uniacid='{$_W['uniacid']}' $condition ",$params);
		
		$uid = $_GPC['uid']?$_GPC['uid']:$_W['member']['uid'];

		$user = mc_fetch($uid, array('nickname', 'mobile','avatar','credit1'));
		if ($_W['member']['uid'] || $_GPC['uid']) {
			include $this->template('user');
		}else{
			checkauth();
		}

		
	}

	public function doMobileMore(){
		global $_GPC, $_W;
		$condition .= " AND deleted = :deleted";
		$params[':deleted'] = '0';
		$condition .= " AND status = :status";
		$params[':status'] = '1';
		$condition .= " AND sys = :sys";
		$params[':sys'] = '1';
		$tabs = pdo_fetchall("SELECT name,id FROM ".tablename('yuexiage_community_tabs')." WHERE weid = '{$_W['uniacid']}' $condition", $params);
		if(count($tabs)){
			foreach ($tabs as $value) {
				$condition = "";
				$condition .= " AND lc.status = 1";
				$condition .= " AND ltc.name = '".$value['name']."'";
				$con = pdo_fetchall("SELECT lc.id,lc.thumb FROM ".tablename('yuexiage_community_contents')." lc LEFT JOIN ".tablename('yuexiage_community_tabs_contents')." 
			ltc ON lc.id = ltc.cid WHERE lc.uniacid = '{$_W['uniacid']}' $condition", array());
				if(!empty($con)){
					$cons[$value['name']] = $con;
					$cons[$value['name']]['count'] = count($con);
					$cons[$value['name']]['id'] = $value['id'];
				}
				
			}
		}

		include $this->template('more');
	}


	public function doMobileDelContent(){
		global $_GPC, $_W;
		if ($_W['isajax']) {
			$result = pdo_update('yuexiage_community_contents', array('status' => 0), array('id' => $_GPC['id']));
			echo $result;
		}

	}

	public function doMobileDelComment(){
			global $_GPC, $_W;
			if ($_W['isajax']) {
				$result = pdo_update('yuexiage_community_comments', array('status' => 0), array('id' => $_GPC['id']));
				echo $result;
			}
		}

	public function TrimArray($Input){
	  
	    if (!is_array($Input))
	        return trim($Input);
	    return array_map(array("Yuexiage_communityModuleSite","TrimArray"), $Input);
	}

	public function doMobileCheckaccess(){
		global $_GPC, $_W;
		
		if($_W['member']['uid']==''){
			$res['error']='1';
			exit;
		}
		if($_GPC['type']==''){
			$res['error']='2';
			exit;
		}
		$exist = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('yuexiage_community_blacklist')." WHERE uniacid = '{$_W['uniacid']}' AND uid='{$_W['member']['uid']}' AND status=1  limit 1 ", $params);
		if($exist){
			$res['error']='5';
			echo json_encode($res);
			exit;
		}else{
			$res['success']='1';
			echo json_encode($res);
		}
	}

	public function doWebBlacklist () {
		global $_GPC, $_W;
		load()->func('tpl');
		$do = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if($do == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$condition = '';
			$params = array();
			if ($_GPC['status'] == '1' || $_GPC['status']== '0') {
				$condition .= " AND status = :status";
				$params[':status'] = $_GPC['status'];
			}

			$list = pdo_fetchall("SELECT * FROM ".tablename('yuexiage_community_blacklist')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yuexiage_community_blacklist') . " WHERE uniacid = '{$_W['uniacid']}' $condition");
			$pager = pagination($total, $pindex, $psize);
		}elseif($do == 'post'){
			$id = intval($_GPC['id']);
			if (checksubmit('search')) {
				$search_nickname = trim($_GPC['search_nickname']);
				$userinfo = pdo_fetchall('SELECT uid,nickname FROM ' . tablename('mc_members') . " WHERE uniacid = '{$_W['uniacid']}' AND (nickname='{$search_nickname}' or uid='{$search_nickname}') ");
				var_dump('SELECT uid,nickname FROM ' . tablename('mc_members') . " WHERE uniacid = '{$_W['uniacid']}' AND (nickname='{$search_nickname}' or uid='{$search_nickname}') ");
			}
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM ".tablename('yuexiage_community_blacklist')." WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，内容不存在或是已经删除！', '', 'error');
				}
			}
			if (checksubmit('submit')) {

				if (empty($_GPC['nickname'])) {
					message('昵称不能为空，请输入昵称！');
				}
				if (empty($_GPC['uid'])) {
					message('UID不能为空!');
				}

				$data = array(
					'uniacid' => $_W['uniacid'],
					'status' => $_GPC['status'],
					'nickname'=>$_GPC['nickname'],
					'time' => TIMESTAMP,
					'uid'=>$_GPC['uid']
				);
				
				$exist = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('yuexiage_community_blacklist')." WHERE uid = :uid" , array(':uid' => $_GPC['uid']));

				if($exist){
					unset($data['time']);
					pdo_update('yuexiage_community_blacklist', $data, array('uid' => $_GPC['uid']));
				}else{
					if (empty($id)) {
					pdo_insert('yuexiage_community_blacklist', $data);
					$aid = pdo_insertid();
					} else {
						unset($data['time']);
						pdo_update('yuexiage_community_blacklist', $data, array('id' => $id));
					}
				}

				
				message('内容更新成功！', $this->createWebUrl('blacklist', array('op' => 'display')), 'success');
			}
		} 
		include $this->template('blacklist');
	}

	private function get_code($id, $appid) {
		global $_GPC, $_W;
		$url =$_W['siteroot'].$this->createMobileUrl('detail', array('id'=>$id)).'&from='.$_GPC['from'];
		$oauth2_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . urlencode($url) . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
		header("location: $oauth2_url");
		exit();
	}

	public function get_openid($id, $code, $appid, $appsecret) {
		global $_GPC, $_W;
		load()->func('communication');
		$oauth2_code = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $appsecret . "&code=" . $code . "&grant_type=authorization_code";
		$content = ihttp_get($oauth2_code);
		$token = @json_decode($content['content'], true);
		if (!empty($token) && is_array($token) && $token['errmsg'] == 'invalid code') {
			$this->get_code($id, $appid);
		}
		if (empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
			message('未获取到 openid , 请刷新重试!');
		}
		return $token['openid'];
	}
	public function filter($text){
		$cfg = $this->module['config'];
		$filter = array_filter($this->TrimArray(explode(',', $cfg['filter'])));
		foreach($filter as $val){
			if(stristr($text,$val)){
				return false;
			}
		}
		return true;
	}
	public function doMobileDetail(){
		global $_GPC, $_W;

		load()->model('mc');
		$id = intval($_GPC['id']);
		$row = pdo_fetch("SELECT * FROM ".tablename('yuexiage_community_contents')." WHERE id = :id", array(':id' => $id));
		if (empty($row)) {
			message('抱歉，文章不存在或是已经被删除！');
		}

		$click=($row['click']+1);
		pdo_update('yuexiage_community_contents', array('click' => $click), array('id' => $_GPC['id']));

		$user = mc_fetch($row['createby'], array('uid','nickname', 'mobile','avatar'));
		$tabs = pdo_fetchall("SELECT name FROM ".tablename('yuexiage_community_tabs_contents')." WHERE cid = :id", array(':id' => $id));
		$hits = pdo_fetchall("SELECT * FROM ".tablename('yuexiage_community_hit')." WHERE cid = :id ORDER BY hits DESC limit 6", array(':id' => $id));
		$comments = pdo_fetchall("SELECT * FROM ".tablename('yuexiage_community_comments')." WHERE cid = :id AND status=:status ORDER BY id ASC" , array(':id' => $id,':status'=>1));
		
		$cfg = $this->module['config'];
		//管理员
		$admin = $this->TrimArray(explode(',', $cfg['admin']));

		$us = mc_fetch($_W['member']['uid'], array('nickname'));

		if ($cfg['credit']) {
			$article = pdo_fetch('SELECT id, credit,createby FROM ' . tablename('yuexiage_community_contents') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
			$credit = iunserializer($article['credit']) ? iunserializer($article['credit']) : array();
			$touid = $_W['member']['uid'];
			$formuid = $touid;
			if($touid){
				if($credit['status'] == 1){//赠送积分
					$limit = $credit['limit'];
					$share = $credit['share'];
					$accumulate = $credit['shared_credit'];
					$handsel = array('module' => 'yuexiage_community', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'click', 'credit_value' => $credit['click'], 'credit_log' => '阅读,赠送积分');
				}
				elseif($credit['status'] == 0){//继承
					$limit = $cfg['limit_credit'];
					$share = $cfg['share_credit'];
					$accumulate = $cfg['shared_credit'];
					$handsel = array('module' => 'yuexiage_community', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'click', 'credit_value' => $cfg['chick_credit'], 'credit_log' => '阅读,赠送积分[系统默认]');
			
				}
			}
			
			//是否是集阅读
			if($_GPC['from'] == 'timeline' || $_GPC['from'] == 'groupmessage' || $_GPC['from'] == 'singlemessage'){
				switch($_GPC['from']){
					case 'timeline':
						$from = "朋友圈";
					break;
					case 'groupmessage':
						$from = "微信群";
					break;
					case 'singlemessage':
						$from = "好友分享";
					break;
				}
				//通过朋友圈，微信群，好友分享来的阅读
				if($_GPC['code']){
					$openid =  $this->get_openid($id,$_GPC['code'],$cfg['appid'],$cfg['appsecret']);
					$_W['openid']=$openid;
				}elseif(empty($_W['openid'])){
					$this->get_code($id,$cfg['appid']);
				}
				$sql = 'SELECT * FROM '. tablename('yuexiage_community_accumulate') . 'WHERE openid= :openid AND itemid=:itemid';
				$param[':openid']=$_W['openid'];
				$param[':itemid']=$id;
				$result =  pdo_fetch($sql,$param);
				if(!$result){
					//如果当前openid没有贡献过积分
					$accumulate_handsel = array('module' => 'yuexiage_community', 'sign' => md5(iserializer(array('id' => $id,'openid'=>$_W['openid']))), 'action' => 'accumulate', 'credit_value' => $accumulate, 'credit_log' => '集阅读赠送积分');
					$status = mc_handsel($row['createby'], $_W['openid'], $accumulate_handsel, $_W['uniacid']);
					$data["itemid"]=$id;
					$data["openid"]=$_W['openid'];
					$msg = "感谢来自".$from.'的朋友为当前作者贡献了'.$accumulate.'点积分';
					pdo_insert('yuexiage_community_accumulate', $data);
				}
			}

			$total = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('mc_handsel') . ' WHERE uniacid = :uniacid AND module = :module AND sign = :sign AND touid = :touid', array(':touid'=>$touid,':uniacid' => $_W['uniacid'], ':module' => 'yuexiage_community', ':sign' => $handsel['sign']));
			if(($total >= $limit) || (($total + $handsel['credit_value']) > $limit)) {
				
			}else{
				if (!$_GPC['action']) {
					$status = mc_handsel($touid, $formuid, $handsel, $_W['uniacid']);
				}
			}
		}

		if ($_W['isajax']) {
			checkauth();
			$handsel = array('module' => 'yuexiage_community', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'share', 'credit_value' => $share, 'credit_log' => '分享,赠送积分');
			if(($total >= $limit) || (($total + $handsel['credit_value']) > $limit)) {
				
			}else{
				if ($_GPC['action']=='share') {
					$status = mc_handsel($touid, $formuid, $handsel, $_W['uniacid']);
				}
			}
			
		}else{
			include $this->template('detail');
		}
		
	}
	public function doMobileHits(){
		global $_GPC, $_W;
		if($_W['isajax']) {
			$hit = pdo_fetch("SELECT hits FROM ".tablename('yuexiage_community_hit')." WHERE cid = :id AND uid=:uid", 
					array(':id' => $_GPC['id'],':uid'=>$_GPC['uid']));

			$data['uid']=$_GPC['uid'];
			$data['cid']=$_GPC['id'];
			$data['hits']=1;
			if($hit["hits"] ==""){
				pdo_insert('yuexiage_community_hit', $data);
				echo 1;
			}else{
				$hit = ++$hit["hits"];
				$result = pdo_update('yuexiage_community_hit', array('hits' => $hit), array('cid' => $_GPC['id'],'uid'=> $_GPC['uid']));
				echo $result;
			}

			$hit_c = pdo_fetch("SELECT hits FROM ".tablename('yuexiage_community_contents')." WHERE id = :id", 
					array(':id' => $_GPC['id']));
			$hits = ++$hit_c["hits"];
			pdo_update('yuexiage_community_contents',array('hits'=>$hits),array('id' => $_GPC['id']));
		
		
		$cfg = $this->module['config'];
		$id = intval($_GPC['id']);
		if ($cfg['credit']) {
			$article = pdo_fetch('SELECT id, credit FROM ' . tablename('yuexiage_community_contents') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
			$credit = iunserializer($article['credit']) ? iunserializer($article['credit']) : array();
			$touid = $_W['member']['uid'];
			$formuid = $touid;
			if($touid){
				if($credit['status'] == 1){//赠送积分
					$limit = $credit['limit'];
					$hit = $credit['hit'];
					$handsel = array('module' => 'yuexiage_community', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'hit', 'credit_value' => $hit, 'credit_log' => '点赞,赠送积分');
				}
				elseif($credit['status'] == 0){//继承
					$limit = $cfg['limit_credit'];
					$hit = $cfg['hits_credit'];
					$handsel = array('module' => 'yuexiage_community', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'hit', 'credit_value' => $hit, 'credit_log' => '点赞,赠送积分[系统默认]');
				}
			}
			
			$total = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('mc_handsel') . ' WHERE uniacid = :uniacid AND module = :module AND sign = :sign AND touid = :touid', array(':touid'=>$touid,':uniacid' => $_W['uniacid'], ':module' => 'yuexiage_community', ':sign' => $handsel['sign']));
			if(($total >= $limit) || (($total + $handsel['credit_value']) > $limit)) {

			}else{
				$status = mc_handsel($touid, $formuid, $handsel, $_W['uniacid']);
			}
			
		}
		
		
		}
		
	}
	
	function doMobileComments(){
		global $_GPC, $_W;
		if($_W['isajax']) {
			if (empty($_GPC['content'])) {
				return 0;
			}
			if (empty($_W['member']['uid'])) {
				return 0;
			}
			if($_GPC['coment_id']){
				$comment=pdo_fetch("SELECT * FROM ".tablename('yuexiage_community_comments')." WHERE id = :id",array(":id"=>$_GPC['id']));
			}
			$data['cid']=$_GPC['cid'];
			$data['ownerid']=$_W['member']['uid'];
			$data['owner']=$_W['fans']['nickname'];
			$data['commentdate']=date("Y-m-d H:i:s");
			$data['commenttext']=$_GPC['coment_pre'].$_GPC['content'];
			$str = preg_replace('/<script>.*?<\/script>/is', '', $data['commenttext']);
			$data['commenttext']=strip_tags($str);
			$data['level']=isset($comment["level"])?($comment["level"]+1):0;
			$data['parent']=isset($comment["parent"])?($comment["parent"]+1):0;
			$data['status']=1;
			if(!$this->filter($data['commenttext'])){
				return false;
			}
			echo pdo_insert("yuexiage_community_comments",$data);
			
			
			$cfg = $this->module['config'];
			$id = intval($_GPC['cid']);
			if ($cfg['credit']) {
				$article = pdo_fetch('SELECT id, credit FROM ' . tablename('yuexiage_community_contents') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
				$credit = iunserializer($article['credit']) ? iunserializer($article['credit']) : array();
				$touid = $_W['member']['uid'];
				$formuid = $touid;
				if($touid){
					if($credit['status'] == 1){//赠送积分
						$limit = $credit['limit'];
						$comm = $credit['comm'];
						$handsel = array('module' => 'yuexiage_community', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'comm', 'credit_value' => $comm, 'credit_log' => '评论,赠送积分');
					}
					elseif($credit['status'] == 0){//继承
						$limit = $cfg['limit_credit'];
						$comm = $cfg['comm_credit'];
						$handsel = array('module' => 'yuexiage_community', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'comm', 'credit_value' => $comm, 'credit_log' => '评论,赠送积分[系统默认]');
					}
				}
				


				$total = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('mc_handsel') . ' WHERE uniacid = :uniacid AND module = :module AND sign = :sign AND touid = :touid', array(':touid'=>$touid,':uniacid' => $_W['uniacid'], ':module' => 'yuexiage_community', ':sign' => $handsel['sign']));
				if(($total >= $limit) || (($total + $handsel['credit_value']) > $limit)) {
				
				}else{
					$status = mc_handsel($touid, $formuid, $handsel, $_W['uniacid']);
				}
				
			}
			
		}
	}

	function doMobileLogin(){
		global $_W, $_GPC;

		if($_W['isajax']){
			$sql = 'SELECT `uid`,`salt`,`password` FROM ' . tablename('mc_members') . ' WHERE `uniacid`=:uniacid';
			$pars = array();
			$pars[':uniacid'] = $_W['uniacid'];
			if(preg_match(REGULAR_MOBILE, $_GPC['username'])) {
				$sql .= ' AND `mobile`=:mobile';
				$pars[':mobile'] = $_GPC['username'];
			} else {
				$sql .= ' AND `email`=:email';
				$pars[':email'] = $_GPC['username'];
			}
			$user = pdo_fetch($sql, $pars);
			if(empty($user)) {
				echo 0;
			}
			if(_mc_login($user)) {
				echo 1;
			}

		}else{
			$header = 1;
			include $this->template('login');
		}
		
	}


	function doMobileAddcontent(){
		global $_GPC, $_W;
		checkauth();
		if($_W['isajax']){
			$post=array();
			$post['title']=$_GPC['title'];
			$post['title']=html_entity_decode($post['title']);
			$str = preg_replace('/<script>.*?<\/script>/is', '', $post['title']);
			$post['title'] = strip_tags($str);

			$post['content']= $_GPC['description'];
			$post['content']=html_entity_decode($post['content']);
			$post['content'] = preg_replace('/<script>.*?<\/script>/is', '', $post['content']);
			

			$post['thumb']=$_GPC['img'];
			$post['source']=0;
			$post['status']=1;
			$post['createby']=$_W['member']['uid'];
			$post['createname']=$_W['member']['nickname'];
			$post['author']=$_W['member']['nickname'];
			$post['createtime']=TIMESTAMP;
			$post['uniacid']=$_W['uniacid'];
			
			if($post['title']=='' || $post['content']=="" || $post['thumb']==""){
				echo 0;
				return ;
			}
			if(!$this->filter($post['content'])){
				echo 0;
				return ;
			}
			pdo_insert("yuexiage_community_contents",$post);
			echo $newId = pdo_insertid();

			if(!empty($_GPC['tabs'])){
				foreach($_GPC['tabs'] as $val) {
					$in['cid'] = $newId;
					$in['name'] = $val;
					pdo_insert('yuexiage_community_tabs_contents', $in);
				}
			}




		$cfg = $this->module['config'];
		$id = $newId;
		if ($cfg['credit']) {
			$article = pdo_fetch('SELECT id, credit FROM ' . tablename('yuexiage_community_contents') . ' WHERE uniacid = :uniacid AND id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $id));
			$credit = iunserializer($article['credit']) ? iunserializer($article['credit']) : array();
			if(!empty($article) && $credit['status'] == 1 && $_W['member']['uid']) {
				$touid = $_W['member']['uid'];
				$formuid = CLIENT_IP;
				$limit = $credit['limit'];
				$credit_value = $credit['content'];
				$handsel = array('module' => 'yuexiage_community', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'content', 'credit_value' => $credit['content'], 'credit_log' => '添加内容,赠送积分');
			}else if($credit['status'] != 1 && $_W['member']['uid']){
				$touid = $_W['member']['uid'];
				$formuid = CLIENT_IP;
				$limit = $cfg['limit_credit'];
				$credit_value = $cfg['content_credit'];
				$handsel = array('module' => 'yuexiage_community', 'sign' => md5(iserializer(array('id' => $id))), 'action' => 'comm', 'credit_value' => $cfg['content_credit'], 'credit_log' => '添加内容,赠送积分[系统默认]');
			}


			$total = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('mc_handsel') . ' WHERE uniacid = :uniacid AND module = :module AND sign = :sign AND touid = :touid', array(':touid'=>$touid,':uniacid' => $_W['uniacid'], ':module' => 'yuexiage_community', ':sign' => $handsel['sign']));
			if(($total >= $limit) || (($total + $handsel['credit_value']) > $limit)) {
			
			}else{
				$status = mc_handsel($touid, $formuid, $handsel, $_W['uniacid']);
			}
			
		}


		}else{
			//置顶标签
			$condition .= " AND deleted = 0";
			$condition .= " AND status = 1";
			$condition .= " AND top = 1";
			$tabs_top = pdo_fetchall("SELECT id,name,thumb FROM ".tablename('yuexiage_community_tabs')." WHERE weid = '{$_W['uniacid']}' $condition ORDER BY  id DESC ", $params);	
			include $this->template('content');
		}
		
	}

	function doMobileUpload(){
		global $_GPC, $_W;
		load()->func('file');
		$base64_string = $_GPC['base64_string'];

	    $savename = uniqid().'.jpeg';//localResizeIMG压缩后的图片都是jpeg格式

	    $savepath = '../attachment/images/'.$_GPC['i'].'/'.date("Y").'/'.date("m").'/'.$savename; 
		@mkdirs(dirname($savepath));
	    $imgpath = 'images/'.$_GPC['i'].'/'.date("Y").'/'.date("m").'/'.$savename;

	    $image = $this->base64_to_img( $base64_string, $savepath );
	    //$img  = $this->base64_to_img( $base64_string, $imgpath );
	    if($image){
	        echo '{"status":1,"content":"上传成功","url":"'.$image.'","img":"'.$imgpath.'"}';
	    }else{
	        echo '{"status":0,"content":"上传失败"}';
	    } 
	}

	function base64_to_img( $base64_string, $output_file ) {
        $ifp = fopen( $output_file, "wb" ); 
        fwrite( $ifp, base64_decode( $base64_string) ); 
        fclose( $ifp ); 
        return( $output_file ); 
	} 


	function doMobileCheckauth() {
		global $_W;
		checkauth();
	}


	public function doWebSlides(){
		global $_GPC, $_W;
		load()->func('tpl');
		$do = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($do == 'display') {
			$_W['page']['title'] = '幻灯片管理- 幻灯片设置 - 功能组件';
			if (checksubmit('submit')) {
				if (!empty($_GPC['displayorder'])) {
					foreach ($_GPC['displayorder'] as $id => $displayorder) {
						pdo_update('yuexiage_community_slide', array('displayorder' => $displayorder), array('id' => $id));
					}
				}
				message('更新排序成功！', referer(), 'success');
			}
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$condition = '';
			$params = array();
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
			}
			$list = pdo_fetchall("SELECT * FROM ".tablename('yuexiage_community_slide')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY displayorder DESC, uniacid DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yuexiage_community_slide') . " WHERE uniacid = '{$_W['uniacid']}' $condition");
			$pager = pagination($total, $pindex, $psize);
		}
		if ($do == 'post') {
			$_W['page']['title'] = '幻灯片添加- 幻灯片设置 - 功能组件';
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM ".tablename('yuexiage_community_slide')." WHERE id = :id" , array(':id' => $id));
				if (empty($item)) {
					message('抱歉，幻灯片不存在或是已经删除！', '', 'error');
				}
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) {
					message('标题不能为空，请输入标题！');
				}

				$data = array(
					'uniacid' => $_W['uniacid'],
					'title' => $_GPC['title'],
					'url' => $_GPC['url'],
					'displayorder' => intval($_GPC['displayorder']),
				);
				if (!empty($_GPC['thumb'])) {
					$data['thumb'] = $_GPC['thumb'];
				}
				if (empty($id)) {
					pdo_insert('yuexiage_community_slide', $data);
				} else {
					pdo_update('yuexiage_community_slide', $data, array('id' => $id));
				}
				message('幻灯片更新成功！', $this->createWebUrl('slides', array('op' => 'display')), 'success');
			}
		}
		if ($do == 'delete') {
			$_W['page']['title'] = '幻灯片删除- 幻灯片设置 - 功能组件';
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id, thumb FROM ".tablename('yuexiage_community_slide')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，幻灯片不存在或是已经被删除！');
			}
			pdo_delete('yuexiage_community_slide', array('id' => $id));
			message('删除成功！', $this->createWebUrl('slides', array('op' => 'display')), 'success');
		}
		include $this->template('slide');
	}

	public function doWebContents() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_GPC, $_W;
		load()->func('tpl');
		$do = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if($do == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$condition = '';
			$params = array();
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND title LIKE :keyword";
				$params[':keyword'] = "%{$_GPC['keyword']}%";
			}
			if ($_GPC['status'] == '1' || $_GPC['status']== '0') {
				$condition .= " AND status = :status";
				$params[':status'] = $_GPC['status'];
			}

			if ($_GPC['source'] == '1' || $_GPC['source']== '0') {
				$condition .= " AND source = :source";
				$params[':source'] = $_GPC['source'];
			}

			$list = pdo_fetchall("SELECT * FROM ".tablename('yuexiage_community_contents')." WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY displayorder DESC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yuexiage_community_contents') . " WHERE uniacid = '{$_W['uniacid']}' $condition");
			$pager = pagination($total, $pindex, $psize);
		}elseif($do == 'post'){
			load()->func('file');
			$id = intval($_GPC['id']);
			if (!empty($id)) {
			$item = pdo_fetch("SELECT * FROM ".tablename('yuexiage_community_contents')." WHERE id = :id" , array(':id' => $id));
			$tabs = pdo_fetchall("SELECT * FROM ".tablename('yuexiage_community_tabs_contents')." WHERE cid = :cid" , array(':cid' => $id));
				if (empty($item)) {
					message('抱歉，文章不存在或是已经删除！', '', 'error');
				}
				$key = pdo_fetchall('SELECT content FROM ' . tablename('rule_keyword') . ' WHERE rid = :rid AND uniacid = :uniacid', array(':rid' => $item['rid'], ':uniacid' => $_W['uniacid']));
				if(!empty($key)) {
					$keywords = array();
					foreach($key as $row) {
						$keywords[] = $row['content'];
					}
					$keywords = implode(',', array_values($keywords));
				}
				$item['credit'] = iunserializer($item['credit']) ? iunserializer($item['credit']) : array();
				if(!empty($item['credit']['limit'])) {
					$credit_num = pdo_fetchcolumn('SELECT SUM(credit_value) FROM ' . tablename('mc_handsel') . ' WHERE uniacid = :uniacid AND module = :module AND sign = :sign', array(':uniacid' => $_W['uniacid'], ':module' => 'yuexiage_community', ':sign' => md5(iserializer(array('id' => $id)))));
					if(is_null($credit_num)) $credit_num = 0;
					$credit_yu = (($item['credit']['limit'] - $credit_num) < 0) ? 0 : $item['credit']['limit'] - $credit_num;
				}
			} else {
				$item['credit'] = array();
			}

			$total_tabs = pdo_fetchall('SELECT id,name FROM ' . tablename('yuexiage_community_tabs') . ' WHERE status = :status AND deleted = :deleted AND weid = :uniacid', 
																									array(':status' => 1,':deleted'=>0, ':uniacid' => $_W['uniacid']));
			if (checksubmit('submit')) {
				
				if (empty($_GPC['title'])) {
					message('标题不能为空，请输入标题！');
				}
				if (empty($_GPC['tabs_list'])) {
					message('请选择标签!');
				}

				$data = array(
					'uniacid' => $_W['uniacid'],
					'iscommend' => intval($_GPC['option']['commend']),
					'ishot' => intval($_GPC['option']['hot']),
					'title' => $_GPC['title'],
					'description' => $_GPC['description'],
					'content' => htmlspecialchars_decode($_GPC['content']),
					'incontent' => intval($_GPC['incontent']),
					'source' => $_GPC['source'],
					'author' => $_GPC['author'],
					'displayorder' => intval($_GPC['displayorder']),
					'linkurl' => $_GPC['linkurl'],
					'status' => $_GPC['status'],
					'advertisement_link' => $_GPC['advertisement_link'],
					'advertisement_img' => $_GPC['advertisement_img'],
					'createtime' => TIMESTAMP,
					'createby'=>$_W['uid'],
					'createname'=>$_W['username'],
					'click' => intval($_GPC['click'])
				);
				
				if (!empty($_GPC['thumb'])) {
					$data['thumb'] = $_GPC['thumb'];
				} elseif (!empty($_GPC['autolitpic'])) {
					$match = array();
					preg_match('/attachment\/(.*?)(\.gif|\.jpg|\.png|\.bmp)/', $_GPC['content'], $match);
					if (!empty($match[1])) {
						$data['thumb'] = $match[1].$match[2];
					}
				} else {
					$data['thumb'] = '';
				}
				$keyword = str_replace('，', ',', trim($_GPC['keyword']));
				$keyword = explode(',', $keyword);
				if(!empty($keyword)) {
					$rule['uniacid'] = $_W['uniacid'];
					$rule['name'] = '社区：' . $_GPC['title'] . ' 触发规则';
					$rule['module'] = 'news';
					$rule['status'] = 1;
					$keywords = array();
					foreach($keyword as $key) {
						$key = trim($key);
						if(empty($key)) continue;
						$keywords[] = array(
							'uniacid' => $_W['uniacid'],
							'module' => 'news',
							'content' => $key,
							'status' => 1,
							'type' => 1,
							'displayorder' => 1,
						);
					}
					$reply['title'] = $_GPC['title'];
					$reply['description'] = $_GPC['description'];
					$reply['thumb'] = $_GPC['thumb'];
					$reply['url'] = $this->createMobileUrl('detail', array('id' => $id));			
				}
				if(!empty($_GPC['credit']['status'])) {
				
					
					$credit['status'] = intval($_GPC['credit']['status']);
					$credit['limit'] = intval($_GPC['credit']['limit']) ? intval($_GPC['credit']['limit']) : message('请设置积分上限');
					$credit['share'] = intval($_GPC['credit']['share']) ? intval($_GPC['credit']['share']) : message('请设置分享时赠送积分多少');
					$credit['click'] = intval($_GPC['credit']['click']) ? intval($_GPC['credit']['click']) : message('请设置阅读时赠送积分多少');
					$credit['hit'] = intval($_GPC['credit']['hit']) ? intval($_GPC['credit']['hit']) : message('请设置点赞时赠送积分多少');
					$credit['comm'] = intval($_GPC['credit']['comm']) ? intval($_GPC['credit']['comm']) : message('请设置评论时赠送积分多少');
					$credit['shared_credit'] = intval($_GPC['credit']['shared_credit']) ? intval($_GPC['credit']['shared_credit']) : message('请设置集阅读时赠送积分多少');
					
					$data['credit'] = iserializer($credit);
				} else {
					$data['credit'] = iserializer(array('status' => 0, 'limit' => 0, 'share' => 0, 'click' => 0));
				}	
				if (empty($id)) {
					
					if(!empty($keywords)) {
						pdo_insert('rule', $rule);
						$rid = pdo_insertid();
						foreach($keywords as $li) {
							$li['rid'] = $rid;
							pdo_insert('rule_keyword', $li);
						}
						$reply['rid'] = $rid;
						pdo_insert('news_reply', $reply);
						$data['rid'] = $rid;
					}
					pdo_insert('yuexiage_community_contents', $data);
					$aid = pdo_insertid();
					$url = $this->createMobileUrl('detail', array('id' => $aid));
					pdo_update('news_reply', array('url' => $url), array('rid' => $rid));

					if(!empty($_GPC['tabs_list'])){
						foreach($_GPC['tabs_list'] as $val) {
							$in['cid'] = $aid;
							$in['name'] = $val;
							pdo_insert('yuexiage_community_tabs_contents', $in);
						}
					}

				} else {
					
					unset($data['createtime']);
					unset($data['createby']);
					pdo_delete('rule', array('id' => $item['rid'], 'uniacid' => $_W['uniacid']));
					pdo_delete('rule_keyword', array('rid' => $item['rid'], 'uniacid' => $_W['uniacid']));
					pdo_delete('news_reply', array('rid' => $item['rid']));
					pdo_delete('yuexiage_community_tabs_contents', array('cid' => $id));
					if(!empty($keywords)) {
						pdo_insert('rule', $rule);
						$rid = pdo_insertid();

						foreach($keywords as $li) {
							$li['rid'] = $rid;
							pdo_insert('rule_keyword', $li);
						}

						$reply['rid'] = $rid;
						pdo_insert('news_reply', $reply);
						$data['rid'] = $rid;
					} else {
						$data['rid'] = 0;
						$data['kid'] = 0;
					}
					$data['modifyid'] = $_W['uid'];
					$data['modify'] = $_W['username'];
					$data['lastmodified'] = TIMESTAMP;

					pdo_update('yuexiage_community_contents', $data, array('id' => $id));
					if(!empty($_GPC['tabs_list'])){
						foreach($_GPC['tabs_list'] as $val) {
							$in['cid'] = $id;
							$in['name'] = $val;
							pdo_insert('yuexiage_community_tabs_contents', $in);
						}
					}
				}
				message('内容更新成功！', $this->createWebUrl('contents', array('op' => 'display')), 'success');
			}
		} elseif($do == 'delete') {
			$id = intval($_GPC['id']);
			$data['status'] = intval($_GPC['status']);

			$row = pdo_fetch("SELECT id,rid,kid,thumb FROM ".tablename('yuexiage_community_contents')." WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，文章不存在或是已经被删除！');
			}
			
			$data['publishedid'] = $_W['uid'];
			$data['published'] = $_W['username'];
			$data['lastpublished'] = TIMESTAMP;
			pdo_update('yuexiage_community_contents', $data, array('id' => $id));

			pdo_update('rule', array('status' => intval($_GPC['status'])), array('id' => $row['rid']));
			message('操作成功！', referer(), 'success');
		}
		include $this->template('contents');
	}

	public function doWebGettabs(){
		global $_GPC, $_W;
		$item["value"] = pdo_fetchall("SELECT id,name FROM " . tablename('yuexiage_community_tabs') . " WHERE deleted = :deleted AND weid = :weid AND status = :status", array(':deleted' => 0,':weid'=>$_W['uniacid'],':status'=>1));
		return json_encode($item);
	}

	/**
	 * [gettree description]
	 * @param  [type]  $cate [类别数组]
	 * @param  integer $pid  [父类ID]
	 * @return [type]        [返回的无限极处理后的数组]
	 */
	public function gettree ($cate,$html='|—',$pid = 0,$level=0){
		$arr = array();
		foreach ($cate as $v) {
			if ($v['parent'] == $pid) {
				$v['level']  =  $level+1;
				$v['html']   =  str_repeat($html, $level);
				$arr[]       =  $v;
				$arr         =  array_merge($arr,self::gettree($cate, $html,$v['id'],$level+1));
			}
		}

		return $arr;
	}


	public function qqcode($message = '', $size = '24px') {
		$qqcode = array(
			"/微笑","/撇嘴","/色","/发呆","/得意","/流泪","/害羞","/闭嘴","/睡","/大哭","/尴尬","/发怒","/调皮","/呲牙","/惊讶","/难过","/酷","/冷汗","/抓狂","/吐","/偷笑","/可爱","/白眼","/傲慢","/饥饿","/困","/惊恐","/流汗","/憨笑","/大兵","/奋斗","/咒骂","/疑问","/嘘","/晕","/折磨","/衰","/:!!!","/敲打","/再见","/擦汗","/抠鼻","/鼓掌","/糗大了","/坏笑","/左哼哼","/右哼哼","/哈欠","/鄙视","/委屈","/快哭了","/阴险","/亲亲","/吓","/可怜","/菜刀","/西瓜","/啤酒","/篮球","/乒乓","/咖啡","/饭","/猪头","/玫瑰","/凋谢","/示爱","/爱心","/心碎","/蛋糕","/闪电","/炸弹","/刀","/足球","/瓢虫","/便便","/月亮","/太阳","/礼物","/拥抱","/强","/弱","/握手","/胜利","/抱拳","/勾引","/拳头","/差劲","/爱你","/NO","/OK","/爱情","/飞吻","/跳跳","/发抖","/怄火","/转圈","/磕头","/回头","/跳绳","/挥手","/激动","/街舞","/献吻","/左太极","/右太极"
		);
		foreach ($qqcode as $index => $emotion) {
			$message = str_replace($emotion, '<img style="width:'.$size.';vertical-align:middle;" src="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/'.$index.'.gif" />', $message);
		}
		return $message;
	}

	public function doWebComments() {
		global $_GPC, $_W;
		load()->func('tpl');
		$do = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		
		if($do == 'display') {
			
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$condition = '';
			$params = array();
			$se='';
			if (!empty($_GPC['keyword'])) {
				$condition .= " AND commenttext LIKE :keyword";
				$params[':keyword'] = "%{$_GPC['keyword']}%";
				$se = 1;
			}
			if ($_GPC['status'] == '1' || $_GPC['status'] == '0') {
				$condition .= " AND status = :status";
				$params[':status'] = $_GPC['status'];
				$se = 1;
			}

			$list = pdo_fetchall("SELECT * FROM ".tablename('yuexiage_community_comments')." WHERE id > 0 $condition ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, $params);
			
			if ($se != 1) {
				$list = $this->gettree($list);
			}

			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('yuexiage_community_comments') . " WHERE id > 0 $condition ", $params);
			$pager = pagination($total, $pindex, $psize);
		}elseif ($do == 'publish'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				//根据id获取当前标签信息
				$item = pdo_fetch("SELECT * FROM " . tablename('yuexiage_community_comments') . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，评论不存在或是已经删除！', '', 'error');
				}
			}
			$data['status'] = intval($_GPC['publish']);
			$data['publishedid'] = $_W['uid'];
			$data['published'] = $_W['username'];
			$data['lastpublished'] = TIMESTAMP;

			pdo_update("yuexiage_community_comments",$data, array('id' => $id));
			message('更新成功！', referer(), 'success');

		}elseif ($do == 'post'){
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				//根据id获取当前标签信息
				$item = pdo_fetch("SELECT * FROM " . tablename('yuexiage_community_comments') . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，评论不存在或是已经删除！', '', 'error');
				}
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['commenttext'])) {
					message('评论内容不能为空！');
				}

				$data = array(
					'commenttext' => $_GPC['commenttext'],
					'status'=>$_GPC['status'],
					'modifyid'=>$_W['uid'],
					'modify'=>$_W['username'],
					'lastmodified'=>TIMESTAMP
					);

				if (empty($id)) {
					message('数据错误！', '', 'error');
				} else {
					pdo_update('yuexiage_community_comments', $data, array('id' => $id));
				}
				message('更新成功！', $this->createWebUrl('comments', array('op' => 'display', 'id' => $id)), 'success');
			}

		}
		include $this->template('comments');
	}
	public function doWebUsers() {
		//这个操作被定义用来呈现 管理中心导航菜单
	}
	public function doWebTabs() {
		//这个操作被定义用来呈现 管理中心导航菜单
		global $_GPC, $_W;
		load()->func('tpl');
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'post') {
			//添加标签
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				//根据id获取当前标签信息
				$item = pdo_fetch("SELECT * FROM " . tablename('yuexiage_community_tabs') . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，标签不存在或是已经删除！', '', 'error');
				}
			}

			if (checksubmit('submit')) {
				if (empty($_GPC['tabsname'])) {
					message('请输入标签名称！');
				}
				$data = array(
					'weid' => intval($_W['uniacid']),
					'name' => $_GPC['tabsname'],
					'status'=>$_GPC['status'],
					'owner'=>$_W['username'],
					'ownerid'=>$_W['uid'],
					'datetime'=>date("Y-m-d H:i:s"),
					'top'=>$_GPC['top'],
					'sys'=>$_GPC['sys'],
					'thumb'=>$_GPC['thumb'],
					'deleted'=>0
					);
				if (empty($id)) {
					
						pdo_insert('yuexiage_community_tabs', $data);
						$id = pdo_insertid();
					
				} else {
					
						unset($data['datetime']);
						pdo_update('yuexiage_community_tabs', $data, array('id' => $id));
					
				}
				message('标签更新成功！', $this->createWebUrl('tabs', array('op' => 'display', 'id' => $id)), 'success');
			}

		}else if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 15;
			$condition = ' WHERE `weid` = :weid ';
			$params = array(':weid' => $_W['uniacid']);
			if (!empty($_GPC['keyword'])) {
				$condition .= ' AND `name` LIKE :name';
				$params[':name'] = '%' . trim($_GPC['keyword']) . '%';
			}

			if (isset($_GPC['status'])) {
				if($_GPC['status']=='1' || $_GPC['status']=='0'){ 
					$condition .= ' AND `status` = :status';
					$params[':status'] = intval($_GPC['status']);
				}
			}
				if($_GPC['deleted']=='1' || $_GPC['deleted']=='0'){
					$condition .= ' AND `deleted` = :deleted';
					$params[':deleted'] = $_GPC['deleted'];
				}
				if($_GPC['sys']=='1' || $_GPC['sys']=='0'){
					$condition .= ' AND `sys` = :sys';
					$params[':sys'] = $_GPC['sys'];
				}
				if($_GPC['top']=='1' || $_GPC['top']=='0'){
					$condition .= ' AND `top` = :top';
					$params[':top'] = $_GPC['top'];
				}
			

			$sql = 'SELECT COUNT(*) FROM ' . tablename('yuexiage_community_tabs') . $condition;
			$total = pdo_fetchcolumn($sql, $params);

			if (!empty($total)) {
				$sql = 'SELECT * FROM ' . tablename('yuexiage_community_tabs') . $condition . ' ORDER BY `status` DESC,
						`id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
				$list = pdo_fetchall($sql, $params);
				$pager = pagination($total, $pindex, $psize);
			}

		}elseif ($operation == 'delete') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM " . tablename('yuexiage_community_tabs') . " WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，标签不存在或是已经被删除！');
			}
			//修改成不直接删除，而设置deleted=1
		
				$date=date("Y-m-d H:i:s");
				$data = array(
						"deleted" => 1,
						"delid"=>$_W['uid'],
						"del"=>$_W['username'],
						"lastdel"=>$date
					);
				pdo_update("yuexiage_community_tabs",$data, array('id' => $id));
				message('删除成功！', referer(), 'success');
			
		}elseif ($operation == 'top') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM " . tablename('yuexiage_community_tabs') . " WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，标签不存在或是已经被删除！');
			}
			//修改成不直接删除，而设置deleted=1
			
				$date=date("Y-m-d H:i:s");
				$data = array(
						"top" => 1,
						"modifyid"=>$_W['uid'],
						"modify"=>$_W['username'],
						"lastmodified"=>$date
					);
				pdo_update("yuexiage_community_tabs", $data, array('id' => $id));
				message('置顶成功！', referer(), 'success');
		
		}elseif ($operation == 'restore') {
			$id = intval($_GPC['id']);
			$row = pdo_fetch("SELECT id FROM " . tablename('yuexiage_community_tabs') . " WHERE id = :id", array(':id' => $id));
			if (empty($row)) {
				message('抱歉，标签不存在或是已经被删除！');
			}
			//修改成不直接删除，而设置deleted=1
			
				$date=date("Y-m-d H:i:s");
				$data = array(
						"deleted" => 0,
						"modifyid"=>$_W['uid'],
						"modify"=>$_W['username'],
						"lastmodified"=>$date
					);
				pdo_update("yuexiage_community_tabs", $data, array('id' => $id));
				message('恢复标签成功！', referer(), 'success');
			
		} 


		include $this->template('tabs');
	}
	public function doMobileMyshare() {
		//这个操作被定义用来呈现 微站个人中心导航
	}
	public function doMobileMycomments() {
		//这个操作被定义用来呈现 微站个人中心导航
	}

	function doMobileUploadimg(){
		global $_W,$_GPC;
		$media['type']='image';
		$media['media_id']=$_GPC['media_id'];
		$account = WeAccount::create($_W['acid']);
		echo $groupid = $account->downloadMedia($media);
	}

}