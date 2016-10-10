<?php
global $_W,$_GPC;//给我送花的人
$weid = $_W['uniacid'];
$settings = pdo_fetch("SELECT * FROM ".tablename('meepo_hongniangset')." WHERE weid=:weid",array(':weid'=>$_W['weid']));
$openid = $_W['openid']; 
$tablename = tablename("meepo_hongnianglikes");
$sql = 'SELECT DISTINCT openid FROM ' . $tablename . ' WHERE toopenid=:toopenid AND weid=:weid  ORDER BY createtime DESC';
$arr = array(":toopenid" =>$openid,":weid"=>$weid);
$res = pdo_fetchall($sql, $arr);
if (!empty($res)) {
	foreach ($res as $row) {
			$result[] = $this->getusers($weid,$row['openid']);
	}
}
include $this->template('mylike');