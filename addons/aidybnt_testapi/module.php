<?php
/**
 * Created by 蓝森林.
 * 这不是一个开源版本的软件,程序版权归 蓝森林 所有
 * QQ:40221015
 * Date: 2015-05-19
 * Time: 10:44
 */
defined('IN_IA') or exit('Access Denied');

class Aidybnt_testapiModule extends WeModule {
    public $tablename = 'aaidybnt_testapi';
    public $tablename2 = 'mc_mapping_fans';
    public $tablename3 = 'account';
    private $_acid = '';

    public function fieldsFormDisplay($rid = 0) {
        global $_W,$_GPC;

        $sql = 'SELECT fanid FROM' . tablename($this->tablename2) . 'WHERE uniacid = :uniacid';
        $_rows = count(pdo_fetchall($sql, array(':uniacid' => $_W['uniacid'])));

        $sql2 = 'SELECT fanid FROM' . tablename($this->tablename2) . 'WHERE uniacid = :uniacid AND follow = 1';
        $_rows2 = count(pdo_fetchall($sql2, array(':uniacid' => $_W['uniacid'])));

        $sql3 = "SELECT * FROM " . tablename($this->tablename) . " WHERE rid = :rid";
        $reply = pdo_fetch($sql3, array(':rid' => $rid));

        $sql_acid = "SELECT `acid` FROM" . tablename($this->tablename3) . "WHERE uniacid = :uniacid";
        $rel_acid = pdo_fetchall($sql_acid, array(':uniacid' => $_W['uniacid']));
        $rel_acid_count = count(pdo_fetchall($sql_acid, array(':uniacid' => $_W['uniacid'])));

        include $this->template('rule');
    }

    public function fieldsFormValidate($rid = 0) {
        global $_GPC;

        $this->_acid = $_GPC['radio_acid'];
        if (empty($this->_acid)) {
            return '请选择对应的公众号';
        }

        $_num = $_GPC['num'];
        if (is_numeric($_num) && $_num >= 0 && strlen($_num) <= 7 || empty($_num)) {
            return '';
        } else {
            return '请填写合法数值';
        }
    }

    public function fieldsFormSubmit($rid) {
        global $_GPC;
        $sql = 'DELETE FROM ' . tablename($this->tablename) . ' WHERE `rid`=:rid';
        $pars = array();
        $pars[':rid'] = $rid;
        pdo_query($sql, $pars);
        pdo_insert($this->tablename, array('rid' => $rid, 'acid' => $this->_acid, 'num' => $_GPC['num'], 'content' => '微信返回的数据：'));
        return true;
    }

    public function ruleDeleted($rid) {
        pdo_delete($this->tablename, array('rid' => $rid));
    }
}
