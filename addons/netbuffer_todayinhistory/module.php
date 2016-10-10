<?php
defined('IN_IA') or exit('Access Denied');
class Netbuffer_todayinhistoryModule extends WeModule
{
    public function settingsDisplay($settings)
    {
        global $_GPC, $_W;
        if (checksubmit()) {
            $cfg = array(
                'nbrouteurl' => $_GPC['nbrouteurl']
            );
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
        include $this->template('settings');
    }
}