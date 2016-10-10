<?php
defined('IN_IA') or exit('Access Denied');
class Meepo_credit1ModuleSite extends WeModuleSite
{
    public function doMobileIndex()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $user    = mc_fetch($_W['openid']);
        $content = 'credit/index';
        $title   = '积分';
        include $this->template('index');
    }
    public function doMobileDetail()
    {
        global $_W, $_GPC;
        $detail = htmlspecialchars_decode($this->module['config']['detail']);
        global $_W, $_GPC;
        load()->model('mc');
        $user    = mc_fetch($_W['openid']);
        $content = 'credit/detail';
        $title   = '充值说明';
        include $this->template('index');
    }
    public function doMobilePay()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $user    = mc_fetch($_W['openid']);
        $content = 'pay/index';
        $title   = '积分充值';
        $fv      = !empty($this->module['config']['credit1_lv']) ? floatval($this->module['config']['credit1_lv']) : 1;
        if ($_W['ispost']) {
            $num = intval($_GPC['num']);
            if (empty($num) || $num <= 0) {
                message('充值积分必须是大于0的整数', referer(), success);
            }
            $data            = array();
            $data['tid']     = "C" . random(8, true) . time();
            $data['setting'] = serialize(array(
                'num' => $num,
                'openid' => $_W['openid']
            ));
            $data['status']  = 0;
            $data['fee']     = $num * $fv;
            $data['uniacid'] = $_W['uniacid'];
            $data['openid']  = $_W['openid'];
            $data['time']    = time();
            pdo_insert('meepo_credit1_paylog', $data);
            die(json_encode(array(
                'tid' => $data['tid'],
                'status' => 1
            )));
        }
        include $this->template('index');
    }
    public function doMobileConfirm()
    {
        global $_W, $_GPC;
        $tid               = trim($_GPC['tid']);
        $sql               = "SELECT * FROM " . tablename('meepo_credit1_paylog') . " WHERE tid = :tid";
        $params            = array(
            ':tid' => $tid
        );
        $paylog            = pdo_fetch($sql, $params);
        $params            = array();
        $params['fee']     = floatval($paylog['fee']);
        $params['tid']     = $paylog['tid'];
        $params['ordersn'] = $paylog['tid'];
        $params['title']   = '积分充值';
        $this->pay($params, array());
        exit();
    }
    public function doMobileLog()
    {
        global $_W, $_GPC;
        load()->model('mc');
        $act     = trim($_GPC['act']);
        $page    = intval($_GPC['page']) > 0 ? intval($_GPC['page']) : 1;
        $psize   = 10;
        $sql     = "SELECT COUNT(*) FROM " . tablename('meepo_credit1_paylog') . " WHERE openid = :openid AND uniacid = :uniacid ORDER BY time DESC limit " . ($page - 1) * $psize . "," . $psize;
        $params  = array(
            ':openid' => $_W['openid'],
            ':uniacid' => $_W['uniacid']
        );
        $total   = pdo_fetchcolumn($sql, $params);
        $hasmore = $total > $page * $psize;
        if (empty($act)) {
            $act = 'list';
        }
        if ($act == 'more') {
            $sql    = "SELECT * FROM " . tablename('meepo_credit1_paylog') . " WHERE openid = :openid AND uniacid = :uniacid ORDER BY time DESC limit " . ($page - 1) * $psize . "," . $psize;
            $params = array(
                ':openid' => $_W['openid'],
                ':uniacid' => $_W['uniacid']
            );
            $list   = pdo_fetchall($sql, $params);
            $lists  = array();
            if (!empty($list)) {
                foreach ($list as $li) {
                    $li['time'] = date('Y-m-d', $li['time']);
                    $lists[]    = $li;
                }
                ob_clean();
                ob_start();
                include $this->template('/credit/log_more');
                $contents = ob_get_contents();
                ob_clean();
                $data           = array();
                $data['data']   = $contents;
                $data['status'] = 0;
            } else {
                $data           = array();
                $data['status'] = 1;
                $data['data']   = '';
            }
            die(json_encode($data));
        }
        if ($act == 'list') {
            $user    = mc_fetch($_W['openid']);
            $content = 'credit/log';
            $title   = '积分充值记录';
            $sql     = "SELECT * FROM " . tablename('meepo_credit1_paylog') . " WHERE openid = :openid AND uniacid = :uniacid ORDER BY time DESC limit " . ($page - 1) * $psize . "," . $psize;
            $params  = array(
                ':openid' => $_W['openid'],
                ':uniacid' => $_W['uniacid']
            );
            $list    = pdo_fetchall($sql, $params);
            $lists   = array();
            foreach ($list as $li) {
                $li['time'] = date('Y-m-d', $li['time']);
                $lists[]    = $li;
            }
            include $this->template('index');
        }
    }
    public function payResult($params)
    {
        global $_W;
        $tid    = $params['tid'];
        $sql    = "SELECT * FROM " . tablename('meepo_credit1_paylog') . " WHERE tid = :tid";
        $par    = array(
            ':tid' => $tid
        );
        $paylog = pdo_fetch($sql, $par);
        if ($params['result'] == 'success') {
            pdo_update('meepo_credit1_paylog', array(
                'status' => 1
            ), array(
                'id' => $paylog['id']
            ));
            $setting = unserialize($paylog['setting']);
            load()->model('mc');
            $uid = mc_openid2uid($setting['openid']);
            mc_credit_update($uid, 'credit1', $setting['num'], array(
                $uid,
                '用户积分充值',
                '0',
                '0'
            ));
            $remark = '用户充值' . $fee . '积分';
            mc_notice_recharge($setting['openid'], $uid, $setting['num'], '', $remark);
        }
        if ($params['result'] == 'success') {
            message('支付成功！', $this->createMobileUrl('index'), 'success');
        } else {
            message('支付失败！', $this->createMobileUrl('index'), 'error');
        }
    }
    public function doWebState()
    {
        global $_W, $_GPC;
        if ($_GPC['act'] == 'delete') {
            pdo_delete('meepo_credit1_paylog', array(
                'id' => intval($_GPC['id'])
            ));
            die();
        }
        $sql    = "SELECT * FROM " . tablename('meepo_credit1_paylog') . " WHERE uniacid = :uniacid ORDER BY time DESC";
        $params = array(
            ':uniacid' => $_W['uniacid']
        );
        $lists  = pdo_fetchall($sql, $params);
        load()->model('mc');
        foreach ($lists as $li) {
            $user           = mc_fetch($li['openid']);
            $li['nickname'] = $user['nickname'];
            $li['uid']      = $user['uid'];
            $li['avatar']   = $user['avatar'];
            $li['time']     = date('Y-m-d', $li['time']);
            if ($li['status'] == 1) {
                $li['status_title'] = '已支付';
                $li['status_label'] = 'success';
            } else {
                $li['status_title'] = '失败';
                $li['status_label'] = 'danger';
            }
            $list[] = $li;
        }
        include $this->template('state');
    }
}