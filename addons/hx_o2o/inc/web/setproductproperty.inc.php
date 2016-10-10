<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
$id = intval($_GPC['id']);
$type = $_GPC['type'];
$data = intval($_GPC['data']);
if (in_array($type, array('status'))) {
	$data = ($data==1?'0':'1');
	pdo_update($this->t_product, array($type => $data), array("id" => $id, "uniacid" => $_W['uniacid']));
	die(json_encode(array("result" => 1, "data" => $data)));
}
die(json_encode(array("result" => 0)));
?>