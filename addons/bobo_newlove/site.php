<?php
defined('IN_IA') or exit('Access Denied');
define('RES', '../addons/bobo_newlove/template/');
class bobo_newloveModuleSite extends WeModuleSite
{
    public $title = '祝天下有情人终成眷属';
    function __construct()
    {
        global $_W, $_GPC;
    }
    public function doMobileIndex()
    {
        global $_W, $_GPC;
        include $this->template('index');
    }
}