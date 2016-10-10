<?php


defined('IN_IA') or exit('Access Denied');
class Netbuffer_qqmusiclistModule extends WeModule
{
    public function settingsDisplay($settings)
    {
        global $_GPC, $_W;
        message('无需参数设置');
        include $this->template('settings');
    }
}