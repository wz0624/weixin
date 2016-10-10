<?php
class FlashWemoduSite extends WeModuleSite
{
    protected function pay($params = array())
    {
        global $_W;
        if (!$this->inMobile) {
            message('支付功能只能在手机上使用');
        }
        if (empty($_W['member']['uid'])) {
            checkauth();
        }
        $params['module'] = $this->module['name'];
        $pars             = array();
        $pars[':uniacid'] = $_W['uniacid'];
        $pars[':module']  = $params['module'];
        $pars[':tid']     = $params['tid'];
        if ($params['fee'] <= 0) {
            $pars['from']   = 'return';
            $pars['result'] = 'success';
            $pars['type']   = 'alipay';
            $pars['tid']    = $params['tid'];
            $site           = WeUtility::createModuleSite($pars[':module']);
            $method         = 'payResult';
            if (method_exists($site, $method)) {
                exit($site->$method($pars));
            }
        }
        $sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid';
        $log = pdo_fetch($sql, $pars);
        if (!empty($log) && $log['status'] == '1') {
            message('这个订单已经支付成功, 不需要重复支付.');
        }
        $setting = uni_setting($_W['uniacid'], array(
            'payment',
            'creditbehaviors'
        ));
        if (!is_array($setting['payment'])) {
            message('没有有效的支付方式, 请联系网站管理员.');
        }
        $pay = $setting['payment'];
        if (!empty($pay['credit']['switch'])) {
            $credtis = mc_credit_fetch($_W['member']['uid']);
        }
        include $this->template('common/paycenter');
    }
    public function payResult($ret)
    {
        global $_W;
        if ($ret['from'] == 'return') {
            if ($ret['type'] == 'credit2') {
                message('已经成功支付', url('mobile/channel', array(
                    'name' => 'index',
                    'weid' => $_W['weid']
                )));
            } else {
                message('已经成功支付', '../../' . url('mobile/channel', array(
                    'name' => 'index',
                    'weid' => $_W['weid']
                )));
            }
        }
    }
}