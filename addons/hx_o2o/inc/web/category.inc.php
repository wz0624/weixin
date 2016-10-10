<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
load()->func('tpl');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	if (!empty($_GPC['displayorder'])) {
		foreach ($_GPC['displayorder'] as $id => $displayorder) {
			pdo_update($this->t_category, array('displayorder' => $displayorder), array('id' => $id));
		}
		message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
	}
	$children = array();
	$category = pdo_fetchall("SELECT * FROM " . tablename($this->t_category) . " WHERE uniacid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
	include $this->template('category');
} elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	if (!empty($id)) {
		$category = pdo_fetch("SELECT * FROM " . tablename($this->t_category) . " WHERE id = '$id'");
	} else {
		$category = array(
			'displayorder' => 0,
		);
	}
	if (checksubmit('submit')) {
		if (empty($_GPC['catename'])) {
			message('抱歉，请输入分类名称！');
		}
		$data = array(
			'uniacid' => $_W['uniacid'],
			'name' => $_GPC['catename'],
			'enabled' => intval($_GPC['enabled']),
			'displayorder' => intval($_GPC['displayorder']),
			'linkurl' => $_GPC['linkurl'],
			'description' => $_GPC['description'],
			'type' => intval($_GPC['type']),
			'thumb' => $_GPC['thumb']
		);
		if (!empty($id)) {
			pdo_update($this->t_category, $data, array('id' => $id));
			pdo_update($this->t_product, array('type'=>intval($_GPC['type'])), array('catid' => $id));
		} else {
			pdo_insert($this->t_category, $data);
			$id = pdo_insertid();
		}
		message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
	}
	include $this->template('category');
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$category = pdo_fetch("SELECT id FROM " . tablename($this->t_category) . " WHERE id = '$id'");
	if (empty($category)) {
		message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
	}
	pdo_delete($this->t_category, array('id' => $id));
	message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
}
?>