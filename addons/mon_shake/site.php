<?php
/**
 * codeMonkey:631872807
 */
defined('IN_IA') or exit('Access Denied');
define("MON_SHAKE", "mon_shake");
define("MON_SHAKE_RES", "../addons/" . MON_SHAKE . "/");
require_once IA_ROOT . "/addons/" . MON_SHAKE . "/dbutil.class.php";
require IA_ROOT . "/addons/" . MON_SHAKE . "/oauth2.class.php";
require_once IA_ROOT . "/addons/" . MON_SHAKE . "/value.class.php";
require_once IA_ROOT . "/addons/" . MON_SHAKE . "/monUtil.class.php";

/**
 * Class Mon_BatonModuleSite
 */
class Mon_ShakeModuleSite extends WeModuleSite
{
	public $weid;
	public $acid;
	public $oauth;
	public static $USER_COOKIE_KEY = "__shakeuserv8";
	public static $USER_CB_PAGE_SIZE = 10;


	function __construct()
	{
		global $_W;
		$this->weid = $_W['uniacid'];
		$this->oauth = new Oauth2('', '');
	}

	/**
	 * 活动管理
	 */
	public function  doWebShakeManage()
	{
		global $_W, $_GPC;
		$where = '';
		$params = array();
		$params[':weid'] = $this->weid;
		if (isset($_GPC['keyword'])) {
			$where .= ' AND `title` LIKE :keywords';
			$params[':keywords'] = "%{$_GPC['keyword']}%";
		}
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("SELECT * FROM " . tablename(DBUtil::$TABLE_SHAKE) . " WHERE weid =:weid " . $where . " ORDER BY createtime DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(DBUtil::$TABLE_SHAKE) . " WHERE weid =:weid " . $where, $params);
			$pager = pagination($total, $pindex, $psize);
		} else if ($operation == 'delete') {
			$id = $_GPC['id'];
			pdo_delete(DBUtil::$TABLE_SHAKE_RECORD, array("sid" => $id));
			pdo_delete(DBUtil::$TABLE_SHAKE_PRIZE, array("sid" => $id));
			message('删除成功！', referer(), 'success');
		}

		include $this->template("shake_manage");

	}


	/**
	 * author: codeMonkey QQ:63187280
	 * 记录
	 */
	public function  doWebRecord_list()
	{
		global $_W, $_GPC;
		$sid=$_GPC['sid'];
		$where = '';
		$params = array();
		$params[':sid'] =$sid;
		if (isset($_GPC['keyword'])) {
			$where .= ' AND `nickname` LIKE :keywords';
			$params[':keywords'] = "%{$_GPC['keyword']}%";
		}
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("SELECT r.*,p.pname as pname FROM " . tablename(DBUtil::$TABLE_SHAKE_RECORD) . " r left join ".tablename(DBUtil::$TABLE_SHAKE_PRIZE)." p  on r.pid=p.id  WHERE r.sid =:sid " . $where . " ORDER BY createtime DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(DBUtil::$TABLE_SHAKE_RECORD) . " WHERE sid =:sid " . $where, $params);
			$pager = pagination($total, $pindex, $psize);
		} else if ($operation == 'delete') {
			$id = $_GPC['id'];
			pdo_delete(DBUtil::$TABLE_SHAKE_RECORD, array("id" => $id));
			message('删除成功！', referer(), 'success');
		}

		include $this->template("record_list");

	}



	public function  doWebPrizeList()
	{
		global $_W, $_GPC;
		$sid=$_GPC['sid'];
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'display') {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("SELECT * FROM " . tablename(DBUtil::$TABLE_SHAKE_PRIZE) . " WHERE sid=:sid  ORDER BY  display_order asc, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':sid'=>$sid));
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(DBUtil::$TABLE_SHAKE_PRIZE) . " WHERE sid =:sid " , array(':sid'=>$sid));
			$pager = pagination($total, $pindex, $psize);
		} else if ($operation == 'delete') {
			$id = $_GPC['id'];
			pdo_delete(DBUtil::$TABLE_SHAKE_PRIZE, array("id" => $id));
			message('删除成功！', referer(), 'success');
		}


