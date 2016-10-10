<?php
/**
 * Created by PhpStorm.
 * User: stevezheng
 * Date: 15/12/24
 * Time: 17:21
 */
global $_W, $_GPC;

checklogin();

load()->func('tpl');
load()->model('mc');
load()->model('activity');

$_W['page']['title'] = '发放记录';
$uniacid = $_W['uniacid'];

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';
if ($operation == 'list') {
    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $condition = ' WHERE y.uniacid=:uniacid';
    $pars = array();
    $pars[':uniacid'] = $_W['uniacid'];
    $list = pdo_fetchall("SELECT * FROM " . tablename('yike_activity_coupon_record') . ' as y left join ' . tablename('mc_members') . ' as m on y.user_id = m.uid ' . $condition . ' order by `id` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $pars);
    include $this->template('web/record/list');
}
