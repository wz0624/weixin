<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
$category = pdo_fetchall("SELECT * FROM " . tablename($this->t_category) . " WHERE uniacid = '{$_W['uniacid']}' AND enabled = 1 ORDER BY displayorder DESC");
$config = $this->module['config'];
$title = isset($config['sitename']) ?  $config['sitename'] : "首页";
include $this->template('main');
?>