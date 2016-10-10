<?php
defined('IN_IA') or exit('Access Denied');
class Meepo_credit1Module extends WeModule
{
    public function settingsDisplay($settings)
    {
        global $_W, $_GPC;
        load()->func('tpl');
        if (checksubmit()) {
            $_POST['detail'] = htmlspecialchars($_POST['detail']);
            $data            = $_POST;
            $this->saveSettings($data);
            message('提交成功', referer(), success);
        }
        include $this->template('setting');
    }
}