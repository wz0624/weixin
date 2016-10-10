<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
if (!class_exists('VirtualModel')){
    class VirtualModel extends PluginModel{
        public function updateGoodsStock($weizan_0 = 0){
            global $_W, $_GPC;
            $weizan_1 = pdo_fetch('select virtual from ' . tablename('ewei_shop_goods') . ' where id=:id and type=3 and uniacid=:uniacid limit 1', array(':id' => $weizan_0, ':uniacid' => $_W['uniacid']));
            if (empty($weizan_1)){
                return;
            }
            $weizan_2 = 0;
            if (!empty($weizan_1['virtual'])){
                $weizan_2 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_virtual_data') . ' where typeid=:typeid and uniacid=:uniacid and openid=\'\' limit 1', array(':typeid' => $weizan_1['virtual'], ':uniacid' => $_W['uniacid']));
            }else{
                $weizan_3 = array();
                $weizan_4 = pdo_fetchall('select id, virtual from ' . tablename('ewei_shop_goods_option') . " where goodsid=$weizan_0");
                foreach ($weizan_4 as $weizan_5){
                    if (empty($weizan_5['virtual'])){
                        continue;
                    }
                    $weizan_6 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_virtual_data') . ' where typeid=:typeid and uniacid=:uniacid and openid=\'\' limit 1', array(':typeid' => $weizan_5['virtual'], ':uniacid' => $_W['uniacid']));
                    pdo_update('ewei_shop_goods_option', array('stock' => $weizan_6), array('id' => $weizan_5['id']));
                    if (!in_array($weizan_5['virtual'], $weizan_3)){
                        $weizan_3[] = $weizan_5['virtual'];
                        $weizan_2 += $weizan_6;
                    }
                }
            }
            pdo_update('ewei_shop_goods', array('total' => $weizan_2), array('id' => $weizan_0));
        }
        public function updateStock($weizan_7 = 0){
            global $_W;
            $weizan_8 = array();
            $weizan_1 = pdo_fetchall('select id from ' . tablename('ewei_shop_goods') . ' where type=3 and virtual=:virtual and uniacid=:uniacid limit 1', array(':virtual' => $weizan_7, ':uniacid' => $_W['uniacid']));
            foreach ($weizan_1 as $weizan_9){
                $weizan_8[] = $weizan_9['id'];
            }
            $weizan_4 = pdo_fetchall('select id, goodsid from ' . tablename('ewei_shop_goods_option') . ' where virtual=:virtual and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':virtual' => $weizan_7));
            foreach ($weizan_4 as $weizan_5){
                if (!in_array($weizan_5['goodsid'], $weizan_8)){
                    $weizan_8[] = $weizan_5['goodsid'];
                }
            }
            foreach ($weizan_8 as $weizan_10){
                $this -> updateGoodsStock($weizan_10);
            }
        }
        public function pay($weizan_11){
            global $_W, $_GPC;
            $weizan_1 = pdo_fetch('select id,goodsid,total,realprice from ' . tablename('ewei_shop_order_goods') . ' where  orderid=:orderid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':orderid' => $weizan_11['id']));
            $weizan_9 = pdo_fetch('select id,credit,sales,salesreal from ' . tablename('ewei_shop_goods') . ' where  id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $weizan_1['goodsid']));
            $weizan_12 = pdo_fetchall('SELECT id,typeid,fields FROM ' . tablename('ewei_shop_virtual_data') . ' WHERE typeid=:typeid and openid=:openid and uniacid=:uniacid order by rand() limit ' . $weizan_1['total'], array(':openid' => '', ':typeid' => $weizan_11['virtual'], ':uniacid' => $_W['uniacid']));
            $weizan_13 = pdo_fetch('select fields from ' . tablename('ewei_shop_virtual_type') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $weizan_11['virtual'], ':uniacid' => $_W['uniacid']));
            $weizan_14 = iunserializer($weizan_13['fields'], true);
            $weizan_15 = array();
            $weizan_16 = array();
            foreach ($weizan_12 as $weizan_17){
                $weizan_15[] = $weizan_17['fields'];
                $weizan_18 = array();
                $weizan_19 = iunserializer($weizan_17['fields']);
                foreach($weizan_19 as $weizan_20 => $weizan_21){
                    $weizan_18[] = $weizan_14[$weizan_20] . ': ' . $weizan_21;
                }
                $weizan_16[] = implode(' ', $weizan_18);
                pdo_update('ewei_shop_virtual_data', array('openid' => $weizan_11['openid'], 'orderid' => $weizan_11['id'], 'ordersn' => $weizan_11['ordersn'], 'price' => round($weizan_1['realprice'] / $weizan_1['total'], 2), 'usetime' => time()), array('id' => $weizan_17['id']));
                pdo_update('ewei_shop_virtual_type', 'usedata=usedata+1', array('id' => $weizan_17['typeid']));
                $this -> updateStock($weizan_17['typeid']);
            }
            $weizan_16 = implode(' ', $weizan_16);
            $weizan_15 = '[' . implode(',', $weizan_15) . ']';
            $weizan_22 = time();
            pdo_update('ewei_shop_order', array('virtual_info' => $weizan_15, 'virtual_str' => $weizan_16, 'status' => '3', 'paytime' => $weizan_22, 'sendtime' => $weizan_22, 'finishtime' => $weizan_22), array('id' => $weizan_11['id']));
            if ($weizan_11['deductcredit2'] > 0){
                $weizan_23 = m('common') -> getSysset('shop');
                m('member') -> setCredit($weizan_11['openid'], 'credit2', - $weizan_11['deductcredit2'], array(0, $weizan_23['name'] . "余额抵扣: {$weizan_11['deductcredit2']} 订单号: " . $weizan_11['ordersn']));
            }
            $weizan_24 = $weizan_1['total'] * $weizan_9['credit'];
            if($weizan_24 > 0){
                $weizan_23 = m('common') -> getSysset('shop');
                m('member') -> setCredit($weizan_11['openid'], 'credit1', $weizan_24, array(0, $weizan_23['name'] . '购物积分 订单号: ' . $weizan_11['ordersn']));
            }
            $weizan_25 = pdo_fetchcolumn('select ifnull(sum(total),0) from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where og.goodsid=:goodsid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':goodsid' => $weizan_9['id'], ':uniacid' => $_W['uniacid']));
            pdo_update('ewei_shop_goods', array('salesreal' => $weizan_25), array('id' => $weizan_9['id']));
            m('member') -> upgradeLevel($weizan_11['openid']);
            m('notice') -> sendOrderMessage($weizan_11['id']);
            if(p('coupon') && !empty($weizan_11['couponid'])){
                p('coupon') -> backConsumeCoupon($weizan_11['id']);
            }
            if (p('commission')){
                p('commission') -> checkOrderPay($weizan_11['id']);
                p('commission') -> checkOrderFinish($weizan_11['id']);
            }
        }
        public function perms(){
            return array('virtual' => array('text' => $this -> getName(), 'isplugin' => true, 'child' => array('temp' => array('text' => '模板', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'data' => array('text' => '数据', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log', 'import' => '导入-log', 'export' => '导出已使用数据-log'), 'category' => array('text' => '分类', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'))));
        }
    }
}
