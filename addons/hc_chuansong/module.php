<?php
defined('IN_IA') or exit('Access Denied');
class hc_chuansongModule extends WeModule
{
    public function fieldsFormDisplay($rid = 0)
    {
        global $_W;
        $reply           = pdo_fetch("SELECT * FROM " . tablename('hc_chuansong_reply') . " WHERE rid = :rid", array(
            ':rid' => $rid
        ));
        $hc_chuansongist = pdo_fetchall('select id,title from ' . tablename('hc_chuansong_list') . ' where weid=:weid ', array(
            ':weid' => $_W['uniacid']
        ));
        include $this->template('form');
    }
    public function fieldsFormValidate($rid = 0)
    {
        global $_W;
        return '';
    }
    public function fieldsFormSubmit($rid)
    {
        global $_W, $_GPC;
        $reid = intval($_GPC['reply_id']);
        $data = array(
            'weid' => $_W['uniacid'],
            'istype' => $_GPC['istype'],
            'hc_chuansongid' => $_GPC['hc_chuansongid'],
            'rid' => $rid,
            'cover' => $_GPC['cover'],
            'title' => $_GPC['title'],
            'desc' => $_GPC['desc']
        );
        if (empty($reid)) {
            pdo_insert('hc_chuansong_reply', $data);
        } else {
            pdo_update('hc_chuansong_reply', $data, array(
                'id' => $reid
            ));
        }
    }
    public function ruleDeleted($rid)
    {
        pdo_delete('hc_chuansong_reply', array(
            'rid' => $rid
        ));
    }
}