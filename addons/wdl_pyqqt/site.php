<?php
defined('IN_IA') or exit('Access Denied');
class wdl_pyqqtModuleSite extends WeModuleSite
{
    public function doWebPay()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $pindex  = max(1, intval($_GPC['page']));
        $psize   = 1;
        $lists   = pdo_fetchall("SELECT * FROM " . tablename('wdl_pyqqt_pay') . " WHERE uniacid = '{$uniacid}' ORDER BY add_time DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total   = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wdl_pyqqt_pay') . " WHERE uniacid = '{$uniacid}' ");
        $pager   = pagination($total, $pindex, $psize);
        foreach ($lists as $k => &$v) {
            $v['origin_user'] = $this->get_user_info($v['origin']);
            $v['want_user']   = $this->get_user_info($v['want']);
        }
        include $this->template('paylist');
    }
    public function doWebWithdraw()
    {
        global $_W, $_GPC;
        $uniacid = $_W['uniacid'];
        $pindex  = max(1, intval($_GPC['page']));
        $psize   = 1;
        $lists   = pdo_fetchall("SELECT * FROM " . tablename('wdl_pyqqt_withdraw') . " WHERE uniacid = '{$uniacid}' ORDER BY ctime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        $total   = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('wdl_pyqqt_withdraw') . " WHERE uniacid = '{$uniacid}' ");
        $pager   = pagination($total, $pindex, $psize);
        foreach ($lists as $k => &$v) {
            $v['user'] = $this->get_user_info($v['openid']);
        }
        include $this->template('withdrawlist');
    }
    public function doWebDeal_withdraw()
    {
        global $_W, $_GPC;
        $config = $this->module['config'];
        $config['withdraw_tip'] || $config['withdraw_tip'] = '朋友圈乞讨提现到账';
        if (!$_GPC['id']) {
            message('要操作的数据不存在', 'refresh', 'error');
        }
        $withdraw = pdo_get('wdl_pyqqt_withdraw', array(
            'id' => $_GPC['id']
        ));
        if ($withdraw['status'] != 0) {
            if ($withdraw['status'] == 1) {
                message('已提现的记录不能再次改变提现状态', $this->createWebUrl('withdraw'), 'error');
            } elseif ($withdraw['status'] == 2) {
                message('已驳回的记录不能再次改变提现状态', $this->createWebUrl('withdraw'), 'error');
            } else {
                message('操作有误', 'refresh', 'error');
            }
        }
        if ($_GPC['status'] == 2) {
            $res = pdo_update('wdl_pyqqt_withdraw', array(
                'status' => 2
            ), array(
                'id' => $_GPC['id']
            ));
            if ($res) {
                message('驳回提现成功', $this->createWebUrl('withdraw'), 'success');
            } else {
                message('驳回提现失败', $this->createWebUrl('withdraw'), 'error');
            }
        } elseif ($_GPC['status'] == 1) {
            $result = $this->mch_pay($withdraw['openid'], $withdraw['income'], $config['withdraw_tip']);
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                $res = pdo_update('wdl_pyqqt_withdraw', array(
                    'status' => 1,
                    'pay_type' => 1
                ), array(
                    'id' => $_GPC['id']
                ));
                if ($res) {
                    message('已同意此提现', $this->createWebUrl('withdraw'), 'success');
                } else {
                    message('同意提现失败', $this->createWebUrl('withdraw'), 'error');
                }
            } else {
                message('同意提现失败，错误原因为：给用户付款的时候出现错误：' . $result['err_code_des']);
            }
        } else {
            message('操作有误', $this->createWebUrl('withdraw'), 'error');
        }
    }
    public function mch_pay($openid, $money, $desc)
    {
        global $_W, $_GPC;
        $config           = $this->module['config'];
        $setting          = uni_setting($_W['uniacid'], array(
            'payment'
        ));
        $wechat           = $setting['payment']['wechat'];
        $sql              = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
        $row              = pdo_fetch($sql, array(
            ':acid' => $wechat['account']
        ));
        $wechat['appid']  = $row['key'];
        $wechat['secret'] = $row['secret'];
        include 'WechatPaySdk/WxPayPubHelper.php';
        $sslcert = MODULE_ROOT . '/apiclient_cert.pem';
        $sslkey  = MODULE_ROOT . '/apiclient_key.pem';
        file_put_contents($sslcert, $config['sslcert']);
        file_put_contents($sslkey, $config['sslkey']);
        $mchpay = new MchPay_pub($wechat['appid'], $wechat['mchid'], $wechat['signkey'], $wechat['secret']);
        $mchpay->setParameter('partner_trade_no', $_W['uniacid'] . time());
        $mchpay->setParameter('openid', $openid);
        $mchpay->setParameter('amount', $money * 100);
        $mchpay->setParameter('check_name', 'NO_CHECK');
        $mchpay->setParameter('desc', $desc);
        $result = $mchpay->getResult($sslcert, $sslkey);
        unlink($sslcert);
        unlink($sslkey);
        return $result;
    }
    public function doWebDelete()
    {
        global $_GPC;
        $sql    = 'SELECT `id` FROM ' . tablename('wdl_pyqqt_pay') . ' WHERE `id` = :id';
        $result = pdo_fetch($sql, array(
            ':id' => intval($_GPC['id'])
        ));
        if (!$result) {
            message('要删除的数据不存在', 'refresh');
        }
        if (pdo_delete('wdl_pyqqt_pay', array(
            'id' => intval($_GPC['id'])
        ))) {
            message('删除成功', referer(), 'success');
        }
    }
    public function doMobileIndex()
    {
        global $_W, $_GPC;
        $config       = $this->get_config($_W['openid']);
        $income       = $this->get_income($_W['openid']);
        $my           = $this->get_user_info($_W['openid']);
        $my['income'] = $income;
        $myorders     = $this->get_pay_orders($_W['openid']);
        foreach ($myorders as $k => &$v) {
            $v['income'] = $this->get_income($v['want']);
            $v['user']   = $this->get_user_info($v['want']);
        }
        $myorders  = $this->array_order($myorders);
        $allorders = $this->get_pay_orders();
        foreach ($allorders as $k => &$v) {
            $v['income'] = $this->get_income($v['origin']);
            $v['user']   = $this->get_user_info($v['origin']);
        }
        $allorders = $this->array_order($allorders);
        include $this->template('index');
    }
    public function get_user_info($openid = '')
    {
        global $_W, $_GPC;
        load()->model('mc');
        if ($openid == '') {
            $openid = $_W['openid'];
        }
        $user_info           = mc_fetch($openid);
        $user_info['openid'] = $openid;
        $weixin_user_info    = mc_fansinfo($openid);
        if ($weixin_user_info) {
            $user_info['fanid']    = $weixin_user_info['fanid'];
            $user_info['gender']   = $weixin_user_info['sex'];
            $user_info['follow']   = $weixin_user_info['follow'];
            $user_info['nickname'] = $weixin_user_info['tag']['nickname'];
            $user_info['avatar']   = $weixin_user_info['tag']['avatar'];
        }
        return $user_info;
    }
    public function array_order($arr)
    {
        $len = count($arr);
        for ($i = 1; $i < $len; $i++) {
            for ($k = 0; $k < $len - $i; $k++) {
                if ($arr[$k]['income'] < $arr[$k + 1]['income']) {
                    $tmp         = $arr[$k + 1];
                    $arr[$k + 1] = $arr[$k];
                    $arr[$k]     = $tmp;
                }
            }
        }
        return $arr;
    }
    public function get_config($openid = '')
    {
        global $_W, $_GPC;
        $config = $this->module['config'];
        if ($_W['container'] != 'wechat') {
            message('请在微信浏览器访问此页面', '');
        }
        if (empty($_W['fans']['nickname'])) {
            mc_oauth_userinfo();
        }
        if ($config['subscribe_url'] && !$_W['fans']['follow']) {
            message('', $config['subscribe_url']);
        }
        if ($openid == '') {
            $openid = $_W['openid'];
        }
        $config['index_page_title'] || $config['index_page_title'] = '如今我成为乞丐，这段友情是否还能继续？';
        $config['share_tip'] || $config['share_tip'] = '我也要加入丐帮争当帮主';
        $config['top_tip'] || $config['top_tip'] = '我坚信:人间有真情，人间有真爱！';
        $config['bottom_tip'] || $config['bottom_tip'] = '通知好友，好友支付后，金额将存进你的微信钱包';
        $config['share_title'] || $config['share_title'] = '如今我成为乞丐，这段友情是否还能继续？';
        $config['share_desc'] || $config['share_desc'] = '证明感情的时刻不要假装没看到哦';
        $config['link'] = $this->createMobileUrl('sleep', array(
            'openid' => $openid
        ));
        if (preg_match('/^\.\//', $config['link'])) {
            $config['link'] = str_replace('./', $_W['siteroot'] . 'app/', $config['link']);
        }
        $config['withdraw_line'] || $config['withdraw_line'] = 10;
        $config['withdraw_max'] || $config['withdraw_max'] = 10;
        $config['withdraw_discount'] || $config['withdraw_discount'] = 0.02;
        $config['withdraw_tip'] || $config['withdraw_tip'] = '朋友圈乞讨提现到账';
        $user                  = $this->get_user_info($openid);
        $config['share_title'] = str_replace('{nickname}', $user['nickname'], $config['share_title']);
        if ($config['share_cover']) {
            $config['share_cover'] = tomedia($config['share_cover']);
        } else {
            $config['share_cover'] = $user['avatar'];
        }
        $config['tip_1'] || $config['tip_1'] = '那些打赏Ta的人';
        $config['tip_2'] || $config['tip_2'] = '让我们一起来证明身边的真情！';
        $config['tip_3'] || $config['tip_3'] = '那些被很多人帮助或虐待的乞丐';
        $config['tip_4'] || $config['tip_4'] = '查看排行榜';
        $config['tip_5'] || $config['tip_5'] = '我也要加入丐帮争当帮主';
        $config['tip_6'] || $config['tip_6'] = '生成我的主页';
        $config['tip_7'] || $config['tip_7'] = '打赏Ta可以看到其他帮助过Ta的人喔！';
        $config['tip_8'] || $config['tip_8'] = '证明感情的时刻不要假装没看到哦';
        $config['tip_9'] || $config['tip_9'] = '打赏Ta';
        $config['tip_10'] || $config['tip_10'] = '支付成功后，你的好友将收到对应金额的零钱<br>同时可以查看已经支付过的人喔';
        $config['tip_11'] || $config['tip_11'] = '先不帮助Ta，加入丐帮看看谁打赏我';
        return $config;
    }
    public function get_pay_orders($openid = '')
    {
        global $_W, $_GPC;
        if ($openid != '') {
            $orders = pdo_fetchall('SELECT * FROM ' . tablename('wdl_pyqqt_pay') . ' WHERE uniacid = :uniacid AND origin = :origin ORDER BY add_time DESC', array(
                ':uniacid' => $_W['uniacid'],
                'origin' => $openid
            ));
        } else {
            $orders = pdo_fetchall('SELECT DISTINCT(origin) FROM ' . tablename('wdl_pyqqt_pay') . ' WHERE uniacid = :uniacid ORDER BY add_time DESC', array(
                ':uniacid' => $_W['uniacid']
            ));
        }
        return $orders;
    }
    public function get_income($openid)
    {
        global $_W, $_GPC;
        $orders = $this->get_pay_orders($openid);
        $income = 0;
        foreach ($orders as $k => $v) {
            $income += $v['price'];
        }
        return $income;
    }
    public function get_withdraw($openid = '')
    {
        global $_W, $_GPC;
        if ($openid == '') {
            $openid = $_W['openid'];
        }
        $withdraw = pdo_fetch('SELECT SUM(money) AS withdraw FROM ' . tablename('wdl_pyqqt_withdraw') . ' WHERE uniacid = :uniacid AND openid = :openid AND status = :status', array(
            ':uniacid' => $_W['uniacid'],
            ':openid' => $openid,
            ':status' => 1
        ));
        return $withdraw['withdraw'];
    }
    public function doMobileSleep()
    {
        global $_W, $_GPC;
        $config     = $this->get_config($_GPC['openid']);
        $myorder    = pdo_get('wdl_pyqqt_pay', array(
            'uniacid' => $_W['uniacid'],
            'origin' => $_GPC['openid'],
            'want' => $_W['openid']
        ));
        $wantorders = $this->get_pay_orders($_GPC['openid']);
        foreach ($wantorders as $k => &$v) {
            $v['income'] = $this->get_income($v['want']);
            $v['user']   = $this->get_user_info($v['want']);
        }
        $income         = $this->get_income($_GPC['openid']);
        $user           = $this->get_user_info($_GPC['openid']);
        $user['income'] = $income;
        $my             = $this->get_user_info($_W['openid']);
        include $this->template('sleep');
    }
    public function doMobileAccount()
    {
        global $_W, $_GPC;
        $config         = $this->get_config();
        $my             = $this->get_user_info($_W['openid']);
        $my['income']   = $this->get_income($_W['openid']);
        $my['withdraw'] = round($this->get_withdraw($_W['openid']), 2);
        $my['blance']   = round(floatval($my['income']) - floatval($my['withdraw']), 2);
        $payorders      = $this->get_pay_orders($_W['openid']);
        foreach ($payorders as $k => &$v) {
            $v['user'] = $this->get_user_info($v['want']);
        }
        $withdraw = pdo_fetchall('SELECT * FROM ' . tablename('wdl_pyqqt_withdraw') . ' WHERE uniacid = :uniacid AND openid = :openid ORDER BY ctime DESC', array(
            ':uniacid' => $_W['uniacid'],
            ':openid' => $_W['openid']
        ));
        include $this->template('account');
    }
    public function doMobileDeal_withdraw()
    {
        global $_W, $_GPC;
        if ($_W['isajax']) {
            $not_deal_withdraw = pdo_get('wdl_pyqqt_withdraw', array(
                'uniacid' => $_W['uniacid'],
                'openid' => $_W['openid'],
                'status' => 0
            ));
            if ($not_deal_withdraw) {
                $return['errcode'] = 0000;
                $return['errmsg']  = '你还有提现未处理，暂时不能继续提现';
                $return['url']     = $this->createMobileUrl('account');
            } else {
                $data['uniacid']  = $_W['uniacid'];
                $data['openid']   = $_W['openid'];
                $data['money']    = floatval($_GPC['withdraw_money']);
                $data['discount'] = floatval($_GPC['discount']);
                $data['fee']      = floatval($_GPC['fee']);
                $data['income']   = floatval($_GPC['income']);
                $data['status']   = 0;
                $data['ctime']    = time();
                $res              = pdo_insert('wdl_pyqqt_withdraw', $data);
                if ($res) {
                    $return['errcode'] = 1111;
                    $return['errmsg']  = '你的提现申请已提交，管理员审核通过后即可到账';
                    $return['url']     = $this->createMobileUrl('account');
                    $return['data']    = $data;
                } else {
                    $return['errcode'] = 0000;
                    $return['errmsg']  = '提现申请不成功，请稍后再试';
                    $return['url']     = $this->createMobileUrl('account');
                    $return['data']    = $data;
                }
            }
            echo json_encode($return);
        }
    }
    public function doMobileDeal_pay()
    {
        global $_W, $_GPC;
        if ($_GPC['orderid']) {
            $payorder         = pdo_get('wdl_pyqqt_pay', array(
                'want' => $_GPC['want'],
                'orderid' => $_GPC['orderid']
            ));
            $data['orderid']  = $_GPC['orderid'];
            $data['uniacid']  = $_W['uniacid'];
            $data['origin']   = $_GPC['origin'];
            $data['want']     = $_GPC['want'];
            $data['add_time'] = time();
            $data['price']    = floatval($_GPC['price']);
            if ($payorder) {
                $res = pdo_update('wdl_pyqqt_pay', $data, array(
                    'want' => $_GPC['want'],
                    'orderid' => $_GPC['orderid']
                ));
            } else {
                $res = pdo_insert('wdl_pyqqt_pay', $data);
            }
            if ($res) {
                $return['errcode'] = 1111;
                $return['errmsg']  = '支付成功';
                $return['url']     = $this->createMobileUrl('sleep', array(
                    'openid' => $_GPC['origin']
                ));
            } else {
                $return['errcode'] = 0000;
                $return['errmsg']  = '未知错误';
                $return['url']     = $this->createMobileUrl('sleep', array(
                    'openid' => $_GPC['origin']
                ));
            }
            echo json_encode($return);
        }
    }
    public function doMobileJson_pay()
    {
        global $_W, $_GPC;
        $data['orderid'] = $_GPC['orderid'];
        $data['price']   = $_GPC['price'];
        $data['origin']  = $_GPC['origin'];
        $data['want']    = $_GPC['want'];
        $jsApiParameters = $this->getJsApiParameters($data);
        echo json_encode($jsApiParameters);
    }
    public function getJsApiParameters($data = array())
    {
        global $_W, $_GPC;
        $setting          = uni_setting($_W['uniacid'], array(
            'payment'
        ));
        $wechat           = $setting['payment']['wechat'];
        $sql              = 'SELECT `key`,`secret` FROM ' . tablename('account_wechats') . ' WHERE `acid`=:acid';
        $row              = pdo_fetch($sql, array(
            ':acid' => $wechat['account']
        ));
        $wechat['appid']  = $row['key'];
        $wechat['secret'] = $row['secret'];
        include 'WechatPaySdk/WxPayPubHelper.php';
        $payConfig = $wechat;
        $jsApi     = new JsApi_pub($payConfig['appid'], $payConfig['mchid'], $payConfig['signkey'], $payConfig['secret']);
        $orderid   = $data['orderid'];
        if ($orderid == "") {
            $orderid = $data['single_orderid'];
        }
        $price        = $data['price'];
        $unifiedOrder = new UnifiedOrder_pub($payConfig['appid'], $payConfig['mchid'], $payConfig['signkey'], $payConfig['secret']);
        $unifiedOrder->setParameter("openid", $_W['openid']);
        $unifiedOrder->setParameter("body", $orderid);
        $unifiedOrder->setParameter("out_trade_no", $orderid);
        $unifiedOrder->setParameter("total_fee", $price * 100);
        $unifiedOrder->setParameter("notify_url", 'http://paysdk.weixin.qq.com/example/notify.php');
        $unifiedOrder->setParameter("trade_type", "JSAPI");
        $unifiedOrder->setParameter("attach", 'uniacid=' . $_W['uniacid'] . '&wecha_id=' . $_W['openid'] . '&from=' . $data['from']);
        $prepay_id = $unifiedOrder->getPrepayId();
        $jsApi->setPrepayId($prepay_id);
        $jsApiParameters = $jsApi->getParameters();
        return $jsApiParameters;
    }
}