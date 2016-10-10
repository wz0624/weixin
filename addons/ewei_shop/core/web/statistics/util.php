<?php

//微赞科技 by QQ:800083075 http://www.012wz.com/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W, $_GPC;

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'days') {
    $year  = intval($_GPC['year']);
    $month = intval($_GPC['month']);
    die(get_last_day($year, $month));
}