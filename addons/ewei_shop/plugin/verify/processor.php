<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
require IA_ROOT . '/addons/ewei_shop/defines.php';
require EWEI_SHOP_INC . 'plugin/plugin_processor.php';
class VerifyProcessor extends PluginProcessor{
    public function __construct(){
        parent :: __construct('verify');
    }
    public function respond($weizan_0 = null){
        global $_W;
        $weizan_1 = $weizan_0 -> message;
        $weizan_2 = $weizan_0 -> message['from'];
        $weizan_3 = $weizan_0 -> message['content'];
        $weizan_4 = strtolower($weizan_1['msgtype']);
        $weizan_5 = strtolower($weizan_1['event']);
        if ($weizan_4 == 'text' || $weizan_5 == 'click'){
            $weizan_6 = pdo_fetch('select * from ' . tablename('ewei_shop_saler') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $weizan_2));
            if (empty($weizan_6)){
                return $this -> responseEmpty();
            }
            $weizan_7 = m('common') -> getSysset('trade');
            if (!$weizan_0 -> inContext){
                $weizan_0 -> beginContext();
                return $weizan_0 -> respText('请输入订单消费码:');
            }else if ($weizan_0 -> inContext && is_numeric($weizan_3)){
                $weizan_8 = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where verifycode=:verifycode and uniacid=:uniacid  limit 1', array(':verifycode' => $weizan_3, ':uniacid' => $_W['uniacid']));
                if (empty($weizan_8)){
                    return $weizan_0 -> respText('未找到要核销的订单,请重新输入!');
                }
                $weizan_9 = $weizan_8['id'];
                if (empty($weizan_8['isverify'])){
                    $weizan_0 -> endContext();
                    return $weizan_0 -> respText('订单无需核销!');
                }
                if (!empty($weizan_8['verified'])){
                    $weizan_0 -> endContext();
                    return $weizan_0 -> respText('此订单已核销，无需重复核销!');
                }
                if ($weizan_8['status'] != 1){
                    $weizan_0 -> endContext();
                    return $weizan_0 -> respText('订单未付款，无法核销!');
                }
                $weizan_10 = array();
                $weizan_11 = pdo_fetchall('select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,g.isverify,g.storeids from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $weizan_8['id']));
                foreach ($weizan_11 as $weizan_12){
                    if (!empty($weizan_12['storeids'])){
                        $weizan_10 = array_merge(explode(',', $weizan_12['storeids']), $weizan_10);
                    }
                }
                if (!empty($weizan_10)){
                    if (!empty($weizan_6['storeid'])){
                        if (!in_array($weizan_6['storeid'], $weizan_10)){
                            return $weizan_0 -> respText('您无此门店的核销权限!');
                        }
                    }
                }
                $weizan_13 = time();
                pdo_update('ewei_shop_order', array('status' => 3, 'sendtime' => $weizan_13, 'finishtime' => $weizan_13, 'verifytime' => $weizan_13, 'verified' => 1, 'verifyopenid' => $weizan_2, 'verifystoreid' => $weizan_6['storeid']), array('id' => $weizan_8['id']));
                m('notice') -> sendOrderMessage($weizan_9);
                if (p('commission')){
                    p('commission') -> checkOrderFinish($weizan_9);
                }
                $weizan_0 -> endContext();
                return $weizan_0 -> respText('核销成功!');
            }
        }
    }
    private function responseEmpty(){
        ob_clean();
        ob_start();
        echo '';
        ob_flush();
        ob_end_flush();
        exit(0);
    }
}
