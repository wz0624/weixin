<?php

defined('IN_IA') or exit('Access Denied');
class wdl_dangerModule extends WeModule
{
    public function settingsDisplay($settings)
    {
        global $_W, $_GPC;
        $data = $_GPC['data'];
        if (checksubmit()) {
            $flag = $this->saveSettings($data);
            if ($flag) {
                message("信息保存成功", "", "success");
            } else {
                message('信息保存失败', "", 'error');
            }
        }
        load()->func('tpl');
        include $this->template('setting');
        echo 'nihao';
    }
}