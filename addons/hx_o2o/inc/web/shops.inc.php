<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
load()->func('tpl');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	if (!empty($_GPC['displayorder'])) {
		foreach ($_GPC['displayorder'] as $id => $displayorder) {
			pdo_update($this->t_shops, array('displayorder' => $displayorder), array('id' => $id));
		}
		message('门店排序更新成功！', $this->createWebUrl('shops', array('op' => 'display')), 'success');
	}
	$children = array();
	$shops = pdo_fetchall("SELECT * FROM " . tablename($this->t_shops) . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
	include $this->template('shops');
} elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$shops = pdo_fetch("SELECT * FROM " . tablename($this->t_shops) . " WHERE id = '$id'");
	} else {
		$shops = array(
			'displayorder' => 0,
			'enabled' => 1
		);
	}
	if (checksubmit('submit')) {
		if (empty($_GPC['shopname'])) {
			message('抱歉，请输入门店名称！');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $_GPC['shopname'],
			'enabled' => intval($_GPC['enabled']),
			'displayorder' => intval($_GPC['displayorder']),
			'province' => $_GPC['address_1']['province'],
			'city' => $_GPC['address_1']['city'],
			'district' => $_GPC['address_1']['district'],
			'address' => $_GPC['address'],
			'content' => htmlspecialchars_decode($_GPC['content']),
			'description' => $_GPC['description'],
			'tel' => $_GPC['tel'],
			'thumb' => $_GPC['thumb'],
			'thumb_sm' => $_GPC['thumb_sm'],
			'score' => intval($_GPC['score']),
			'createtime' => time(),
		);
		if (!empty($id)) {
			unset($data['createtime']);
			pdo_update($this->t_shops, $data, array('id' => $id));
		} else {
			pdo_insert($this->t_shops, $data);
			$id = pdo_insertid();
		}
		message('更新门店成功！', $this->createWebUrl('shops', array('op' => 'display')), 'success');
	}
	include $this->template('shops');
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$shops = pdo_fetch("SELECT id FROM " . tablename($this->t_shops) . " WHERE id = '$id'");
	if (empty($shops)) {
		message('抱歉，门店不存在或是已经被删除！', $this->createWebUrl('shops', array('op' => 'display')), 'error');
	}
	pdo_delete($this->t_shops, array('id' => $id));
	message('门店删除成功！', $this->createWebUrl('shops', array('op' => 'display')), 'success');
}
?>