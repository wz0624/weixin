<?php

global $_W,$_GPC;

$openid = $_W['openid'];
if(empty($openid)){
	echo "<script>";
	echo "alert('请使用微信访问')";
	echo "</script>";
	exit();
}




$modulelist = uni_modules(false);
$name = 'ice_commonhb';
$module = $modulelist[$name];
if(empty($module)) {
	message('抱歉，你操作的模块不能被访问！');
}
define('CRUMBS_NAV', 1);
$ptr_title = '参数设置';
$module_types = module_types();
define('ACTIVE_FRAME_URL', url('home/welcome/ext', array('m' => $name)));

		$settings = $module['config'];

		$settings['hbrule'] = htmlspecialchars_decode($settings['hbrule']);

		
		$pindex = max(1, intval($_GPC['pageno']));
		$psize = 3;
		$param = array();
		$param[':uniacid'] = $_W['uniacid'];
		
		$myhbs = pdo_fetchall("select code,type,id from ".tablename("ice_yzmhb_code")." where uniacid = :uniacid and openid = :openid and yzmhbid = 0 LIMIT " . ($pindex - 1) * $psize . ',' . $psize,array(":uniacid"=>$_W['uniacid'],":openid"=>$openid));
		
		
		foreach ($myhbs as $k => $v){
			$params = array(
					":uniacid" => $_W['uniacid'],
					":codeid" => $v['id'],
					":openid" => $openid
			);
			if($v['type'] == 1){
				$url = $_W['siteroot']."app/index.php?i=".$_W['uniacid']."&c=entry&do=show&m=ice_commonhb&codeid=".$v['id'];
				$status = '2';
				$time = pdo_fetchcolumn("select time from ".tablename("ice_yzmhb_sendlist")." where uniacid = :uniacid and codeid = :codeid and openid = :openid",$params);
				$myhbs[$k]['url'] = $url;
				$myhbs[$k]['status'] = $status;
				$myhbs[$k]['typemc'] = "普通红包";
				$myhbs[$k]['time'] = date("Y-m-d H:i:s",$time);
// 				$myhbs[
			}else if($v['type'] == 2){
				$url = $_W['siteroot']."app/index.php?i=".$_W['uniacid']."&c=entry&do=show&m=ice_grouphb&codeid=".$v['id'];
				$status = '2';
				$time = pdo_fetchcolumn("select time from ".tablename("ice_yzmhb_sendlist")." where uniacid = :uniacid and codeid = :codeid and openid = :openid",$params);
				$myhbs[$k]['url'] = $url;
				$myhbs[$k]['status'] = $status;
				$myhbs[$k]['typemc'] = "裂变红包";
				$myhbs[$k]['time'] = date("Y-m-d H:i:s",$time);
			}else if($v['type'] == 3){
				$url = $_W['siteroot']."app/index.php?i=".$_W['uniacid']."&c=entry&do=share&m=ice_guesshb&codeid=".$v['id'];
				$res1 = pdo_fetch("select status,gettime,guess_count from ".tablename("ice_guesshb")." where uniacid = :uniacid and codeid = :codeid and openid = :openid ",$params);
				$status = $res1['status'];
				$time = $res1['gettime'];
				$myhbs[$k]['url'] = $url;
				$myhbs[$k]['status'] = $status;
				$myhbs[$k]['typemc'] = "小伙伴猜红包";
				$myhbs[$k]['time'] = date("Y-m-d H:i:s",$time);
				$myhbs[$k]['guess_count'] = $res1['guess_count'];
			}else if($v['type'] == 4){
				$url = $_W['siteroot']."app/index.php?i=".$_W['uniacid']."&c=entry&do=share&m=ice_robhb&codeid=".$v['id'];
				$res1 = pdo_fetch("select status,gettime from ".tablename("ice_robhb")." where uniacid = :uniacid and codeid = :codeid and openid = :openid ",$params);
				$status = $res1['status'];
				$time = $res1['gettime'];
				$myhbs[$k]['url'] = $url;
				$myhbs[$k]['typemc'] = "小伙伴抢红包";
				$myhbs[$k]['time'] = date("Y-m-d H:i:s",$time);
			}
			
		}
		
// 		$myhbs = pdo_fetchall("select * from ".tablename("ice_robhb")." where openid = :openid and status != '3' and uniacid = :uniacid order by status asc,gettime desc  LIMIT " . ($pindex - 1) * $psize . ',' . $psize,array(":openid"=>$openid,":uniacid" => $_W['uniacid']));
	//	$count = pdo_fetchcolumn("select count(*) from ".tablename("ice_guesshb")." where openid = :openid ",array(":openid" => $openid));
// 		$acc = WeAccount::create($_W['account']['uniacid']);
// 		$fan = $acc->fansQueryInfo($openid, true);
// 		$nickname = $fan['nickname'];
// 		$headimgurl = $fan['headimgurl'];
		
		
 		include $this->template("myhb");
		
	



