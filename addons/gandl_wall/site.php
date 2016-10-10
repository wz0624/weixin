<?php
//decode by QQ:270656184 http://www.yunlu99.com/
defined("IN_IA") or exit("Access Denied");
define("MD_ROOT", IA_ROOT . "/addons/gandl_wall");
define("GANL_WALL_BRANCH", 'G');
require MD_ROOT . "/source/common/common.func.php";
require MD_ROOT . "/source/Model.class.php";
require MD_ROOT . "/source/GandlWallModel.class.php";
require MD_ROOT . "/libs/vendor/autoload.php";

class Gandl_wallModuleSite extends WeModuleSite
{
	public function __construct()
	{
		global $_GPC, $_W;
		load()->model("module");
		$module = module_fetch('gandl_wall');
		if (empty($module['config'])) {
			return returnError('应用尚未配置');
		}
		$_W['module_setting'] = $module['config'];
		$certified_cache_key = MD5('gandl_wall_certified');
		$certified_cache = cache_load($certified_cache_key);
		if (empty($certified_cache) || count($certified_cache) == 0 || $certified_cache['expire_time'] < time()) {
			$wall_cnt = pdo_fetchcolumn("select COUNT(id) from " . tablename('gandl_wall') . " ");
			$static_piece = pdo_fetch("select COUNT(DISTINCT(user_id)) AS u, SUM(total_amount) AS t, SUM(pay) AS p from " . tablename('gandl_wall_piece') . " where uniacid=:uniacid AND status>0", array(':uniacid' => $_W['uniacid']));
			$static_rob = pdo_fetch("select COUNT(DISTINCT(user_id)) AS u, SUM(money) AS m from " . tablename('gandl_wall_rob') . " where uniacid=:uniacid ", array(':uniacid' => $_W['uniacid']));
			$static_user = pdo_fetchcolumn("select COUNT(DISTINCT(user_id)) from " . tablename('gandl_wall_user') . " where uniacid=:uniacid ", array(':uniacid' => $_W['uniacid']));
			$walls_data = array('piece_u' => $static_piece['u'], 'piece_t' => $static_piece['t'], 'piece_p' => $static_piece['p'], 'rob_u' => $static_rob['u'], 'rob_m' => $static_rob['m'], 'wall_u' => $static_user);
			$info = array('site_root' => $_W['siteroot'], 'branch' => GANL_WALL_BRANCH, 'version' => $module['version'], 'walls_cnt' => $wall_cnt, 'walls_data' => iserializer($walls_data));
			load()->func("communication");
			if (!is_error($resp)) {	
			}
			cache_write($certified_cache_key, $certified_cache);
		}
		if (!empty($certified_cache) && $certified_cache['verify'] == 2) {		
		}
	}

	public $_user;
	public $_is_user_infoed = 0;

