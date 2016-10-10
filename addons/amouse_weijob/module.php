<?php
/**
 * 微招聘模块定义
 * @url
 */
defined('IN_IA') or exit('Access Denied');

class Amouse_weijobModule extends WeModule
{
    public function fieldsFormDisplay($rid = 0) {
        global $_W, $_GPC;
        if (!empty($rid)) {
           $reply = pdo_fetch("SELECT * FROM " . tablename('amouse_weijob_reply') . " WHERE rid = :rid", array(':rid' => $rid));
           $sql = 'SELECT id,title,thumb,content FROM ' . tablename('amouse_weijob_company') . ' WHERE `id`=:companyid';
           $activity = pdo_fetch($sql, array(':companyid' => $reply['companyid']));
        }
        include $this->template('form');
    }

    public function fieldsFormValidate($rid = 0) {
        global $_W, $_GPC;
        $list_id= intval($_GPC['activity']);
        if(!empty($list_id)) {
            $sql = 'SELECT * FROM ' . tablename('amouse_weijob_company') . " WHERE `id`=:list_id";
            $params = array();
            $params[':list_id'] = $list_id;
            $activity = pdo_fetch($sql, $params);
            return ;
            if(!empty($activity)) {
                return '';
            }
        }
        return '没有选择合适的公司';
    }


    public function fieldsFormSubmit($rid) {
        global $_GPC;
        $companyid = intval($_GPC['activity']);
        $record = array();
        $record['companyid'] = $companyid;
        $record['rid'] = $rid;
        $reply = pdo_fetch("SELECT * FROM " . tablename('amouse_weijob_reply') . " WHERE rid = :rid", array(':rid' => $rid));
        if($reply) {
            pdo_update('amouse_weijob_reply', $record, array('id' => $reply['id']));
        } else {
            pdo_insert('amouse_weijob_reply', $record);
        }
    }

    public function ruleDeleted($rid){
        pdo_delete('amouse_weijob_reply', array('rid' => $rid));
    }

    public function settingsDisplay($settings) {
        global $_W, $_GPC;
        load()->func('tpl');
        if(checksubmit()) {
            $cfg = array();
            $cfg['appid'] = $_GPC['appid'];
            $cfg['secret'] = $_GPC['secret'];
            $cfg['followurl']=$_GPC['followurl'];
            if($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
        include $this->template('settings');
    }

}