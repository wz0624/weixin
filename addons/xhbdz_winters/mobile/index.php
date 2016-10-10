<?php
/**
 * 首页
*
*/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
global $_W,$_GPC;

$settings = $this->module['config'];



$goodss = m('goods')->get_goods($settings['link1']);



//$goods2 = m('goods')->get_goods($settings['link2']);



//$goods3 = m('goods')->get_goods($settings['link3']);


include $this->template('index');