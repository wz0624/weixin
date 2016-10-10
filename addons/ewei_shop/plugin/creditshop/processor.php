<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
require IA_ROOT . '/addons/ewei_shop/defines.php';
require EWEI_SHOP_INC . 'plugin/plugin_processor.php';
class CreditshopProcessor extends PluginProcessor{
    public function __construct(){
        parent :: __construct('creditshop');
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
            if (!$weizan_0 -> inContext){
                $weizan_0 -> beginContext();
                return $weizan_0 -> respText('请输入兑换码:');
            }else if ($weizan_0 -> inContext && is_numeric($weizan_3)){
                $weizan_7 = pdo_fetch('select * from ' . tablename('ewei_shop_creditshop_log') . ' where eno=:eno and uniacid=:uniacid  limit 1', array(':eno' => $weizan_3, ':uniacid' => $_W['uniacid']));
                if (empty($weizan_7)){
                    return $weizan_0 -> respText('未找到要兑换码,请重新输入!');
                }
                $weizan_8 = $weizan_7['id'];
                if (empty($weizan_7)){
                    return $weizan_0 -> respText('未找到要兑换码,请重新输入!');
                }
                if (empty($weizan_7['status'])){
                    return $weizan_0 -> respText('无效兑换记录!');
                }
                if ($weizan_7['status'] >= 3){
                    return $weizan_0 -> respText('此记录已兑换过了!');
                }
                $weizan_9 = m('member') -> getMember($weizan_7['openid']);
                $weizan_10 = $this -> model -> getGoods($weizan_7['goodsid'], $weizan_9);
                if (empty($weizan_10['id'])){
                    return $weizan_0 -> respText('商品记录不存在!');
                }
                if (empty($weizan_10['isverify'])){
                    $weizan_0 -> endContext();
                    return $weizan_0 -> respText('此商品不支持线下兑换!');
                }
                if (!empty($weizan_10['type'])){
                    if ($weizan_7['status'] <= 1){
                        return $weizan_0 -> respText('未中奖，不能兑换!');
                    }
                }
                if ($weizan_10['money'] > 0 && empty($weizan_7['paystatus'])){
                    return $weizan_0 -> respText('未支付，无法进行兑换!');
                }
                if ($weizan_10['dispatch'] > 0 && empty($weizan_7['dispatchstatus'])){
                    return $weizan_0 -> respText('未支付运费，无法进行兑换!');
                }
                $weizan_11 = explode(',', $weizan_10['storeids']);
                if (!empty($weizan_12)){
                    if (!empty($weizan_6['storeid'])){
                        if (!in_array($weizan_6['storeid'], $weizan_12)){
                            return $weizan_0 -> respText('您无此门店的兑换权限!');
                        }
                    }
                }
                $weizan_13 = time();
                pdo_update('ewei_shop_creditshop_log', array('status' => 3, 'usetime' => $weizan_13, 'verifyopenid' => $weizan_2), array('id' => $weizan_7['id']));
                $this -> model -> sendMessage($weizan_8);
                $weizan_0 -> endContext();
                return $weizan_0 -> respText('兑换成功!');
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
