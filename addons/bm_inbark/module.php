<?php


defined('IN_IA') or exit('Access Denied');
class Bm_inbarkModule extends WeModule
{
    public function fieldsFormDisplay($rid = 0)
    {
        global $_W;
        $reply = pdo_fetch("SELECT * FROM " . tablename('bm_inbark_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(
            ':rid' => $rid
        ));
        load()->func('tpl');
        include $this->template('form');
    }
    public function fieldsFormValidate($rid = 0)
    {
        return '';
    }
    public function fieldsFormSubmit($rid)
    {
        global $_W, $_GPC;
        $weid = $_W['uniacid'];
        $data = array(
            'rid' => $rid,
            'weid' => $_W['uniacid'],
            'title' => $_GPC['title'],
            'desc' => $_GPC['desc'],
            'picurl' => $_GPC['picurl'],
            'logo' => $_GPC['logo']
        );
        if ($_W['ispost']) {
            if (empty($_GPC['reply_id'])) {
                pdo_insert('bm_inbark_reply', $data);
            } else {
                pdo_update('bm_inbark_reply', $data, array(
                    'id' => $_GPC['reply_id']
                ));
            }
            message('更新成功', referer(), 'success');
        }
    }
    public function ruleDeleted($rid)
    {
        global $_W;
        $replies  = pdo_fetchall("SELECT *  FROM " . tablename('bm_inbark_reply') . " WHERE rid = '$rid'");
        $deleteid = array();
        if (!empty($replies)) {
            foreach ($replies as $index => $row) {
                $deleteid[] = $row['id'];
            }
        }
        pdo_delete('bm_inbark_reply', "id IN ('" . implode("','", $deleteid) . "')");
        return true;
    }
}