<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
$id = intval($_GPC['id']);
if (empty($id)) {
	message('访问错误.', $this->createWebUrl('main'), 'error');
}
$item = pdo_fetch("SELECT * FROM " . tablename($this->t_product) . " WHERE uniacid = '{$_W['uniacid']}' AND id = '{$id}' AND deleted = 0 AND status = 1");
if (empty($item)) {
	message('产品不存在或不可用.', $this->createWebUrl('main'), 'error');
}
pdo_update($this->t_product, array('viewtimes' => $item['viewtimes'] + 1), array('uniacid' => $_W['uniacid'], 'id' => $id));
$title = $item['name'] . '详情';
include $this->template('detail_type'.$item['type']);
?>