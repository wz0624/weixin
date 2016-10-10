<?php

//微赞科技 by QQ:800083075 http://www.012wz.com/
global $_W, $_GPC;

$shopset = m('common')->getSysset('shop');
ca('taobao.fetch');
$sql      = 'SELECT * FROM ' . tablename('ewei_shop_category') . ' WHERE `uniacid` = :uniacid ORDER BY `parentid`, `displayorder` DESC';
$category = pdo_fetchall($sql, array(
    ':uniacid' => $_W['uniacid']
), 'id');
$parent   = $children = array();
if (!empty($category)) {
    foreach ($category as $cid => $cate) {
        if (!empty($cate['parentid'])) {
            $children[$cate['parentid']][] = $cate;
        } else {
            $parent[$cate['id']] = $cate;
        }
    }
}
load()->func('tpl');
include $this->template('index');