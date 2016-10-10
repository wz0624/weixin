<?php
defined('IN_IA') or exit('Access Denied');
class Cyl_wxweizhangModule extends WeModule
{
    public function settingsDisplay($settings)
    {
        global $_W, $_GPC;
        load()->func('file');
        if (checksubmit()) {
            $data          = $_GPC['data'];
            $data['dsfgg'] = htmlspecialchars_decode($data['dsfgg']);
            mkdirs(MODULE_ROOT . '/cert');
            $r = true;
            if (!empty($_GPC['cert'])) {
                $ret = file_put_contents(MODULE_ROOT . '/cert/apiclient_cert.pem.' . $_W['uniacid'], trim($_GPC['cert']));
                $r   = $r && $ret;
            }
            if (!empty($_GPC['key'])) {
                $ret = file_put_contents(MODULE_ROOT . '/cert/apiclient_key.pem.' . $_W['uniacid'], trim($_GPC['key']));
                $r   = $r && $ret;
            }
            if (!empty($_GPC['ca'])) {
                $ret = file_put_contents(MODULE_ROOT . '/cert/rootca.pem.' . $_W['uniacid'], trim($_GPC['ca']));
                $r   = $r && $ret;
            }
            if (!$r) {
                message('证书保存失败, 请保证 /addons/cgt_qyhb/cert/ 目录可写');
            }
            if (!$this->saveSettings($data)) {
                message('保存信息失败', '', 'error');
            } else {
                message('保存信息成功', '', 'success');
            }
        }
        load()->func('tpl');
        include $this->template('setting');
    }
}