		include $this->template("prize_list");

	}

	/**
	 * author: codeMonkey QQ:631872807
	 * 添加物品页面
	 */
	public function doWebEditPrize()
	{
		global $_W, $_GPC;
		$sid = $_GPC['sid'];
		$pid = $_GPC['pid'];
		load()->func('tpl');
		$totalp = pdo_fetchcolumn('SELECT sum(pb) FROM ' . tablename(DBUtil::$TABLE_SHAKE_PRIZE) . " WHERE sid=:sid ", array(':sid' => $sid));
		$leftp = 100 - $totalp;

		if (!empty($pid)) {
			$item=DBUtil::findById(DBUtil::$TABLE_SHAKE_PRIZE,$pid);
			$leftp=100-$totalp+$item['pb'];
		}

		if (checksubmit('submit')) {

			if ($_GPC['pb'] > $leftp) {
				message("概率设置不正确，请重新设置!");
			}
			$data = array(
				'sid' => $sid,
				'pname' => $_GPC['pname'],
				'pimg' => $_GPC['pimg'],
				'p_url' => $_GPC['p_url'],
				'pb_count' => $_GPC['pb_count'],
				'display_order' => $_GPC['display_order'],
				'createtime' => TIMESTAMP,
				'pb' => $_GPC['pb']
			);

			if (empty ($pid)) {
				DBUtil::create(DBUtil::$TABLE_SHAKE_PRIZE, $data);
				message('添加成功！', referer(), 'success');

			} else {
				DBUtil::updateById(DBUtil::$TABLE_SHAKE_PRIZE, $data, $pid);
				message('修改成功！', referer(), 'success');
			}

		}

		include $this->template("prize_edit");
	}

	/**
	 * author: codeMonkey QQ:631872807
	 * 删除摇一摇
	 */
	public function doWebDeleteShake()
	{
		global $_GPC, $_W;

		foreach ($_GPC['idArr'] as $k => $bid) {
			$id = intval($bid);
			if ($id == 0)
				continue;
			pdo_delete(DBUtil::$TABLE_SHAKE_RECORD, array("sid" => $id));
			pdo_delete(DBUtil::$TABLE_SHAKE_PRIZE, array("sid" => $id));
			pdo_delete(DBUtil::$TABLE_SHAKE, array("id" => $id));
		}

		echo json_encode(array('code' => 200));
	}

	public function doWebDeleteRecord()
	{
		global $_GPC, $_W;

		foreach ($_GPC['idArr'] as $k => $bid) {
			$id = intval($bid);
			if ($id == 0)
				continue;
			pdo_delete(DBUtil::$TABLE_SHAKE_RECORD, array("id" => $id));

		}

		echo json_encode(array('code' => 200));
	}

	/**
	 * author: codeMonkey QQ:631872807
	 * 删除用户
	 */
	public function doWebDeleteUser()
	{
		global $_GPC, $_W;

		foreach ($_GPC['idArr'] as $k => $uid) {
			$id = intval($uid);
			if ($id == 0)
				continue;
			pdo_delete(DBUtil::$TABLE_BATON_USER, array("id" => $id));
		}

		echo json_encode(array('code' => 200));
	}

	public function webmessage($error, $url = '', $errno = -1)
	{
		$data = array();
		$data['errno'] = $errno;
		if (!empty($url)) {
			$data['url'] = $url;
		}
		$data['error'] = $error;
		echo json_encode($data);
		exit;
	}

	/**
	 * author: codeMonkey QQ:631872807
	 * 用户信息导出
	 */
	public function  doWebUDownload()
	{

		require_once 'udownload.php';
	}

	/****************************手机**********************************/
	public function doMobileIndex() {
		global $_W,$_GPC;
		$sid=$_GPC['sid'];
		MonUtil::checkmobile();
		$shake=DBUtil::findById(DBUtil::$TABLE_SHAKE,$sid);
		if(empty($shake)) {
			message("摇一摇活动删除或已不存在");
		}
		$follow=1;
		if (!empty($_W['fans']['follow'])){
			$follow=2;
		}
		$openid = $_W['fans']['from_user'];
		$this::setClientUserInfo($openid);
		include $this->template("index");
	}


	public function doMobileAjaxShake() {
		global $_W,$_GPC;
		$sid=$_GPC['sid'];
		$shake=DBUtil::findById(DBUtil::$TABLE_SHAKE,$sid);
		$res = array();
		if (empty($shake)) {
			$res['code']=501;
			$res['msg']="摇一摇活动删除或不存在!";
			die(json_encode($res));
		}

		if (TIMESTAMP < $shake['starttime']) {
			$res['code']=500;
			$res['msg']="活动还未开始，敬请期待哦!";
			die(json_encode($res));
		}

		if (TIMESTAMP>$shake['endtime']) {
			$res['code']=501;
			$res['msg']="摇一摇活动已结束，下次再来吧!";
			die(json_encode($res));
		}

		$openid = $_W['fans']['from_user'];
		if (empty($openid)) {
			$res['code']=503;
			$res['msg']="请登录授权后再参与，获取用户信息失败！";
			die(json_encode($res));
		}

		$already_playCount = $this->findUserRecordCount($sid, $openid);
		$leftTotalCount = $shake['total_limit'] - $already_playCount;

		if ($leftTotalCount <= 0) {
			$res['code'] = 504;
			$res['msg'] = "您已经没有机会了下次再来吧!";
			die(json_encode($res));
		}
		$user_day_play_count= $this->findUserDayRecordCount($sid,$openid);
		if($shake['shake_day_limit']-$user_day_play_count<=0) {
			$res['code'] = 500;
			$res['msg'] = "您今天的摇一摇机会已用完!";
			die(json_encode($res));
		}



		$prizes = pdo_fetchall("select * from " . tablename(DBUtil::$TABLE_SHAKE_PRIZE) . " where sid=:sid  ", array(":sid" => $sid));

		$arrayRand = array();

		for ($index = 0; $index < count($prizes); $index++) {
			$arrayRand[$index] = $prizes[$index]['pb'];
		}



		$pIndex = $this->get_rand($arrayRand);//随机
		$userP=$prizes[$pIndex];//奖品
        $leftPrizeCount = $userP['pb_count'] - $this->findPrizeAwardCount($userP['id']);
        $rand_time = 0;
		while($leftPrizeCount <= 0) {
			$pIndex = $this->get_rand($arrayRand);//随机
			$userP=$prizes[$pIndex];//奖品
			$leftPrizeCount = $userP['pb_count'] - $this->findPrizeAwardCount($userP['id']);
			$rand_time++;
			if ($rand_time > 100) {
				$res['code'] = 500;
				$res['msg'] = "奖品没了，下次再来参与吧!!";
				die(json_encode($res));
			}
		}



		$userInfo=MonUtil::getClientCookieUserInfo($this::$USER_COOKIE_KEY);

		$record_data=array(
			'sid'=>$sid,
			'pid'=>$userP['id'],
			'openid'=>$openid,
			'nickname'=>$userInfo['nickname'],
			'headimgurl'=>$userInfo['headimgurl'],
			'createtime'=>TIMESTAMP
		);

		DBUtil::create(DBUtil::$TABLE_SHAKE_RECORD,$record_data);

		$res['code'] = 200;
		$res['data'] =$userP;
		die(json_encode($res));
	}

	/**
	 * author: codeMonkey QQ:631872807
	 * @param $pid
	 * 查找物品中奖信息
	 */
    public  function findPrizeAwardCount($pid) {
		$count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(DBUtil::$TABLE_SHAKE_RECORD) . " WHERE  pid=:pid  ", array(':pid' => $pid));
		return $count;
	}
	public function setClientUserInfo($openid)
	{
		global $_W;
		if (!empty($openid)&&($_W['account']['level']==3 || $_W['account']['level']==4)) {
			load()->classs('weixin.account');
			$accObj= WeixinAccount::create($_W['acid']);
			$access_token = $accObj->fetch_token();

			if (empty($access_token)) {
				message("获取accessToken失败");
			}
			$userInfo = $this->oauth->getUserInfo($access_token, $openid);
			MonUtil::setClientCookieUserInfo($userInfo,$this::$USER_COOKIE_KEY);
			return $userInfo;
		}
	}

	/**
	 * 概率计算
	 *
	 * @param unknown $proArr
	 * @return Ambigous <string, unknown>
	 */
	function get_rand($proArr)
	{
		$result = '';
		// 概率数组的总概率精度
		$proSum = array_sum($proArr);
		// 概率数组循环
		foreach ($proArr as $key => $proCur) {
			$randNum = mt_rand(1, $proSum); // 抽取随机数
			if ($randNum <= $proCur) {
				$result = $key; // 得出结果
				break;
			} else {
				$proSum -= $proCur;
			}
		}
		unset($proArr);
		return $result;
	}


	/**
	 * author: codeMonkey QQ:631872807
	 * @param $sid
	 * @param $openid
	 * @return bool
	 * 总次数
	 */
	public function  findUserRecordCount($sid, $openid)
	{

		$count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(DBUtil::$TABLE_SHAKE_RECORD) . " WHERE  sid=:sid and openid=:openid ", array(':sid' => $sid, ":openid" => $openid));
		return $count;


	}

	/**
	 * author: codeMonkey QQ:631872807
	 * @param $sid
	 * @param $openid
	 * @return bool每天次数
	 */
	public function  findUserDayRecordCount($sid,$openid)
	{

		$today_beginTime = strtotime(date('Y-m-d' . '00:00:00', TIMESTAMP));
		$today_endTime = strtotime(date('Y-m-d' . '23:59:59', TIMESTAMP));

		$count = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename(DBUtil::$TABLE_SHAKE_RECORD) . " WHERE  sid=:sid and openid=:openid and createtime<=:endtime and  createtime>=:starttime ", array(':sid' =>$sid, ":openid" =>$openid, ":endtime" => $today_endTime, ":starttime" => $today_beginTime));
		return $count;
	}






	/***************************函数********************************/
	/**
	 * author: codeMonkey QQ:631872807
	 * @param $kid
	 * @param $status
	 * @return bool数量
	 */

	function  encode($value)
	{
		return $value;
		return iconv("utf-8", "gb2312", $value);

	}
}