	protected function _doMobileAuth()
	{
		global $_GPC, $_W;
		if ($_W['container'] != 'wechat') {
			return $this->returnError('应用目前仅支持在微信中访问', '', 'error');
		}
		if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
			if (intval($_W['account']['level']) != 4) {
				if (empty($_W['oauth_account'])) {
					return message('该公众号无微信授权能力，请联系公众号管理员', '', 'error');
				}
				if ($_W['oauth_account']['level'] != 4) {
					return message('微信授权能力获取失败，请联系公众号管理员', '', 'error');
				}
			}
			if (empty($_SESSION['oauth_openid'])) {
				return message('微信授权失败，请重试', '', 'error');
			}
			$getUserInfo = false;
			$accObj = WeiXinAccount::create($_W['oauth_account']);
			$userinfo = $accObj->fansQueryInfo($_SESSION['oauth_openid']);
			if (!is_error($userinfo) && !empty($userinfo) && is_array($userinfo) && !empty($userinfo['subscribe'])) {
				if (empty($userinfo['nickname'])) {
					return message('获取个人信息失败，请重试', '', 'error');
				}
				$getUserInfo = true;
				$userinfo['nickname'] = stripcslashes($userinfo['nickname']);
				$userinfo['avatar'] = $userinfo['headimgurl'];
				unset($userinfo['headimgurl']);
				$_SESSION['userinfo'] = base64_encode(iserializer($userinfo));
			}
			$default_groupid = pdo_fetchcolumn('SELECT groupid FROM ' . tablename('mc_groups') . ' WHERE uniacid = :uniacid AND isdefault = 1', array(':uniacid' => $_W['uniacid']));
			$data = array('uniacid' => $_W['uniacid'], 'email' => md5($_SESSION['oauth_openid']) . '@we7.cc', 'salt' => random(8), 'groupid' => $default_groupid, 'createtime' => TIMESTAMP, 'password' => md5($message['from'] . $data['salt'] . $_W['config']['setting']['authkey']));
			if (true === $getUserInfo) {
				$data['nickname'] = stripslashes($userinfo['nickname']);
				$data['avatar'] = rtrim($userinfo['avatar'], '0') . 132;
				$data['gender'] = $userinfo['sex'];
				$data['nationality'] = $userinfo['country'];
				$data['resideprovince'] = $userinfo['province'] . '省';
				$data['residecity'] = $userinfo['city'] . '市';
			}
			$uid = pdo_fetchcolumn('SELECT uid FROM ' . tablename('mc_members') . ' WHERE uniacid = :uniacid AND email = :email ', array(':uniacid' => $_W['uniacid'], ':email' => $data['email']));
			if (!$uid || empty($uid) || $uid <= 0) {
				pdo_insert('mc_members', $data);
				$uid = pdo_insertid();
			}
			$_SESSION['uid'] = $uid;
			$fan = mc_fansinfo($_SESSION['oauth_openid']);
			if (empty($fan)) {
				$fan = array('openid' => $_SESSION['oauth_openid'], 'uid' => $uid, 'acid' => $_W['acid'], 'uniacid' => $_W['uniacid'], 'salt' => random(8), 'updatetime' => TIMESTAMP, 'follow' => 0, 'followtime' => 0, 'unfollowtime' => 0,);
				if (true === $getUserInfo) {
					$fan['nickname'] = $data['nickname'];
					$fan['follow'] = $userinfo['subscribe'];
					$fan['followtime'] = $userinfo['subscribe_time'];
					$fan['tag'] = base64_encode(iserializer($userinfo));
				}
				pdo_insert("mc_mapping_fans", $fan);
			} else {
				$fan['uid'] = $uid;
				$fan['updatetime'] = TIMESTAMP;
				unset($fan['tag']);
				if (true === $getUserInfo) {
					$fan['nickname'] = $data['nickname'];
					$fan['follow'] = $userinfo['subscribe'];
					$fan['followtime'] = $userinfo['subscribe_time'];
					$fan['tag'] = base64_encode(iserializer($userinfo));
				}
				pdo_update("mc_mapping_fans", $fan, array('openid' => $_SESSION['oauth_openid'], 'acid' => $_W['acid'], 'uniacid' => $_W['uniacid']));
			}
			$_W['fans'] = $fan;
			$_W['fans']['from_user'] = $_SESSION['oauth_openid'];
			if (intval($_W['account']['level']) != 4) {
				$mc_oauth_fan = _mc_oauth_fans($_SESSION['oauth_openid'], $_W['acid']);
				if (empty($mc_oauth_fan)) {
					$data = array('acid' => $_W['acid'], 'oauth_openid' => $_SESSION['oauth_openid'], 'uid' => $uid, 'openid' => $_SESSION['openid']);
					pdo_insert("mc_oauth_fans", $data);
				} else {
					if (!empty($mc_oauth_fan['uid'])) {
						$_SESSION['uid'] = intval($mc_oauth_fan['uid']);
					}
					if (empty($_SESSION['openid']) && !empty($mc_oauth_fan['openid'])) {
						$_SESSION['openid'] = strval($mc_oauth_fan['openid']);
					}
				}
			} else {
				$_SESSION['openid'] = $_SESSION['oauth_openid'];
			}
			header("Location: " . $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING']);
		}
		load()->model("mc");
		$this->_user = mc_fetch($_SESSION['uid'], array('email', 'mobile', 'nickname', 'gender', 'avatar'));
		if (empty($this->_user)) {
			if (intval($_W['account']['level']) != 4) {
				pdo_delete('mc_oauth_fans', array('acid' => $_W['acid'], 'uid' => $_SESSION['uid']));
			}
			unset($_SESSION['uid']);
			header("Location: " . $_W['siteroot'] . 'app/index.php?' . $_SERVER['QUERY_STRING']);
			exit();
		}
		if (!empty($this->_user['nickname']) || !empty($this->_user['avatar'])) {
			$this->_is_user_infoed = 1;
		}
	}

