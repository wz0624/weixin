<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
load()->func('tpl');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'cover';
if ($operation == 'cover') {
	$category = pdo_fetchall("SELECT * FROM " . tablename($this->t_category) . " WHERE uniacid = '{$_W['uniacid']}' AND enabled = 1 ORDER BY displayorder DESC");
	include $this->template('product');
}elseif ($operation == 'display') {
	$category = pdo_fetchall("SELECT * FROM " . tablename($this->t_category) . " WHERE uniacid = '{$_W['uniacid']}' AND enabled = 1 ORDER BY displayorder DESC");
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = ' WHERE `uniacid` = :uniacid AND `deleted` = :deleted';
	$params = array(':uniacid' => $_W['uniacid'], ':deleted' => '0');
	if (!empty($_GPC['keyword'])) {
		$condition .= ' AND `name` LIKE :name';
		$params[':name'] = '%' . trim($_GPC['keyword']) . '%';
	}
	if (isset($_GPC['catid']) && $_GPC['catid'] > 0) {
		$condition .= ' AND `catid` = :catid';
		$params[':catid'] = intval($_GPC['catid']);
	}
	if (isset($_GPC['status'])) {
		$condition .= ' AND `status` = :status';
		$params[':status'] = intval($_GPC['status']);
	}
	$sql = 'SELECT COUNT(*) FROM ' . tablename($this->t_product) . $condition;
	$total = pdo_fetchcolumn($sql, $params);
	if (!empty($total)) {
		$sql = 'SELECT * FROM ' . tablename($this->t_product) . $condition . ' ORDER BY `status` DESC, `displayorder` DESC,	`type` ASC ,`id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
		$list = pdo_fetchall($sql, $params);
		$pager = pagination($total, $pindex, $psize);
	}
	include $this->template('product');
} elseif ($operation == 'post') {
	$id = intval($_GPC['id']);
	$catid = intval($_GPC['catid']);
	if (empty($catid)) {
		message('请先选择产品分类', $this->createWebUrl('product', array('op' => 'cover')), 'error');
	}
	$category = pdo_fetch("SELECT * FROM " . tablename($this->t_category) . " WHERE uniacid = '{$_W['uniacid']}' AND id = '{$catid}' AND enabled = 1");
	if (empty($category)) {
		message('分类不存在或不可用，请重新先选择产品分类', $this->createWebUrl('product', array('op' => 'cover')), 'error');
	}
	if (!empty($id)) {
		$item = pdo_fetch("SELECT * FROM " . tablename($this->t_product) . " WHERE id = '$id'");
	} else {
		$item = array(
			'displayorder' => 0,
			'times' => 1,
		);
	}
	if (checksubmit('submit')) {
		if (!empty($_GPC['picname'])) {
			foreach ($_GPC['picname'] as $key => $value) {
				$pics[$key]['name'] = $value;
				$pics[$key]['thumb'] = $_GPC['picthumb'][$key];
			}
		}
		
		$data = array(
			'uniacid' => $_W['uniacid'],
			'catid' => $catid,
			'name' => $_GPC['goodsname'],
			'thumb' => $_GPC['thumb'],
			'list_thumb' => $_GPC['list_thumb'],
			'price' => intval($_GPC['price']),
			'marketprice' => intval($_GPC['marketprice']),
			'timeneed' => intval($_GPC['timeneed']),
			'pics' => iserializer($pics),
			'times' => intval($_GPC['times']),
			'description' => $_GPC['description'],
			'content' => htmlspecialchars_decode($_GPC['content']),
			'status' => intval($_GPC['status']),
			'displayorder' => intval($_GPC['displayorder']),
			'type' => intval($category['type']),
			'createtime' => time(),
			'deleted' => 0,
			'viewtimes' => intval($_GPC['viewtimes']),
		);
		if (!empty($id)) {
			pdo_update($this->t_product, $data, array('id' => $id));
		} else {
			pdo_insert($this->t_product, $data);
			$id = pdo_insertid();
		}
		message('更新产品成功！', $this->createWebUrl('product', array('op' => 'display')), 'success');
	}
	include $this->template('product');
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$product = pdo_fetch("SELECT id FROM " . tablename($this->t_product) . " WHERE id = '$id'");
	if (empty($product)) {
		message('抱歉，项目不存在或是已经被删除！', $this->createWebUrl('product', array('op' => 'display')), 'error');
	}
	pdo_delete($this->t_product, array('id' => $id));
	message('项目删除成功！', $this->createWebUrl('product', array('op' => 'display')), 'success');
}
?>