<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
$catid = intval($_GPC['catid']);
if (empty($catid)) {
	message('访问错误', $this->createWebUrl('main'), 'error');
}
$category = pdo_fetch("SELECT * FROM " . tablename($this->t_category) . " WHERE uniacid = '{$_W['uniacid']}' AND id = '{$catid}' AND enabled = 1");
if (empty($category)) {
	message('分类不存在或不可用，请重新先选择产品分类', $this->createWebUrl('main'), 'error');
}
$pindex = max(1, intval($_GPC['page']));
$psize = 2;
$condition = ' WHERE `uniacid` = :uniacid AND `catid`=:catid AND `status` = 1 AND `deleted` = :deleted';
$params = array(':uniacid' => $_W['uniacid'],':catid'=>$catid, ':deleted' => '0');
$sql = 'SELECT COUNT(*) FROM ' . tablename($this->t_product) . $condition;
$total = pdo_fetchcolumn($sql, $params);
if (!empty($total)) {
	$sql = 'SELECT * FROM ' . tablename($this->t_product) . $condition . ' ORDER BY `displayorder` DESC,`id` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
	$list = pdo_fetchall($sql, $params);
	$pager = pagination($total, $pindex, $psize);
}
if ($_W['isajax']) {
	if (!empty($list)) {
		foreach ($list as &$value) {
			$value['thumb'] = tomedia($value['thumb']);
			$value['list_thumb'] = tomedia($value['list_thumb']);
			$value['url'] = $this->createMobileUrl('detail',array('id'=>$value['id']));
		}
		die(json_encode(array('status'=>1,'data'=>$list)));
	}else{
		die(json_encode(array('status'=>0,'message'=>'已全部加载')));
	}
}
$title = $category['name'];
include $this->template('list_type'.$category['type']);
?>