	public $_cmd;
	public $_wall;
	public $_wall_status = 1;
	public $_mine;
	public $_inviter;

	protected function _doMobileInitialize()
	{
		global $_GPC, $_W, $do;
		$this->_cmd = $_GPC['cmd'];
		$pid = $_GPC['pid'];
		if (empty($pid)) {
			$this->returnError('朋友，迷路了吧');
		}
		$pid = pdecode($pid);
		if (empty($pid)) {
			$this->returnError('朋友，走错路了吧');
		}
		$pid = intval($pid);
		if ($pid <= 0) {
			$this->returnError('你是逗逼请来的黑客吗？');
		}
		$this->_wall = pdo_fetch("select * from " . tablename('gandl_wall') . " where uniacid=:uniacid and id=:id ", array(':uniacid' => $_W['uniacid'], ':id' => $pid));
		if (empty($this->_wall)) {
			$this->returnError('你要找的圈子已经不见了');
		}
		if ($this->_wall['start_time'] > time()) {
			$this->_wall_status = 0;
		}
		if ($this->_wall['end_time'] <= time()) {
			$this->_wall_status = 2;
		}
		
		if (!empty($this->_wall['slider'])) {
			$this->_wall['slider'] = iunserializer($this->_wall['slider']);	
			if (!empty($this->_wall['slider']['links'])) {
//旧				$this->_wall['slider']['links'] = explode_array($this->_wall['slider']['links']);
				$links=serialize($this->_wall['slider']['links']); //修改：ding
				$links=iunserializer($links); //修改：ding
				$this->_wall['slider']['links'] = explode(';',$links); //修改：ding
			}
		}
		$this->_wall['lang'] = explode_map($this->_wall['lang']);
		if (!empty($this->_wall['piece_model'])) {
			$this->_wall['piece_model'] = explode(',', $this->_wall['piece_model']);
		} else {
			$this->_wall['piece_model'] = array();
		}
		$this->_wall['group_rule'] = explode_map($this->_wall['group_rule']);
		$this->_wall['hot_rule'] = explode_map($this->_wall['hot_rule']);
		$this->_wall['max_num'] = intval(floatval($this->_wall['total_max']) / floatval($this->_wall['avg_min']));
		$this->_wall['share'] = iunserializer($this->_wall['share']);
		$this->_wall['share_title'] = $this->_wall['share']['title'];
		$this->_wall['share_img'] = $this->_wall['share']['img'];
		$this->_wall['share_desc'] = $this->_wall['share']['desc'];
		$this->_mine = pdo_fetch("select * from " . tablename('gandl_wall_user') . " where wall_id=:wall_id and user_id=:user_id ", array(':wall_id' => $this->_wall['id'], ':user_id' => $this->_user['uid']));
		$src = $_GPC['src'];
		if (!empty($src)) {
			$src = pdecode($src);
			if (!empty($src)) {
				$src = intval($src);
				if ($src > 0) {
					$inviter = pdo_fetch("select * from " . tablename('gandl_wall_user') . " where uniacid=:uniacid and wall_id=:wall_id and id=:id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $this->_wall['id'], ':id' => $src));
					if (!empty($inviter)) {
						$this->_inviter = $inviter;
					}
				}
			}
		}
		$doors = array('help', 'invite', 'test');
		if (empty($this->_cmd) || in_array($this->_cmd, $doors)) {
			if (empty($this->_mine)) {
				load()->model('mc');
				$fan = mc_fansinfo($this->_user['uid'], $_W['acid'], $_W['uniacid']);
				$mine = array();
				$mine['uniacid'] = $_W['uniacid'];
				$mine['wall_id'] = $this->_wall['id'];
				$mine['user_id'] = $this->_user['uid'];
				$mine['followed'] = (!empty($fan) && $fan['follow'] == 1) ? 1 : 0;
				$mine['follow'] = (!empty($fan) && $fan['follow'] == 1) ? 1 : 0;
				$mine['notify_newpiece'] = 1;
				$mine['money'] = 0;
				$mine['money_in'] = 0;
				$mine['money_out'] = 0;
				$mine['send_times'] = 0;
				$mine['send_total'] = 0;
				$mine['rob_times'] = 0;
				$mine['rob_total'] = 0;
				$mine['rob_luck'] = 0;
				$mine['create_time'] = time();
				$mine['last_active_time'] = time();
				if (!empty($this->_inviter)) {
					$mine['inviter_id'] = $this->_inviter['id'];
				}
				pdo_insert("gandl_wall_user", $mine);
				$mine_id = pdo_insertid();
				if ($mine_id > 0) {
					$this->_mine = $mine;
					$this->_mine['id'] = $mine_id;
				}
				if (!empty($this->_inviter)) {
					pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET rob_fast=rob_fast+:rob_fast,rob_next_time=0 where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'], ':wall_id' => $this->_wall['id'], ':id' => $this->_inviter['id'], ':rob_fast' => $this->_wall['task_invite']));
				}
			} else if ($this->_mine['follow'] == -1) {
				load()->model('mc');
				$fan = mc_fansinfo($this->_user['uid'], $_W['acid'], $_W['uniacid']);
				$this->_mine['follow'] = (!empty($fan) && $fan['follow'] == 1) ? 1 : 0;
				pdo_query("UPDATE " . tablename("gandl_wall_user") . " SET follow=:follow where uniacid=:uniacid and wall_id=:wall_id and id=:id", array(":uniacid" => $_W['uniacid'], ':wall_id' => $this->_wall['id'], ':id' => $this->_mine['id'], ':follow' => $this->_mine['follow']));
			}
		}
		if (empty($this->_mine)) {
			$this->returnError('请从圈子入口访问');
		}
		if ($this->_wall['status'] != 1) {
			if (!($this->_mine['admin'] > 0)) {
				$this->returnError($this->_wall['topic'] . '正在维护，请稍后再来吧~');
			}
		}
		if ($this->_mine['black'] == 1) {
			$this->returnError('您暂时无法访问，原因：' . $this->_mine['black_why']);
		}
		if (($do == 'index' && empty($this->_cmd)) || ($do == 'piece' && empty($this->_cmd))) {
			if (time() - $mine['last_active_time'] >= 300) {
				pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET last_active_time=:last_active_time where uniacid=:uniacid and wall_id=:wall_id and id=:id', array(':uniacid' => $_W['uniacid'], ':wall_id' => $this->_wall['id'], ':id' => $this->_mine['id'], ':last_active_time' => time()));
			}
		}
	}

	public function doMobileLogin()
	{
		global $_GPC, $_W;
		if (empty($_SESSION['login_referer'])) {
			$_SESSION['login_referer'] = $_SERVER['HTTP_REFERER'];
		}
		if ($_W['container'] == 'wechat') {
			$userinfo = mc_oauth_userinfo();
			if (is_error($userinfo)) {
				unset($_SESSION['login_referer']);
				return message($userinfo['message'], '', 'error');
			}
			if (empty($userinfo) || !is_array($userinfo)) {
				unset($_SESSION['login_referer']);
				return message("微信自动登录失败，请重试", '', "error");
			}
			$login_referer = $_SESSION['login_referer'];
			unset($_SESSION['login_referer']);
			header("Location: " . $login_referer);
			exit;
		} else {
			unset($_SESSION['login_referer']);
			return message("该应用仅支持在微信中运行", '', "error");
		}
		unset($_SESSION['login_referer']);
		return message("该应用目前仅支持在微信中访问", '', "error");
	}

	protected function vp_users($uids, $fields)
	{
		global $_W;
		if (empty($uids)) {
			return null;
		}
		if (is_array($uids)) {
			if (count($uids) == 0) {
				return array();
			}
			return pdo_fetchall("select " . $fields . " from " . tablename('gandl_wall_user') . " where uniacid=:uniacid  and wall_id=:wall_id AND user_id IN(" . implode(",", $uids) . ") ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $this->_wall['id']), 'user_id');
		} else {
			return pdo_fetch("select " . $fields . " from " . tablename('gandl_wall_user') . " where uniacid=:uniacid  and wall_id=:wall_id AND user_id=:user_id ", array(':uniacid' => $_W['uniacid'], ':wall_id' => $this->_wall['id'], ':user_id' => $uids));
		}
	}

	public function doMobileReset()
	{
		global $_GPC, $_W;
		session_unset();
		message("已清空");
	}

	public function doMobileQr()
	{
		global $_GPC;
		$raw = @base64_decode($_GPC['raw']);
		if (!empty($raw)) {
			include MD_ROOT . '/source/common/phpqrcode.php';
			QRcode::png($raw, false, QR_ECLEVEL_Q, 4);
		}
	}

	public function doWebQr()
	{
		global $_GPC;
		$raw = @base64_decode($_GPC['raw']);
		if (!empty($raw)) {
			include MD_ROOT . '/source/common/phpqrcode.php';
			QRcode::png($raw, false, QR_ECLEVEL_Q, 4);
		}
	}

	protected function returnMessage($msg, $redirect = '', $type = '')
	{
		global $_W, $_GPC;
		if ($redirect == 'refresh') {
			$redirect = $_W['script_name'] . '?' . $_SERVER['QUERY_STRING'];
		}
		if ($redirect == 'referer') {
			$redirect = referer();
		}
		if ($redirect == '') {
			$type = in_array($type, array('success', 'error', 'info', 'warn')) ? $type : 'info';
		} else {
			$type = in_array($type, array('success', 'error', 'info', 'warn')) ? $type : 'success';
		}
		if (empty($msg) && !empty($redirect)) {
			header('location: ' . $redirect);
		}
		$label = $type;
		if ($type == 'error') {
			$label = 'warn';
		}
		include $this->template('inc/message');
		exit();
	}

	protected function returnError($message, $data = '', $status = 0, $type = '')
	{
		global $_W;
		if ($_W['isajax'] || $type == 'ajax') {
			header('Content-Type:application/json; charset=utf-8');
			$ret = array('status' => $status, 'info' => $message, 'data' => $data);
			exit(json_encode($ret));
		} else {
			return $this->returnMessage($message, $data, 'error');
		}
	}

	protected function returnSuccess($message, $data = '', $status = 1, $type = '')
	{
		global $_W;
		if ($_W['isajax'] || $type == 'ajax') {
			header('Content-Type:application/json; charset=utf-8');
			$ret = array('status' => $status, 'info' => $message, 'data' => $data);
			exit(json_encode($ret));
		} else {
			return $this->returnMessage($message, $data, 'success');
		}
	}

	protected function payReady($params = array(), $mine = array())
	{
		global $_W;
		$params['module'] = $this->module['name'];
		$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid';
		$pars = array();
		$pars[':uniacid'] = $_W['uniacid'];
		$pars[':module'] = $params['module'];
		$pars[':tid'] = $params['tid'];
		$log = pdo_fetch($sql, $pars);
		if (empty($log)) {
			$log = array('uniacid' => $_W['uniacid'], 'acid' => $_W['acid'], 'openid' => $_W['member']['uid'], 'module' => $this->module['name'], 'tid' => $params['tid'], 'fee' => $params['fee'], 'card_fee' => $params['fee'], 'status' => '0', 'is_usecard' => '0',);
			pdo_insert("core_paylog", $log);
		}
		if ($log['status'] == '1') {
			message('这个订单已经支付成功, 不需要重复支付.');
		}
		return $params;
	}

	public function payResult($params)
	{
		global $_W;
		if ($params['result'] == 'success' && $params['from'] == 'notify') {
			$piece = pdo_fetch("select id,uniacid,wall_id,user_id,hot_time,total_amount,total_pay from " . tablename('gandl_wall_piece') . " where id=:id ", array(':id' => $params['tid']));
			if (intval($piece['total_pay']) < 1 || intval($piece['total_pay']) != intval(floatval($params['fee']) * 100)) {
				exit();
			}
			$wall = pdo_fetch("select id,uniacid,start_time,end_time,begin_time,over_time,notify,notify_tpl,piece_verify from " . tablename('gandl_wall') . " where id=:id ", array(':id' => $piece['wall_id']));
			$now = time();
			$begin_time = strtotime(date('Y-m-d')) + $wall['begin_time'] * 3600;
			$over_time = strtotime(date('Y-m-d')) + $wall['over_time'] * 3600;
			$next_begin_time = strtotime(date('Y-m-d', strtotime('+1 day'))) + $wall['begin_time'] * 3600;
			$rob_start_time = 0;
			if ($now < $begin_time) {
				$rob_start_time = $begin_time + $piece['hot_time'];
			} else if ($now < $over_time) {
				$rob_start_time = $now + $piece['hot_time'];
			} else {
				$rob_start_time = $next_begin_time + $piece['hot_time'];
			}
			$pieceUp = array('publish_time' => $now, 'rob_start_time' => $rob_start_time, 'pay' => floatval($params['fee']) * 100, 'status' => 1);
			pdo_update("gandl_wall_piece", $pieceUp, array('id' => $params['tid']));
			pdo_query("UPDATE " . tablename("gandl_wall_user") . ' SET money_in=money_in+:pay,send_times=send_times+1,send_total=send_total+:send_amount,send_last_time=:send_last_time where uniacid=:uniacid and wall_id=:wall_id and user_id=:user_id', array(":uniacid" => $piece['uniacid'], ':wall_id' => $piece['wall_id'], ':user_id' => $piece['user_id'], ':pay' => floatval($params['fee']) * 100, ':send_amount' => $piece['total_amount'], ':send_last_time' => $now));
		}
		if ($params['from'] == 'return') {
			if ($params['result'] == 'success') {
				$piece = pdo_fetch("select id,uniacid,wall_id,user_id,total_pay,op from " . tablename('gandl_wall_piece') . " where id=:id ", array(':id' => $params['tid']));
				if (intval($piece['total_pay']) < 1 || intval($piece['total_pay']) != intval(floatval($params['fee']) * 100)) {
					$this->returnError('支付金额不符！');
				}
				$redirect = $_W['siteroot'] . 'app/' . substr($this->createMobileUrl('piece', array('pid' => pencode($piece['wall_id']), 'piid' => pencode($piece['id']))), 2);
				$this->returnSuccess(($piece['op'] == -1 ? '已提交审核' : '发布成功！'), $redirect);
			} else {
				$this->returnError('支付失败！');
			}
		}
	}

	protected function transferByRedpack($transfer)
	{
		global $_W;
		$api = array('mchid' => $_W['module_setting']['mchid'], 'appid' => $_W['module_setting']['appid'], 'ip' => $_W['module_setting']['ip'], 'key' => $_W['module_setting']['key']);
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
		load()->func("communication");
		$pars = array();
		$pars['nonce_str'] = random(32);
		$pars['mch_billno'] = $api['mchid'] . date('Ymd') . $transfer['id'];
		$pars['mch_id'] = $api['mchid'];
		$pars['wxappid'] = $api['appid'];
		$pars['nick_name'] = $transfer['nick_name'];
		$pars['send_name'] = $transfer['send_name'];
		$pars['re_openid'] = $_W['openid'];
		$pars['total_amount'] = $transfer['money'];
		$pars['min_value'] = $transfer['money'];
		$pars['max_value'] = $transfer['money'];
		$pars['total_num'] = 1;
		$pars['wishing'] = $transfer['wishing'];
		$pars['client_ip'] = $api['ip'];
		$pars['act_name'] = $transfer['act_name'];
		$pars['remark'] = $transfer['remark'];
		ksort($pars, SORT_STRING);
		$string1 = '';
		foreach ($pars as $k => $v) {
			$string1 .= "{$k}={$v}&";
		}
		$string1 .= "key={$api['key']}";
		$pars['sign'] = strtoupper(md5($string1));
		$xml = array2xml($pars);
		$extras = array();
		$extras['CURLOPT_CAINFO'] = ATTACHMENT_ROOT . '/' . $_W['module_setting']['cert_rootca']['path'];
		$extras['CURLOPT_SSLCERT'] = ATTACHMENT_ROOT . '/' . $_W['module_setting']['cert_cert']['path'];
		$extras['CURLOPT_SSLKEY'] = ATTACHMENT_ROOT . '/' . $_W['module_setting']['cert_key']['path'];
		$procResult = null;
		$resp = ihttp_request($url, $xml, $extras);
		if (is_error($resp)) {
			return $resp;
		} else {
			$xml = '<?xml version="1.0" encoding="utf-8"?>' . $resp['content'];
			$dom = new DOMDocument();
			if ($dom->loadXML($xml)) {
				$xpath = new DOMXPath($dom);
				$code = $xpath->evaluate('string(//xml/return_code)');
				$ret = $xpath->evaluate('string(//xml/result_code)');
				if (strtolower($code) == 'success' && strtolower($ret) == 'success') {
					$mch_billno = $xpath->evaluate('string(//xml/mch_billno)');
					$out_billno = $xpath->evaluate('string(//xml/send_listid)');
					$out_money = $xpath->evaluate('string(//xml/total_amount)');
					$procResult = array('mch_billno' => $mch_billno, 'out_billno' => $out_billno, 'out_money' => $out_money, 'tag' => iserializer($resp));
				} else {
					$error = $xpath->evaluate('string(//xml/err_code_des)');
					$procResult = error(-2, $error);
				}
			} else {
				$procResult = error(-1, 'error response');
			}
		}
		return $procResult;
	}
}