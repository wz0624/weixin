<?php
defined('IN_IA') or exit('Access Denied');
class Zio_drugqueryModule extends WeModule
{
    public function settingsDisplay($settings)
    {
        global $_GPC, $_W;
        if (checksubmit()) {
            $cfg = array(
                'savedrug' => intval($_GPC['savedrug'])
            );
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
        load()->func('tpl');
        include $this->template('setting');
    }
}