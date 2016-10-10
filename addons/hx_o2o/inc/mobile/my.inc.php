<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
if (empty($_W['fans']['nickname'])) {
	mc_oauth_userinfo();
}
$config = $this->module['config'];
$bg_img = !empty($config['member_bg']) ? $config['member_bg'] : './addons/hx_o2o/template/style/images/member_bg.jpg';
$tel = !empty($config['tel']) ? $config['tel'] : '400-808-3121';
$title = $item['name'] . '个人中心';
include $this->template('member');
?>