<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
define('TM_COMMISSION_AGENT_NEW', 'commission_agent_new');
define('TM_COMMISSION_ORDER_PAY', 'commission_order_pay');
define('TM_COMMISSION_ORDER_FINISH', 'commission_order_finish');
define('TM_COMMISSION_APPLY', 'commission_apply');
define('TM_COMMISSION_CHECK', 'commission_check');
define('TM_COMMISSION_PAY', 'commission_pay');
define('TM_COMMISSION_UPGRADE', 'commission_upgrade');
define('TM_COMMISSION_BECOME', 'commission_become');
if (!class_exists('CommissionModel')){
    class CommissionModel extends PluginModel{
        public function getSet($weizan_0 = 0){
            $weizan_1 = parent :: getSet($weizan_0);
            $weizan_1['texts'] = array('agent' => empty($weizan_1['texts']['agent']) ? '分销商' : $weizan_1['texts']['agent'], 'shop' => empty($weizan_1['texts']['shop']) ? '小店' : $weizan_1['texts']['shop'], 'myshop' => empty($weizan_1['texts']['myshop']) ? '我的小店' : $weizan_1['texts']['myshop'], 'center' => empty($weizan_1['texts']['center']) ? '分销中心' : $weizan_1['texts']['center'], 'become' => empty($weizan_1['texts']['become']) ? '成为分销商' : $weizan_1['texts']['become'], 'withdraw' => empty($weizan_1['texts']['withdraw']) ? '提现' : $weizan_1['texts']['withdraw'], 'commission' => empty($weizan_1['texts']['commission']) ? '佣金' : $weizan_1['texts']['commission'], 'commission1' => empty($weizan_1['texts']['commission1']) ? '分销佣金' : $weizan_1['texts']['commission1'], 'commission_total' => empty($weizan_1['texts']['commission_total']) ? '累计佣金' : $weizan_1['texts']['commission_total'], 'commission_ok' => empty($weizan_1['texts']['commission_ok']) ? '可提现佣金' : $weizan_1['texts']['commission_ok'], 'commission_apply' => empty($weizan_1['texts']['commission_apply']) ? '已申请佣金' : $weizan_1['texts']['commission_apply'], 'commission_check' => empty($weizan_1['texts']['commission_check']) ? '待打款佣金' : $weizan_1['texts']['commission_check'], 'commission_lock' => empty($weizan_1['texts']['commission_lock']) ? '未结算佣金' : $weizan_1['texts']['commission_lock'], 'commission_detail' => empty($weizan_1['texts']['commission_detail']) ? '佣金明细' : $weizan_1['texts']['commission_detail'], 'commission_pay' => empty($weizan_1['texts']['commission_pay']) ? '成功提现佣金' : $weizan_1['texts']['commission_pay'], 'order' => empty($weizan_1['texts']['order']) ? '分销订单' : $weizan_1['texts']['order'], 'myteam' => empty($weizan_1['texts']['myteam']) ? '我的团队' : $weizan_1['texts']['myteam'], 'c1' => empty($weizan_1['texts']['c1']) ? '一级' : $weizan_1['texts']['c1'], 'c2' => empty($weizan_1['texts']['c2']) ? '二级' : $weizan_1['texts']['c2'], 'c3' => empty($weizan_1['texts']['c3']) ? '三级' : $weizan_1['texts']['c3'], 'mycustomer' => empty($weizan_1['texts']['mycustomer']) ? '我的客户' : $weizan_1['texts']['mycustomer'],);
            return $weizan_1;
        }
        public function calculate($weizan_2 = 0, $weizan_3 = true){
            global $_W;
            $weizan_1 = $this -> getSet();
            $weizan_4 = $this -> getLevels();
            $weizan_5 = pdo_fetchcolumn('select agentid from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $weizan_2));
            $weizan_6 = pdo_fetchall('select og.id,og.realprice,og.total,g.hascommission,g.nocommission, g.commission1_rate,g.commission1_pay,g.commission2_rate,g.commission2_pay,g.commission3_rate,g.commission3_pay,og.commissions from ' . tablename('ewei_shop_order_goods') . '  og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id = og.goodsid' . ' where og.orderid=:orderid and og.uniacid=:uniacid', array(':orderid' => $weizan_2, ':uniacid' => $_W['uniacid']));
            if ($weizan_1['level'] > 0){
                foreach ($weizan_6 as & $weizan_7){
                    $weizan_8 = $weizan_7['realprice'];
                    if (empty($weizan_7['nocommission'])){
                        if ($weizan_7['hascommission'] == 1){
                            $weizan_7['commission1'] = array('default' => $weizan_1['level'] >= 1 ? ($weizan_7['commission1_rate'] > 0 ? round($weizan_7['commission1_rate'] * $weizan_8 / 100, 2) . "" : round($weizan_7['commission1_pay'] * $weizan_7['total'], 2)) : 0);
                            $weizan_7['commission2'] = array('default' => $weizan_1['level'] >= 2 ? ($weizan_7['commission2_rate'] > 0 ? round($weizan_7['commission2_rate'] * $weizan_8 / 100, 2) . "" : round($weizan_7['commission2_pay'] * $weizan_7['total'], 2)) : 0);
                            $weizan_7['commission3'] = array('default' => $weizan_1['level'] >= 3 ? ($weizan_7['commission3_rate'] > 0 ? round($weizan_7['commission3_rate'] * $weizan_8 / 100, 2) . "" : round($weizan_7['commission3_pay'] * $weizan_7['total'], 2)) : 0);
                            foreach ($weizan_4 as $weizan_9){
                                $weizan_7['commission1']['level' . $weizan_9['id']] = $weizan_7['commission1_rate'] > 0 ? round($weizan_7['commission1_rate'] * $weizan_8 / 100, 2) . "" : round($weizan_7['commission1_pay'] * $weizan_7['total'], 2);
                                $weizan_7['commission2']['level' . $weizan_9['id']] = $weizan_7['commission2_rate'] > 0 ? round($weizan_7['commission2_rate'] * $weizan_8 / 100, 2) . "" : round($weizan_7['commission2_pay'] * $weizan_7['total'], 2);
                                $weizan_7['commission3']['level' . $weizan_9['id']] = $weizan_7['commission3_rate'] > 0 ? round($weizan_7['commission3_rate'] * $weizan_8 / 100, 2) . "" : round($weizan_7['commission3_pay'] * $weizan_7['total'], 2);
                            }
                        }else{
                            $weizan_7['commission1'] = array('default' => $weizan_1['level'] >= 1 ? round($weizan_1['commission1'] * $weizan_8 / 100, 2) . "" : 0);
                            $weizan_7['commission2'] = array('default' => $weizan_1['level'] >= 2 ? round($weizan_1['commission2'] * $weizan_8 / 100, 2) . "" : 0);
                            $weizan_7['commission3'] = array('default' => $weizan_1['level'] >= 3 ? round($weizan_1['commission3'] * $weizan_8 / 100, 2) . "" : 0);
                            foreach ($weizan_4 as $weizan_9){
                                $weizan_7['commission1']['level' . $weizan_9['id']] = $weizan_1['level'] >= 1 ? round($weizan_9['commission1'] * $weizan_8 / 100, 2) . "" : 0;
                                $weizan_7['commission2']['level' . $weizan_9['id']] = $weizan_1['level'] >= 2 ? round($weizan_9['commission2'] * $weizan_8 / 100, 2) . "" : 0;
                                $weizan_7['commission3']['level' . $weizan_9['id']] = $weizan_1['level'] >= 3 ? round($weizan_9['commission3'] * $weizan_8 / 100, 2) . "" : 0;
                            }
                        }
                    }else{
                        $weizan_7['commission1'] = array('default' => 0);
                        $weizan_7['commission2'] = array('default' => 0);
                        $weizan_7['commission3'] = array('default' => 0);
                        foreach ($weizan_4 as $weizan_9){
                            $weizan_7['commission1']['level' . $weizan_9['id']] = 0;
                            $weizan_7['commission2']['level' . $weizan_9['id']] = 0;
                            $weizan_7['commission3']['level' . $weizan_9['id']] = 0;
                        }
                    }
                    if ($weizan_3){
                        $weizan_10 = array('level1' => 0, 'level2' => 0, 'level3' => 0);
                        if (!empty($weizan_5)){
                            $weizan_11 = m('member') -> getMember($weizan_5);
                            if ($weizan_11['isagent'] == 1 && $weizan_11['status'] == 1){
                                $weizan_12 = $this -> getLevel($weizan_11['openid']);
                                $weizan_10['level1'] = empty($weizan_12) ? round($weizan_7['commission1']['default'], 2) : round($weizan_7['commission1']['level' . $weizan_12['id']], 2);
                                if (!empty($weizan_11['agentid'])){
                                    $weizan_13 = m('member') -> getMember($weizan_11['agentid']);
                                    $weizan_14 = $this -> getLevel($weizan_13['openid']);
                                    $weizan_10['level2'] = empty($weizan_14) ? round($weizan_7['commission2']['default'], 2) : round($weizan_7['commission2']['level' . $weizan_14['id']], 2);
                                    if (!empty($weizan_13['agentid'])){
                                        $weizan_15 = m('member') -> getMember($weizan_13['agentid']);
                                        $weizan_16 = $this -> getLevel($weizan_15['openid']);
                                        $weizan_10['level3'] = empty($weizan_16) ? round($weizan_7['commission3']['default'], 2) : round($weizan_7['commission3']['level' . $weizan_16['id']], 2);
                                    }
                                }
                            }
                        }
                        pdo_update('ewei_shop_order_goods', array('commission1' => iserializer($weizan_7['commission1']), 'commission2' => iserializer($weizan_7['commission2']), 'commission3' => iserializer($weizan_7['commission3']), 'commissions' => iserializer($weizan_10), 'nocommission' => $weizan_7['nocommission']), array('id' => $weizan_7['id']));
                    }
                }
                unset($weizan_7);
            }
            return $weizan_6;
        }
        public function getOrderCommissions($weizan_2 = 0, $weizan_17 = 0){
            global $_W;
            $weizan_1 = $this -> getSet();
            $weizan_5 = pdo_fetchcolumn('select agentid from ' . tablename('ewei_shop_order') . ' where id=:id limit 1', array(':id' => $weizan_2));
            $weizan_6 = pdo_fetch('select commission1,commission2,commission3 from ' . tablename('ewei_shop_order_goods') . ' where id=:id and orderid=:orderid and uniacid=:uniacid and nocommission=0 limit 1', array(':id' => $weizan_17, ':orderid' => $weizan_2, ':uniacid' => $_W['uniacid']));
            $weizan_10 = array('level1' => 0, 'level2' => 0, 'level3' => 0);
            if ($weizan_1['level'] > 0){
                $weizan_18 = iunserializer($weizan_6['commission1']);
                $weizan_19 = iunserializer($weizan_6['commission2']);
                $weizan_20 = iunserializer($weizan_6['commission3']);
                if (!empty($weizan_5)){
                    $weizan_11 = m('member') -> getMember($weizan_5);
                    if ($weizan_11['isagent'] == 1 && $weizan_11['status'] == 1){
                        $weizan_12 = $this -> getLevel($weizan_11['openid']);
                        $weizan_10['level1'] = empty($weizan_12) ? round($weizan_18['default'], 2) : round($weizan_18['level' . $weizan_12['id']], 2);
                        if (!empty($weizan_11['agentid'])){
                            $weizan_13 = m('member') -> getMember($weizan_11['agentid']);
                            $weizan_14 = $this -> getLevel($weizan_13['openid']);
                            $weizan_10['level2'] = empty($weizan_14) ? round($weizan_19['default'], 2) : round($weizan_19['level' . $weizan_14['id']], 2);
                            if (!empty($weizan_13['agentid'])){
                                $weizan_15 = m('member') -> getMember($weizan_13['agentid']);
                                $weizan_16 = $this -> getLevel($weizan_15['openid']);
                                $weizan_10['level3'] = empty($weizan_16) ? round($weizan_20['default'], 2) : round($weizan_20['level' . $weizan_16['id']], 2);
                            }
                        }
                    }
                }
            }
            return $weizan_10;
        }
        public function getInfo($weizan_21, $weizan_22 = null){
            if (empty($weizan_22) || !is_array($weizan_22)){
                $weizan_22 = array();
            }
            global $_W;
            $weizan_1 = $this -> getSet();
            $weizan_9 = intval($weizan_1['level']);
            $weizan_23 = m('member') -> getMember($weizan_21);
            $weizan_24 = $this -> getLevel($weizan_21);
            $weizan_25 = time();
            $weizan_26 = intval($weizan_1['settledays']) * 3600 * 24;
            $weizan_27 = 0;
            $weizan_28 = 0;
            $weizan_29 = 0;
            $weizan_30 = 0;
            $weizan_31 = 0;
            $weizan_32 = 0;
            $weizan_33 = 0;
            $weizan_34 = 0;
            $weizan_35 = 0;
            $weizan_36 = 0;
            $weizan_37 = 0;
            $weizan_38 = 0;
            $weizan_39 = 0;
            $weizan_40 = 0;
            $weizan_41 = 0;
            $weizan_42 = 0;
            $weizan_43 = 0;
            $weizan_44 = 0;
            $weizan_45 = 0;
            $weizan_46 = 0;
            $weizan_47 = 0;
            $weizan_48 = 0;
            $weizan_49 = 0;
            $weizan_50 = 0;
            $weizan_51 = 0;
            $weizan_52 = 0;
            $weizan_53 = 0;
            $weizan_54 = 0;
            if ($weizan_9 >= 1){
                if (in_array('ordercount0', $weizan_22)){
                    $weizan_55 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=0 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $weizan_23['id']));
                    $weizan_43 += $weizan_55['ordercount'];
                    $weizan_28 += $weizan_55['ordercount'];
                    $weizan_29 += $weizan_55['ordermoney'];
                }
                if (in_array('ordercount', $weizan_22)){
                    $weizan_55 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=1 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $weizan_23['id']));
                    $weizan_46 += $weizan_55['ordercount'];
                    $weizan_30 += $weizan_55['ordercount'];
                    $weizan_31 += $weizan_55['ordermoney'];
                }
                if (in_array('ordercount3', $weizan_22)){
                    $weizan_56 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=3 and og.status1>=0 and og.nocommission=0 and o.uniacid=:uniacid  limit 1', array(':uniacid' => $_W['uniacid'], ':agentid' => $weizan_23['id']));
                    $weizan_49 += $weizan_56['ordercount'];
                    $weizan_32 += $weizan_56['ordercount'];
                    $weizan_33 += $weizan_56['ordermoney'];
                    $weizan_52 += $weizan_56['ordermoney'];
                }
                if (in_array('total', $weizan_22)){
                    $weizan_57 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $weizan_23['id']));
                    foreach ($weizan_57 as $weizan_58){
                        $weizan_10 = iunserializer($weizan_58['commissions']);
                        $weizan_59 = iunserializer($weizan_58['commission1']);
                        if (empty($weizan_10)){
                            $weizan_34 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                        }else{
                            $weizan_34 += isset($weizan_10['level1']) ? floatval($weizan_10['level1']) : 0;
                        }
                    }
                }
                if (in_array('ok', $weizan_22)){
                    $weizan_57 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . " where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and ({$weizan_25} - o.finishtime > {$weizan_26}) and og.status1=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':agentid' => $weizan_23['id']));
                    foreach ($weizan_57 as $weizan_58){
                        $weizan_10 = iunserializer($weizan_58['commissions']);
                        $weizan_59 = iunserializer($weizan_58['commission1']);
                        if (empty($weizan_10)){
                            $weizan_35 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                        }else{
                            $weizan_35 += isset($weizan_10['level1']) ? $weizan_10['level1'] : 0;
                        }
                    }
                }
                if (in_array('lock', $weizan_22)){
                    $weizan_60 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . " where o.agentid=:agentid and o.status>=3 and og.nocommission=0 and ({$weizan_25} - o.finishtime <= {$weizan_26})  and og.status1=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid'], ':agentid' => $weizan_23['id']));
                    foreach ($weizan_60 as $weizan_58){
                        $weizan_10 = iunserializer($weizan_58['commissions']);
                        $weizan_59 = iunserializer($weizan_58['commission1']);
                        if (empty($weizan_10)){
                            $weizan_38 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                        }else{
                            $weizan_38 += isset($weizan_10['level1']) ? $weizan_10['level1'] : 0;
                        }
                    }
                }
                if (in_array('apply', $weizan_22)){
                    $weizan_61 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':agentid' => $weizan_23['id']));
                    foreach ($weizan_61 as $weizan_58){
                        $weizan_10 = iunserializer($weizan_58['commissions']);
                        $weizan_59 = iunserializer($weizan_58['commission1']);
                        if (empty($weizan_10)){
                            $weizan_36 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                        }else{
                            $weizan_36 += isset($weizan_10['level1']) ? $weizan_10['level1'] : 0;
                        }
                    }
                }
                if (in_array('check', $weizan_22)){
                    $weizan_61 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=2 and og.nocommission=0 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $weizan_23['id']));
                    foreach ($weizan_61 as $weizan_58){
                        $weizan_10 = iunserializer($weizan_58['commissions']);
                        $weizan_59 = iunserializer($weizan_58['commission1']);
                        if (empty($weizan_10)){
                            $weizan_37 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                        }else{
                            $weizan_37 += isset($weizan_10['level1']) ? $weizan_10['level1'] : 0;
                        }
                    }
                }
                if (in_array('pay', $weizan_22)){
                    $weizan_61 = pdo_fetchall('select og.commission1,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid=:agentid and o.status>=3 and og.status1=3 and og.nocommission=0 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $weizan_23['id']));
                    foreach ($weizan_61 as $weizan_58){
                        $weizan_10 = iunserializer($weizan_58['commissions']);
                        $weizan_59 = iunserializer($weizan_58['commission1']);
                        if (empty($weizan_10)){
                            $weizan_39 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                        }else{
                            $weizan_39 += isset($weizan_10['level1']) ? $weizan_10['level1'] : 0;
                        }
                    }
                }
                $weizan_62 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and isagent=1 and status=1 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':agentid' => $weizan_23['id']), 'id');
                $weizan_40 = count($weizan_62);
                $weizan_27 += $weizan_40;
            }
            if ($weizan_9 >= 2){
                if ($weizan_40 > 0){
                    if (in_array('ordercount0', $weizan_22)){
                        $weizan_63 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_62)) . ')  and o.status>=0 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $weizan_44 += $weizan_63['ordercount'];
                        $weizan_28 += $weizan_63['ordercount'];
                        $weizan_29 += $weizan_63['ordermoney'];
                    }
                    if (in_array('ordercount', $weizan_22)){
                        $weizan_63 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_62)) . ')  and o.status>=1 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $weizan_47 += $weizan_63['ordercount'];
                        $weizan_30 += $weizan_63['ordercount'];
                        $weizan_31 += $weizan_63['ordermoney'];
                    }
                    if (in_array('ordercount3', $weizan_22)){
                        $weizan_64 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct o.id) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_62)) . ')  and o.status>=3 and og.status2>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $weizan_50 += $weizan_64['ordercount'];
                        $weizan_32 += $weizan_64['ordercount'];
                        $weizan_33 += $weizan_64['ordermoney'];
                        $weizan_53 += $weizan_64['ordermoney'];
                    }
                    if (in_array('total', $weizan_22)){
                        $weizan_65 = pdo_fetchall('select og.commission2,og.commissions from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_62)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_65 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission2']);
                            if (empty($weizan_10)){
                                $weizan_34 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_34 += isset($weizan_10['level2']) ? $weizan_10['level2'] : 0;
                            }
                        }
                    }
                    if (in_array('ok', $weizan_22)){
                        $weizan_65 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_62)) . ")  and ({$weizan_25} - o.finishtime > {$weizan_26}) and o.status>=3 and og.status2=0 and og.nocommission=0  and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_65 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission2']);
                            if (empty($weizan_10)){
                                $weizan_35 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_35 += isset($weizan_10['level2']) ? $weizan_10['level2'] : 0;
                            }
                        }
                    }
                    if (in_array('lock', $weizan_22)){
                        $weizan_66 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_62)) . ")  and ({$weizan_25} - o.finishtime <= {$weizan_26}) and og.status2=0 and o.status>=3 and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_66 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission2']);
                            if (empty($weizan_10)){
                                $weizan_38 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_38 += isset($weizan_10['level2']) ? $weizan_10['level2'] : 0;
                            }
                        }
                    }
                    if (in_array('apply', $weizan_22)){
                        $weizan_67 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_62)) . ')  and o.status>=3 and og.status2=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_67 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission2']);
                            if (empty($weizan_10)){
                                $weizan_36 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_36 += isset($weizan_10['level2']) ? $weizan_10['level2'] : 0;
                            }
                        }
                    }
                    if (in_array('check', $weizan_22)){
                        $weizan_68 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_62)) . ')  and o.status>=3 and og.status2=2 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_68 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission2']);
                            if (empty($weizan_10)){
                                $weizan_37 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_37 += isset($weizan_10['level2']) ? $weizan_10['level2'] : 0;
                            }
                        }
                    }
                    if (in_array('pay', $weizan_22)){
                        $weizan_68 = pdo_fetchall('select og.commission2,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_62)) . ')  and o.status>=3 and og.status2=3 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_68 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission2']);
                            if (empty($weizan_10)){
                                $weizan_39 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_39 += isset($weizan_10['level2']) ? $weizan_10['level2'] : 0;
                            }
                        }
                    }
                    $weizan_69 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($weizan_62)) . ') and isagent=1 and status=1 and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
                    $weizan_41 = count($weizan_69);
                    $weizan_27 += $weizan_41;
                }
            }
            if ($weizan_9 >= 3){
                if ($weizan_41 > 0){
                    if (in_array('ordercount0', $weizan_22)){
                        $weizan_70 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_69)) . ')  and o.status>=0 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $weizan_45 += $weizan_70['ordercount'];
                        $weizan_28 += $weizan_70['ordercount'];
                        $weizan_29 += $weizan_70['ordermoney'];
                    }
                    if (in_array('ordercount', $weizan_22)){
                        $weizan_70 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_69)) . ')  and o.status>=1 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $weizan_48 += $weizan_70['ordercount'];
                        $weizan_30 += $weizan_70['ordercount'];
                        $weizan_31 += $weizan_70['ordermoney'];
                    }
                    if (in_array('ordercount3', $weizan_22)){
                        $weizan_71 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($weizan_69)) . ')  and o.status>=3 and og.status3>=0 and og.nocommission=0 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
                        $weizan_51 += $weizan_71['ordercount'];
                        $weizan_32 += $weizan_71['ordercount'];
                        $weizan_33 += $weizan_71['ordermoney'];
                        $weizan_54 += $weizan_70['ordermoney'];
                    }
                    if (in_array('total', $weizan_22)){
                        $weizan_72 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($weizan_69)) . ')  and o.status>=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_72 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission3']);
                            if (empty($weizan_10)){
                                $weizan_34 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_34 += isset($weizan_10['level3']) ? $weizan_10['level3'] : 0;
                            }
                        }
                    }
                    if (in_array('ok', $weizan_22)){
                        $weizan_72 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($weizan_69)) . ")  and ({$weizan_25} - o.finishtime > {$weizan_26}) and o.status>=3 and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_72 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission3']);
                            if (empty($weizan_10)){
                                $weizan_35 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_35 += isset($weizan_10['level3']) ? $weizan_10['level3'] : 0;
                            }
                        }
                    }
                    if (in_array('lock', $weizan_22)){
                        $weizan_73 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($weizan_69)) . ")  and o.status>=3 and ({$weizan_25} - o.finishtime > {$weizan_26}) and og.status3=0  and og.nocommission=0 and o.uniacid=:uniacid", array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_73 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission3']);
                            if (empty($weizan_10)){
                                $weizan_38 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_38 += isset($weizan_10['level3']) ? $weizan_10['level3'] : 0;
                            }
                        }
                    }
                    if (in_array('apply', $weizan_22)){
                        $weizan_74 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($weizan_69)) . ')  and o.status>=3 and og.status3=1 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_74 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission3']);
                            if (empty($weizan_10)){
                                $weizan_36 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_36 += isset($weizan_10['level3']) ? $weizan_10['level3'] : 0;
                            }
                        }
                    }
                    if (in_array('check', $weizan_22)){
                        $weizan_75 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($weizan_69)) . ')  and o.status>=3 and og.status3=2 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_75 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission3']);
                            if (empty($weizan_10)){
                                $weizan_37 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_37 += isset($weizan_10['level3']) ? $weizan_10['level3'] : 0;
                            }
                        }
                    }
                    if (in_array('pay', $weizan_22)){
                        $weizan_75 = pdo_fetchall('select og.commission3,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where o.agentid in( ' . implode(',', array_keys($weizan_69)) . ')  and o.status>=3 and og.status3=3 and og.nocommission=0 and o.uniacid=:uniacid', array(':uniacid' => $_W['uniacid']));
                        foreach ($weizan_75 as $weizan_58){
                            $weizan_10 = iunserializer($weizan_58['commissions']);
                            $weizan_59 = iunserializer($weizan_58['commission3']);
                            if (empty($weizan_10)){
                                $weizan_39 += isset($weizan_59['level' . $weizan_24['id']]) ? $weizan_59['level' . $weizan_24['id']] : $weizan_59['default'];
                            }else{
                                $weizan_39 += isset($weizan_10['level3']) ? $weizan_10['level3'] : 0;
                            }
                        }
                    }
                    $weizan_76 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and agentid in( ' . implode(',', array_keys($weizan_69)) . ') and isagent=1 and status=1', array(':uniacid' => $_W['uniacid']), 'id');
                    $weizan_42 = count($weizan_76);
                    $weizan_27 += $weizan_42;
                }
            }
            $weizan_23['agentcount'] = $weizan_27;
            $weizan_23['ordercount'] = $weizan_30;
            $weizan_23['ordermoney'] = $weizan_31;
            $weizan_23['order1'] = $weizan_46;
            $weizan_23['order2'] = $weizan_47;
            $weizan_23['order3'] = $weizan_48;
            $weizan_23['ordercount3'] = $weizan_32;
            $weizan_23['ordermoney3'] = $weizan_33;
            $weizan_23['order13'] = $weizan_49;
            $weizan_23['order23'] = $weizan_50;
            $weizan_23['order33'] = $weizan_51;
            $weizan_23['order13money'] = $weizan_52;
            $weizan_23['order23money'] = $weizan_53;
            $weizan_23['order33money'] = $weizan_54;
            $weizan_23['ordercount0'] = $weizan_28;
            $weizan_23['ordermoney0'] = $weizan_29;
            $weizan_23['order10'] = $weizan_43;
            $weizan_23['order20'] = $weizan_44;
            $weizan_23['order30'] = $weizan_45;
            $weizan_23['commission_total'] = round($weizan_34, 2);
            $weizan_23['commission_ok'] = round($weizan_35, 2);
            $weizan_23['commission_lock'] = round($weizan_38, 2);
            $weizan_23['commission_apply'] = round($weizan_36, 2);
            $weizan_23['commission_check'] = round($weizan_37, 2);
            $weizan_23['commission_pay'] = round($weizan_39, 2);
            $weizan_23['level1'] = $weizan_40;
            $weizan_23['level1_agentids'] = $weizan_62;
            $weizan_23['level2'] = $weizan_41;
            $weizan_23['level2_agentids'] = $weizan_69;
            $weizan_23['level3'] = $weizan_42;
            $weizan_23['level3_agentids'] = $weizan_76;
            $weizan_23['agenttime'] = date('Y-m-d H:i', $weizan_23['agenttime']);
            return $weizan_23;
        }
        public function getAgents($weizan_2 = 0){
            global $_W, $_GPC;
            $weizan_77 = array();
            $weizan_78 = pdo_fetch('select id,agentid,openid from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1' , array(':id' => $weizan_2, ':uniacid' => $_W['uniacid']));
            if (empty($weizan_78)){
                return $weizan_77;
            }
            $weizan_11 = m('member') -> getMember($weizan_78['agentid']);
            if (!empty($weizan_11) && $weizan_11['isagent'] == 1 && $weizan_11['status'] == 1){
                $weizan_77[] = $weizan_11;
                if (!empty($weizan_11['agentid'])){
                    $weizan_13 = m('member') -> getMember($weizan_11['agentid']);
                    if (!empty($weizan_13) && $weizan_13['isagent'] == 1 && $weizan_13['status'] == 1){
                        $weizan_77[] = $weizan_13;
                        if (!empty($weizan_13['agentid'])){
                            $weizan_15 = m('member') -> getMember($weizan_13['agentid']);
                            if (!empty($weizan_15) && $weizan_15['isagent'] == 1 && $weizan_15['status'] == 1){
                                $weizan_77[] = $weizan_15;
                            }
                        }
                    }
                }
            }
            return $weizan_77;
        }
        public function isAgent($weizan_21){
            if (empty($weizan_21)){
                return false;
            }
            if (is_array($weizan_21)){
                return $weizan_21['isagent'] == 1 && $weizan_21['status'] == 1;
            }
            $weizan_23 = m('member') -> getMember($weizan_21);
            return $weizan_23['isagent'] == 1 && $weizan_23['status'] == 1;
        }
        public function getCommission($weizan_6){
            global $_W;
            $weizan_1 = $this -> getSet();
            $weizan_59 = 0;
            if ($weizan_6['hascommission'] == 1){
                $weizan_59 = $weizan_1['level'] >= 1 ? ($weizan_6['commission1_rate'] > 0 ? ($weizan_6['commission1_rate'] * $weizan_6['marketprice'] / 100) : $weizan_6['commission1_pay']) : 0;
            }else{
                $weizan_21 = m('user') -> getOpenid();
                $weizan_9 = $this -> getLevel($weizan_21);
                if (!empty($weizan_9)){
                    $weizan_59 = $weizan_1['level'] >= 1 ? round($weizan_9['commission1'] * $weizan_6['marketprice'] / 100, 2) : 0;
                }else{
                    $weizan_59 = $weizan_1['level'] >= 1 ? round($weizan_1['commission1'] * $weizan_6['marketprice'] / 100, 2) : 0;
                }
            }
            return $weizan_59;
        }
        public function createMyShopQrcode($weizan_79 = 0, $weizan_80 = 0){
            global $_W;
            $weizan_81 = IA_ROOT . '/addons/ewei_shop/data/qrcode/' . $_W['uniacid'];
            if (!is_dir($weizan_81)){
                load() -> func('file');
                mkdirs($weizan_81);
            }
            $weizan_82 = $_W['siteroot'] . 'app/index.php?i=' . $_W['uniacid'] . '&c=entry&m=ewei_shop&do=plugin&p=commission&method=myshop&mid=' . $weizan_79;
            if (!empty($weizan_80)){
                $weizan_82 .= '&posterid=' . $weizan_80;
            }
            $weizan_83 = 'myshop_' . $weizan_80 . '_' . $weizan_79 . '.png';
            $weizan_84 = $weizan_81 . '/' . $weizan_83;
            if (!is_file($weizan_84)){
                require IA_ROOT . '/framework/library/qrcode/phpqrcode.php';
                QRcode :: png($weizan_82, $weizan_84, QR_ECLEVEL_H, 4);
            }
            return $_W['siteroot'] . 'addons/ewei_shop/data/qrcode/' . $_W['uniacid'] . '/' . $weizan_83;
        }
        private function createImage($weizan_82){
            load() -> func('communication');
            $weizan_85 = ihttp_request($weizan_82);
            return imagecreatefromstring($weizan_85['content']);
        }
        public function createGoodsImage($weizan_6, $weizan_86){
            global $_W, $_GPC;
            $weizan_6 = set_medias($weizan_6, 'thumb');
            $weizan_21 = m('user') -> getOpenid();
            $weizan_87 = m('member') -> getMember($weizan_21);
            if ($weizan_87['isagent'] == 1 && $weizan_87['status'] == 1){
                $weizan_88 = $weizan_87;
            }else{
                $weizan_79 = intval($_GPC['mid']);
                if (!empty($weizan_79)){
                    $weizan_88 = m('member') -> getMember($weizan_79);
                }
            }
            $weizan_81 = IA_ROOT . '/addons/ewei_shop/data/poster/' . $_W['uniacid'] . '/';
            if (!is_dir($weizan_81)){
                load() -> func('file');
                mkdirs($weizan_81);
            }
            $weizan_89 = empty($weizan_6['commission_thumb']) ? $weizan_6['thumb'] : tomedia($weizan_6['commission_thumb']);
            $weizan_90 = md5(json_encode(array('id' => $weizan_6['id'], 'marketprice' => $weizan_6['marketprice'], 'productprice' => $weizan_6['productprice'], 'img' => $weizan_89, 'openid' => $weizan_21, 'version' => 4)));
            $weizan_83 = $weizan_90 . '.jpg';
            if (!is_file($weizan_81 . $weizan_83)){
                set_time_limit(0);
                $weizan_91 = IA_ROOT . '/addons/ewei_shop/static/fonts/msyh.ttf';
                $weizan_92 = imagecreatetruecolor(640, 1225);
                $weizan_93 = imagecreatefromjpeg(IA_ROOT . '/addons/ewei_shop/plugin/commission/images/poster.jpg');
                imagecopy($weizan_92, $weizan_93, 0, 0, 0, 0, 640, 1225);
                imagedestroy($weizan_93);
                $weizan_94 = preg_replace('/\/0$/i', '/96', $weizan_88['avatar']);
                $weizan_95 = $this -> createImage($weizan_94);
                $weizan_96 = imagesx($weizan_95);
                $weizan_97 = imagesy($weizan_95);
                imagecopyresized($weizan_92, $weizan_95, 24, 32, 0, 0, 88, 88, $weizan_96, $weizan_97);
                imagedestroy($weizan_95);
                $weizan_98 = $this -> createImage($weizan_89);
                $weizan_96 = imagesx($weizan_98);
                $weizan_97 = imagesy($weizan_98);
                imagecopyresized($weizan_92, $weizan_98, 0, 160, 0, 0, 640, 640, $weizan_96, $weizan_97);
                imagedestroy($weizan_98);
                $weizan_99 = imagecreatetruecolor(640, 127);
                imagealphablending($weizan_99, false);
                imagesavealpha($weizan_99, true);
                $weizan_100 = imagecolorallocatealpha($weizan_99, 0, 0, 0, 25);
                imagefill($weizan_99, 0, 0, $weizan_100);
                imagecopy($weizan_92, $weizan_99, 0, 678, 0, 0, 640, 127);
                imagedestroy($weizan_99);
                $weizan_101 = tomedia(m('qrcode') -> createGoodsQrcode($weizan_88['id'], $weizan_6['id']));
                $weizan_102 = $this -> createImage($weizan_101);
                $weizan_96 = imagesx($weizan_102);
                $weizan_97 = imagesy($weizan_102);
                imagecopyresized($weizan_92, $weizan_102, 50, 835, 0, 0, 250, 250, $weizan_96, $weizan_97);
                imagedestroy($weizan_102);
                $weizan_103 = imagecolorallocate($weizan_92, 0, 3, 51);
                $weizan_104 = imagecolorallocate($weizan_92, 240, 102, 0);
                $weizan_105 = imagecolorallocate($weizan_92, 255, 255, 255);
                $weizan_106 = imagecolorallocate($weizan_92, 255, 255, 0);
                $weizan_107 = '我是';
                imagettftext($weizan_92, 20, 0, 150, 70, $weizan_103, $weizan_91, $weizan_107);
                imagettftext($weizan_92, 20, 0, 210, 70, $weizan_104, $weizan_91, $weizan_88['nickname']);
                $weizan_108 = '我要为';
                imagettftext($weizan_92, 20, 0, 150, 105, $weizan_103, $weizan_91, $weizan_108);
                $weizan_109 = $weizan_86['name'];
                imagettftext($weizan_92, 20, 0, 240, 105, $weizan_104, $weizan_91, $weizan_109);
                $weizan_110 = imagettfbbox(20, 0, $weizan_91, $weizan_109);
                $weizan_111 = $weizan_110[4] - $weizan_110[6];
                $weizan_112 = '代言';
                imagettftext($weizan_92, 20, 0, 240 + $weizan_111 + 10, 105, $weizan_103, $weizan_91, $weizan_112);
                $weizan_113 = mb_substr($weizan_6['title'], 0, 50, 'utf-8');
                imagettftext($weizan_92, 20, 0, 30, 730, $weizan_105, $weizan_91, $weizan_113);
                $weizan_114 = '￥' . number_format($weizan_6['marketprice'], 2);
                imagettftext($weizan_92, 25, 0, 25, 780, $weizan_106, $weizan_91, $weizan_114);
                $weizan_110 = imagettfbbox(26, 0, $weizan_91, $weizan_114);
                $weizan_111 = $weizan_110[4] - $weizan_110[6];
                if ($weizan_6['productprice'] > 0){
                    $weizan_115 = '￥' . number_format($weizan_6['productprice'], 2);
                    imagettftext($weizan_92, 22, 0, 25 + $weizan_111 + 10, 780, $weizan_105, $weizan_91, $weizan_115);
                    $weizan_116 = 25 + $weizan_111 + 10;
                    $weizan_110 = imagettfbbox(22, 0, $weizan_91, $weizan_115);
                    $weizan_111 = $weizan_110[4] - $weizan_110[6];
                    imageline($weizan_92, $weizan_116, 770, $weizan_116 + $weizan_111 + 20, 770, $weizan_105);
                    imageline($weizan_92, $weizan_116, 771.5, $weizan_116 + $weizan_111 + 20, 771, $weizan_105);
                }
                imagejpeg($weizan_92, $weizan_81 . $weizan_83);
                imagedestroy($weizan_92);
            }
            return $_W['siteroot'] . 'addons/ewei_shop/data/poster/' . $_W['uniacid'] . '/' . $weizan_83;
        }
        public function createShopImage($weizan_86){
            global $_W, $_GPC;
            $weizan_86 = set_medias($weizan_86, 'signimg');
            $weizan_81 = IA_ROOT . '/addons/ewei_shop/data/poster/' . $_W['uniacid'] . '/';
            if (!is_dir($weizan_81)){
                load() -> func('file');
                mkdirs($weizan_81);
            }
            $weizan_79 = intval($_GPC['mid']);
            $weizan_21 = m('user') -> getOpenid();
            $weizan_87 = m('member') -> getMember($weizan_21);
            if ($weizan_87['isagent'] == 1 && $weizan_87['status'] == 1){
                $weizan_88 = $weizan_87;
            }else{
                $weizan_79 = intval($_GPC['mid']);
                if (!empty($weizan_79)){
                    $weizan_88 = m('member') -> getMember($weizan_79);
                }
            }
            $weizan_90 = md5(json_encode(array('openid' => $weizan_21, 'signimg' => $weizan_86['signimg'], 'version' => 4)));
            $weizan_83 = $weizan_90 . '.jpg';
            if (!is_file($weizan_81 . $weizan_83)){
                set_time_limit(0);
                @ini_set('memory_limit', '256M');
                $weizan_91 = IA_ROOT . '/addons/ewei_shop/static/fonts/msyh.ttf';
                $weizan_92 = imagecreatetruecolor(640, 1225);
                $weizan_103 = imagecolorallocate($weizan_92, 0, 3, 51);
                $weizan_104 = imagecolorallocate($weizan_92, 240, 102, 0);
                $weizan_105 = imagecolorallocate($weizan_92, 255, 255, 255);
                $weizan_106 = imagecolorallocate($weizan_92, 255, 255, 0);
                $weizan_93 = imagecreatefromjpeg(IA_ROOT . '/addons/ewei_shop/plugin/commission/images/poster.jpg');
                imagecopy($weizan_92, $weizan_93, 0, 0, 0, 0, 640, 1225);
                imagedestroy($weizan_93);
                $weizan_94 = preg_replace('/\/0$/i', '/96', $weizan_88['avatar']);
                $weizan_95 = $this -> createImage($weizan_94);
                $weizan_96 = imagesx($weizan_95);
                $weizan_97 = imagesy($weizan_95);
                imagecopyresized($weizan_92, $weizan_95, 24, 32, 0, 0, 88, 88, $weizan_96, $weizan_97);
                imagedestroy($weizan_95);
                $weizan_98 = $this -> createImage($weizan_86['signimg']);
                $weizan_96 = imagesx($weizan_98);
                $weizan_97 = imagesy($weizan_98);
                imagecopyresized($weizan_92, $weizan_98, 0, 160, 0, 0, 640, 640, $weizan_96, $weizan_97);
                imagedestroy($weizan_98);
                $weizan_117 = tomedia($this -> createMyShopQrcode($weizan_88['id']));
                $weizan_102 = $this -> createImage($weizan_117);
                $weizan_96 = imagesx($weizan_102);
                $weizan_97 = imagesy($weizan_102);
                imagecopyresized($weizan_92, $weizan_102, 50, 835, 0, 0, 250, 250, $weizan_96, $weizan_97);
                imagedestroy($weizan_102);
                $weizan_107 = '我是';
                imagettftext($weizan_92, 20, 0, 150, 70, $weizan_103, $weizan_91, $weizan_107);
                imagettftext($weizan_92, 20, 0, 210, 70, $weizan_104, $weizan_91, $weizan_88['nickname']);
                $weizan_108 = '我要为';
                imagettftext($weizan_92, 20, 0, 150, 105, $weizan_103, $weizan_91, $weizan_108);
                $weizan_109 = $weizan_86['name'];
                imagettftext($weizan_92, 20, 0, 240, 105, $weizan_104, $weizan_91, $weizan_109);
                $weizan_110 = imagettfbbox(20, 0, $weizan_91, $weizan_109);
                $weizan_111 = $weizan_110[4] - $weizan_110[6];
                $weizan_112 = '代言';
                imagettftext($weizan_92, 20, 0, 240 + $weizan_111 + 10, 105, $weizan_103, $weizan_91, $weizan_112);
                imagejpeg($weizan_92, $weizan_81 . $weizan_83);
                imagedestroy($weizan_92);
            }
            return $_W['siteroot'] . 'addons/ewei_shop/data/poster/' . $_W['uniacid'] . '/' . $weizan_83;
        }
        public function checkAgent(){
            global $_W, $_GPC;
            $weizan_1 = $this -> getSet();
            if (empty($weizan_1['level'])){
                return;
            }
            $weizan_21 = m('user') -> getOpenid();
            if (empty($weizan_21)){
                return;
            }
            $weizan_23 = m('member') -> getMember($weizan_21);
            if (empty($weizan_23)){
                return;
            }
            $weizan_118 = false;
            $weizan_79 = intval($_GPC['mid']);
            if (!empty($weizan_79)){
                $weizan_118 = m('member') -> getMember($weizan_79);
            }
            $weizan_119 = !empty($weizan_118) && $weizan_118['isagent'] == 1 && $weizan_118['status'] == 1;
            if ($weizan_119){
                if ($weizan_118['openid'] != $weizan_21){
                    $weizan_120 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_clickcount') . ' where uniacid=:uniacid and openid=:openid and from_openid=:from_openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $weizan_21, ':from_openid' => $weizan_118['openid']));
                    if ($weizan_120 <= 0){
                        $weizan_121 = array('uniacid' => $_W['uniacid'], 'openid' => $weizan_21, 'from_openid' => $weizan_118['openid'], 'clicktime' => time());
                        pdo_insert('ewei_shop_commission_clickcount', $weizan_121);
                        pdo_update('ewei_shop_member', array('clickcount' => $weizan_118['clickcount'] + 1), array('uniacid' => $_W['uniacid'], 'id' => $weizan_118['id']));
                    }
                }
            }
            if ($weizan_23['isagent'] == 1){
                return;
            }
            if ($weizan_122 == 0){
                $weizan_123 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where id<>:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $weizan_23['id']));
                if ($weizan_123 <= 0){
                    pdo_update('ewei_shop_member', array('isagent' => 1, 'status' => 1, 'agenttime' => time(), 'agentblack' => 0), array('uniacid' => $_W['uniacid'], 'id' => $weizan_23['id']));
                    return;
                }
            }
            $weizan_25 = time();
            $weizan_124 = intval($weizan_1['become_child']);
            if ($weizan_119 && empty($weizan_23['agentid'])){
                if ($weizan_23['id'] != $weizan_118['id']){
                    if (empty($weizan_124)){
                        if (empty($weizan_23['fixagentid'])){
                            pdo_update('ewei_shop_member', array('agentid' => $weizan_118['id'], 'childtime' => $weizan_25), array('uniacid' => $_W['uniacid'], 'id' => $weizan_23['id']));
                            $this -> sendMessage($weizan_118['openid'], array('nickname' => $weizan_23['nickname'], 'childtime' => $weizan_25), TM_COMMISSION_AGENT_NEW);
                            $this -> upgradeLevelByAgent($weizan_118['id']);
                        }
                    }else{
                        pdo_update('ewei_shop_member', array('inviter' => $weizan_118['id']), array('uniacid' => $_W['uniacid'], 'id' => $weizan_23['id']));
                    }
                }
            }
            $weizan_125 = intval($weizan_1['become_check']);
            if (empty($weizan_1['become'])){
                if (empty($weizan_23['agentblack'])){
                    pdo_update('ewei_shop_member', array('isagent' => 1, 'status' => $weizan_125, 'agenttime' => $weizan_125 == 1 ? $weizan_25 : 0), array('uniacid' => $_W['uniacid'], 'id' => $weizan_23['id']));
                    if ($weizan_125 == 1){
                        $this -> sendMessage($weizan_21, array('nickname' => $weizan_23['nickname'], 'agenttime' => $weizan_25), TM_COMMISSION_BECOME);
                        if ($weizan_119){
                            $this -> upgradeLevelByAgent($weizan_118['id']);
                        }
                    }
                }
            }
        }
        public function checkOrderConfirm($weizan_2 = '0'){
            global $_W, $_GPC;
            if (empty($weizan_2)){
                return;
            }
            $weizan_1 = $this -> getSet();
            if (empty($weizan_1['level'])){
                return;
            }
            $weizan_78 = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime from ' . tablename('ewei_shop_order') . ' where id=:id and status>=0 and uniacid=:uniacid limit 1', array(':id' => $weizan_2, ':uniacid' => $_W['uniacid']));
            if (empty($weizan_78)){
                return;
            }
            $weizan_21 = $weizan_78['openid'];
            $weizan_23 = m('member') -> getMember($weizan_21);
            if (empty($weizan_23)){
                return;
            }
            $weizan_124 = intval($weizan_1['become_child']);
            $weizan_118 = false;
            if (empty($weizan_124)){
                $weizan_118 = m('member') -> getMember($weizan_23['agentid']);
            }else{
                $weizan_118 = m('member') -> getMember($weizan_23['inviter']);
            }
            $weizan_119 = !empty($weizan_118) && $weizan_118['isagent'] == 1 && $weizan_118['status'] == 1;
            $weizan_25 = time();
            $weizan_124 = intval($weizan_1['become_child']);
            if ($weizan_119){
                if ($weizan_124 == 1){
                    if (empty($weizan_23['agentid']) && $weizan_23['id'] != $weizan_118['id']){
                        if (empty($weizan_23['fixagentid'])){
                            $weizan_23['agentid'] = $weizan_118['id'];
                            pdo_update('ewei_shop_member', array('agentid' => $weizan_118['id'], 'childtime' => $weizan_25), array('uniacid' => $_W['uniacid'], 'id' => $weizan_23['id']));
                            $this -> sendMessage($weizan_118['openid'], array('nickname' => $weizan_23['nickname'], 'childtime' => $weizan_25), TM_COMMISSION_AGENT_NEW);
                            $this -> upgradeLevelByAgent($weizan_118['id']);
                        }
                    }
                }
            }
            $weizan_5 = $weizan_23['agentid'];
            if ($weizan_23['isagent'] == 1 && $weizan_23['status'] == 1){
                if (!empty($weizan_1['selfbuy'])){
                    $weizan_5 = $weizan_23['id'];
                }
            }
            if (!empty($weizan_5)){
                pdo_update('ewei_shop_order', array('agentid' => $weizan_5), array('id' => $weizan_2));
            }
            $this -> calculate($weizan_2);
        }
        public function checkOrderPay($weizan_2 = '0'){
            global $_W, $_GPC;
            if (empty($weizan_2)){
                return;
            }
            $weizan_1 = $this -> getSet();
            if (empty($weizan_1['level'])){
                return;
            }
            $weizan_78 = pdo_fetch('select id,openid,ordersn,goodsprice,agentid,paytime from ' . tablename('ewei_shop_order') . ' where id=:id and status>=1 and uniacid=:uniacid limit 1', array(':id' => $weizan_2, ':uniacid' => $_W['uniacid']));
            if (empty($weizan_78)){
                return;
            }
            $weizan_21 = $weizan_78['openid'];
            $weizan_23 = m('member') -> getMember($weizan_21);
            if (empty($weizan_23)){
                return;
            }
            $weizan_124 = intval($weizan_1['become_child']);
            $weizan_118 = false;
            if (empty($weizan_124)){
                $weizan_118 = m('member') -> getMember($weizan_23['agentid']);
            }else{
                $weizan_118 = m('member') -> getMember($weizan_23['inviter']);
            }
            $weizan_119 = !empty($weizan_118) && $weizan_118['isagent'] == 1 && $weizan_118['status'] == 1;
            $weizan_25 = time();
            $weizan_124 = intval($weizan_1['become_child']);
            if ($weizan_119){
                if ($weizan_124 == 2){
                    if (empty($weizan_23['agentid']) && $weizan_23['id'] != $weizan_118['id']){
                        if (empty($weizan_23['fixagentid'])){
                            $weizan_23['agentid'] = $weizan_118['id'];
                            pdo_update('ewei_shop_member', array('agentid' => $weizan_118['id'], 'childtime' => $weizan_25), array('uniacid' => $_W['uniacid'], 'id' => $weizan_23['id']));
                            $this -> sendMessage($weizan_118['openid'], array('nickname' => $weizan_23['nickname'], 'childtime' => $weizan_25), TM_COMMISSION_AGENT_NEW);
                            $this -> upgradeLevelByAgent($weizan_118['id']);
                            if (empty($weizan_78['agentid'])){
                                $weizan_78['agentid'] = $weizan_118['id'];
                                pdo_update('ewei_shop_order', array('agentid' => $weizan_118['id']), array('id' => $weizan_2));
                                $this -> calculate($weizan_2);
                            }
                        }
                    }
                }
            }
            $weizan_126 = $weizan_23['isagent'] == 1 && $weizan_23['status'] == 1;
            if (!$weizan_126){
                if (intval($weizan_1['become']) == 4 && !empty($weizan_1['become_goodsid'])){
                    $weizan_127 = pdo_fetchall('select goodsid from ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid  ', array(':uniacid' => $_W['uniacid'], ':orderid' => $weizan_78['id']), 'goodsid');
                    if (in_array($weizan_1['become_goodsid'], array_keys($weizan_127))){
                        if (empty($weizan_23['agentblack'])){
                            pdo_update('ewei_shop_member', array('status' => 1, 'isagent' => 1, 'agenttime' => $weizan_25), array('uniacid' => $_W['uniacid'], 'id' => $weizan_23['id']));
                            $this -> sendMessage($weizan_21, array('nickname' => $weizan_23['nickname'], 'agenttime' => $weizan_25), TM_COMMISSION_BECOME);
                            if (!empty($weizan_118)){
                                $this -> upgradeLevelByAgent($weizan_118['id']);
                            }
                        }
                    }
                }else if ($weizan_1['become'] == 2 || $weizan_1['become'] == 3){
                    if (empty($weizan_1['become_order'])){
                        $weizan_25 = time();
                        if ($weizan_1['become'] == 2 || $weizan_1['become'] == 3){
                            $weizan_128 = true;
                            if (!empty($weizan_23['agentid'])){
                                $weizan_118 = m('member') -> getMember($weizan_23['agentid']);
                                if (empty($weizan_118) || $weizan_118['isagent'] != 1 || $weizan_118['status'] != 1){
                                    $weizan_128 = false;
                                }
                            }
                            if ($weizan_128){
                                $weizan_129 = false;
                                if ($weizan_1['become'] == '2'){
                                    $weizan_30 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status>=1 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $weizan_21));
                                    $weizan_129 = $weizan_30 >= intval($weizan_1['become_ordercount']);
                                }else if ($weizan_1['become'] == '3'){
                                    $weizan_130 = pdo_fetchcolumn('select sum(og.realprice) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id  where o.openid=:openid and o.status>=1 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $weizan_21));
                                    $weizan_129 = $weizan_130 >= floatval($weizan_1['become_moneycount']);
                                }
                                if ($weizan_129){
                                    if (empty($weizan_23['agentblack'])){
                                        $weizan_125 = intval($weizan_1['become_check']);
                                        pdo_update('ewei_shop_member', array('status' => $weizan_125, 'isagent' => 1, 'agenttime' => $weizan_25), array('uniacid' => $_W['uniacid'], 'id' => $weizan_23['id']));
                                        if ($weizan_125 == 1){
                                            $this -> sendMessage($weizan_21, array('nickname' => $weizan_23['nickname'], 'agenttime' => $weizan_25), TM_COMMISSION_BECOME);
                                            if ($weizan_128){
                                                $this -> upgradeLevelByAgent($weizan_118['id']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($weizan_23['agentid'])){
                $weizan_118 = m('member') -> getMember($weizan_23['agentid']);
                if (!empty($weizan_118) && $weizan_118['isagent'] == 1 && $weizan_118['status'] == 1){
                    if ($weizan_78['agentid'] == $weizan_118['id']){
                        $weizan_127 = pdo_fetchall('select g.id,g.title,og.total,og.price,og.realprice, og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $weizan_78['id']));
                        $weizan_6 = '';
                        $weizan_9 = $weizan_118['agentlevel'];
                        $weizan_34 = 0;
                        $weizan_131 = 0;
                        foreach ($weizan_127 as $weizan_132){
                            $weizan_6 .= "" . $weizan_132['title'] . '( ';
                            if (!empty($weizan_132['optiontitle'])){
                                $weizan_6 .= ' 规格: ' . $weizan_132['optiontitle'];
                            }
                            $weizan_6 .= ' 单价: ' . ($weizan_132['realprice'] / $weizan_132['total']) . ' 数量: ' . $weizan_132['total'] . ' 总价: ' . $weizan_132['realprice'] . '); ';
                            $weizan_59 = iunserializer($weizan_132['commission1']);
                            $weizan_34 += isset($weizan_59['level' . $weizan_9]) ? $weizan_59['level' . $weizan_9] : $weizan_59['default'];
                            $weizan_131 += $weizan_132['realprice'];
                        }
                        $this -> sendMessage($weizan_118['openid'], array('nickname' => $weizan_23['nickname'], 'ordersn' => $weizan_78['ordersn'], 'price' => $weizan_131, 'goods' => $weizan_6, 'commission' => $weizan_34, 'paytime' => $weizan_78['paytime'],), TM_COMMISSION_ORDER_PAY);
                    }
                }
            }
        }
        public function checkOrderFinish($weizan_2 = ''){
            global $_W, $_GPC;
            if (empty($weizan_2)){
                return;
            }
            $weizan_78 = pdo_fetch('select id,openid, ordersn,goodsprice,agentid,finishtime from ' . tablename('ewei_shop_order') . ' where id=:id and status>=3 and uniacid=:uniacid limit 1', array(':id' => $weizan_2, ':uniacid' => $_W['uniacid']));
            if (empty($weizan_78)){
                return;
            }
            $weizan_1 = $this -> getSet();
            if (empty($weizan_1['level'])){
                return;
            }
            $weizan_21 = $weizan_78['openid'];
            $weizan_23 = m('member') -> getMember($weizan_21);
            if (empty($weizan_23)){
                return;
            }
            $weizan_25 = time();
            $weizan_126 = $weizan_23['isagent'] == 1 && $weizan_23['status'] == 1;
            if (!$weizan_126 && $weizan_1['become_order'] == 1){
                if ($weizan_1['become'] == 2 || $weizan_1['become'] == 3){
                    $weizan_128 = true;
                    if (!empty($weizan_23['agentid'])){
                        $weizan_118 = m('member') -> getMember($weizan_23['agentid']);
                        if (empty($weizan_118) || $weizan_118['isagent'] != 1 || $weizan_118['status'] != 1){
                            $weizan_128 = false;
                        }
                    }
                    if ($weizan_128){
                        $weizan_129 = false;
                        if ($weizan_1['become'] == '2'){
                            $weizan_30 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status>=3 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $weizan_21));
                            $weizan_129 = $weizan_30 >= intval($weizan_1['become_ordercount']);
                        }else if ($weizan_1['become'] == '3'){
                            $weizan_130 = pdo_fetchcolumn('select sum(goodsprice) from ' . tablename('ewei_shop_order') . ' where openid=:openid and status>=3 and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $weizan_21));
                            $weizan_129 = $weizan_130 >= floatval($weizan_1['become_moneycount']);
                        }
                        if ($weizan_129){
                            if (empty($weizan_23['agentblack'])){
                                $weizan_125 = intval($weizan_1['become_check']);
                                pdo_update('ewei_shop_member', array('status' => $weizan_125, 'isagent' => 1, 'agenttime' => $weizan_25), array('uniacid' => $_W['uniacid'], 'id' => $weizan_23['id']));
                                if ($weizan_125 == 1){
                                    $this -> sendMessage($weizan_23['openid'], array('nickname' => $weizan_23['nickname'], 'agenttime' => $weizan_25), TM_COMMISSION_BECOME);
                                    if ($weizan_128){
                                        $this -> upgradeLevelByAgent($weizan_118['id']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if (!empty($weizan_23['agentid'])){
                $weizan_118 = m('member') -> getMember($weizan_23['agentid']);
                if (!empty($weizan_118) && $weizan_118['isagent'] == 1 && $weizan_118['status'] == 1){
                    if ($weizan_78['agentid'] == $weizan_118['id']){
                        $weizan_127 = pdo_fetchall('select g.id,g.title,og.total,og.realprice,og.price,og.optionname as optiontitle,g.noticeopenid,g.noticetype,og.commission1 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $weizan_78['id']));
                        $weizan_6 = '';
                        $weizan_9 = $weizan_118['agentlevel'];
                        $weizan_34 = 0;
                        $weizan_131 = 0;
                        foreach ($weizan_127 as $weizan_132){
                            $weizan_6 .= "" . $weizan_132['title'] . '( ';
                            if (!empty($weizan_132['optiontitle'])){
                                $weizan_6 .= ' 规格: ' . $weizan_132['optiontitle'];
                            }
                            $weizan_6 .= ' 单价: ' . ($weizan_132['realprice'] / $weizan_132['total']) . ' 数量: ' . $weizan_132['total'] . ' 总价: ' . $weizan_132['realprice'] . '); ';
                            $weizan_59 = iunserializer($weizan_132['commission1']);
                            $weizan_34 += isset($weizan_59['level' . $weizan_9]) ? $weizan_59['level' . $weizan_9] : $weizan_59['default'];
                            $weizan_131 += $weizan_132['realprice'];
                        }
                        $this -> sendMessage($weizan_118['openid'], array('nickname' => $weizan_23['nickname'], 'ordersn' => $weizan_78['ordersn'], 'price' => $weizan_131, 'goods' => $weizan_6, 'commission' => $weizan_34, 'finishtime' => $weizan_78['finishtime'],), TM_COMMISSION_ORDER_FINISH);
                    }
                }
            }
            $this -> upgradeLevelByOrder($weizan_21);
        }
        function getShop($weizan_133){
            global $_W;
            $weizan_23 = m('member') -> getMember($weizan_133);
            $weizan_134 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_shop') . ' where uniacid=:uniacid and mid=:mid limit 1' , array(':uniacid' => $_W['uniacid'], ':mid' => $weizan_23['id']));
            $weizan_135 = m('common') -> getSysset(array('shop', 'share'));
            $weizan_1 = $weizan_135['shop'];
            $weizan_136 = $weizan_135['share'];
            $weizan_137 = $weizan_136['desc'];
            if (empty($weizan_137)){
                $weizan_137 = $weizan_1['description'];
            }
            if (empty($weizan_137)){
                $weizan_137 = $weizan_1['name'];
            }
            $weizan_138 = $this -> getSet();
            if (empty($weizan_134)){
                $weizan_134 = array('name' => $weizan_23['nickname'] . '的' . $weizan_138['texts']['shop'], 'logo' => $weizan_23['avatar'], 'desc' => $weizan_137, 'img' => tomedia($weizan_1['img']),);
            }else{
                if (empty($weizan_134['name'])){
                    $weizan_134['name'] = $weizan_23['nickname'] . '的' . $weizan_138['texts']['shop'];
                }
                if (empty($weizan_134['logo'])){
                    $weizan_134['logo'] = tomedia($weizan_23['avatar']);
                }
                if (empty($weizan_134['img'])){
                    $weizan_134['img'] = tomedia($weizan_1['img']);
                }
                if (empty($weizan_134['desc'])){
                    $weizan_134['desc'] = $weizan_137;
                }
            }
            return $weizan_134;
        }
        function getLevels($weizan_139 = true){
            global $_W;
            if ($weizan_139){
                return pdo_fetchall('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid order by commission1 asc', array(':uniacid' => $_W['uniacid']));
            }else{
                return pdo_fetchall('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid and (ordermoney>0 or commissionmoney>0) order by commission1 asc', array(':uniacid' => $_W['uniacid']));
            }
        }
        function getLevel($weizan_21){
            global $_W;
            if (empty($weizan_21)){
                return false;
            }
            $weizan_23 = m('member') -> getMember($weizan_21);
            if (empty($weizan_23['agentlevel'])){
                return false;
            }
            $weizan_9 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $weizan_23['agentlevel']));
            return $weizan_9;
        }
        function upgradeLevelByOrder($weizan_21){
            global $_W;
            if (empty($weizan_21)){
                return false;
            }
            $weizan_1 = $this -> getSet();
            if (empty($weizan_1['level'])){
                return false;
            }
            $weizan_133 = m('member') -> getMember($weizan_21);
            if (empty($weizan_133)){
                return;
            }
            $weizan_140 = intval($weizan_1['leveltype']);
            if ($weizan_140 == 4 || $weizan_140 == 5){
                if (!empty($weizan_133['agentnotupgrade'])){
                    return;
                }
                $weizan_141 = $this -> getLevel($weizan_133['openid']);
                if (empty($weizan_141['id'])){
                    $weizan_141 = array('levelname' => empty($weizan_1['levelname']) ? '普通等级' : $weizan_1['levelname'], 'commission1' => $weizan_1['commission1'], 'commission2' => $weizan_1['commission2'], 'commission3' => $weizan_1['commission3']);
                }
                $weizan_142 = pdo_fetch('select sum(og.realprice) as ordermoney,count(distinct og.orderid) as ordercount from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.openid=:openid and o.status>=3 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $weizan_21));
                $weizan_31 = $weizan_142['ordermoney'];
                $weizan_30 = $weizan_142['ordercount'];
                if ($weizan_140 == 4){
                    $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$weizan_31} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
                    if (empty($weizan_143)){
                        return;
                    }
                    if (!empty($weizan_141['id'])){
                        if ($weizan_141['id'] == $weizan_143['id']){
                            return;
                        }
                        if ($weizan_141['ordermoney'] > $weizan_143['ordermoney']){
                            return;
                        }
                    }
                }else if ($weizan_140 == 5){
                    $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$weizan_30} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
                    if (empty($weizan_143)){
                        return;
                    }
                    if (!empty($weizan_141['id'])){
                        if ($weizan_141['id'] == $weizan_143['id']){
                            return;
                        }
                        if ($weizan_141['ordercount'] > $weizan_143['ordercount']){
                            return;
                        }
                    }
                }
                pdo_update('ewei_shop_member', array('agentlevel' => $weizan_143['id']), array('id' => $weizan_133['id']));
                $this -> sendMessage($weizan_133['openid'], array('nickname' => $weizan_133['nickname'], 'oldlevel' => $weizan_141, 'newlevel' => $weizan_143,), TM_COMMISSION_UPGRADE);
            }else if ($weizan_140 >= 0 && $weizan_140 <= 3){
                $weizan_77 = array();
                if (!empty($weizan_1['selfbuy'])){
                    $weizan_77[] = $weizan_133;
                }
                if (!empty($weizan_133['agentid'])){
                    $weizan_11 = m('member') -> getMember($weizan_133['agentid']);
                    if (!empty($weizan_11)){
                        $weizan_77[] = $weizan_11;
                        if (!empty($weizan_11['agentid']) && $weizan_11['isagent'] == 1 && $weizan_11['status'] == 1){
                            $weizan_13 = m('member') -> getMember($weizan_11['agentid']);
                            if (!empty($weizan_13) && $weizan_13['isagent'] == 1 && $weizan_13['status'] == 1){
                                $weizan_77[] = $weizan_13;
                                if (empty($weizan_1['selfbuy'])){
                                    if (!empty($weizan_13['agentid']) && $weizan_13['isagent'] == 1 && $weizan_13['status'] == 1){
                                        $weizan_15 = m('member') -> getMember($weizan_13['agentid']);
                                        if (!empty($weizan_15) && $weizan_15['isagent'] == 1 && $weizan_15['status'] == 1){
                                            $weizan_77[] = $weizan_15;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (empty($weizan_77)){
                    return;
                }
                foreach ($weizan_77 as $weizan_144){
                    $weizan_145 = $this -> getInfo($weizan_144['id'], array('ordercount3', 'ordermoney3', 'order13money', 'order13'));
                    if (!empty($weizan_145['agentnotupgrade'])){
                        continue;
                    }
                    $weizan_141 = $this -> getLevel($weizan_144['openid']);
                    if (empty($weizan_141['id'])){
                        $weizan_141 = array('levelname' => empty($weizan_1['levelname']) ? '普通等级' : $weizan_1['levelname'], 'commission1' => $weizan_1['commission1'], 'commission2' => $weizan_1['commission2'], 'commission3' => $weizan_1['commission3']);
                    }
                    if ($weizan_140 == 0){
                        $weizan_31 = $weizan_145['ordermoney3'];
                        $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid and {$weizan_31} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
                        if (empty($weizan_143)){
                            continue;
                        }
                        if (!empty($weizan_141['id'])){
                            if ($weizan_141['id'] == $weizan_143['id']){
                                continue;
                            }
                            if ($weizan_141['ordermoney'] > $weizan_143['ordermoney']){
                                continue;
                            }
                        }
                    }else if ($weizan_140 == 1){
                        $weizan_31 = $weizan_145['order13money'];
                        $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid and {$weizan_31} >= ordermoney and ordermoney>0  order by ordermoney desc limit 1", array(':uniacid' => $_W['uniacid']));
                        if (empty($weizan_143)){
                            continue;
                        }
                        if (!empty($weizan_141['id'])){
                            if ($weizan_141['id'] == $weizan_143['id']){
                                continue;
                            }
                            if ($weizan_141['ordermoney'] > $weizan_143['ordermoney']){
                                continue;
                            }
                        }
                    }else if ($weizan_140 == 2){
                        $weizan_30 = $weizan_145['ordercount3'];
                        $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$weizan_30} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
                        if (empty($weizan_143)){
                            continue;
                        }
                        if (!empty($weizan_141['id'])){
                            if ($weizan_141['id'] == $weizan_143['id']){
                                continue;
                            }
                            if ($weizan_141['ordercount'] > $weizan_143['ordercount']){
                                continue;
                            }
                        }
                    }else if ($weizan_140 == 3){
                        $weizan_30 = $weizan_145['order13'];
                        $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$weizan_30} >= ordercount and ordercount>0  order by ordercount desc limit 1", array(':uniacid' => $_W['uniacid']));
                        if (empty($weizan_143)){
                            continue;
                        }
                        if (!empty($weizan_141['id'])){
                            if ($weizan_141['id'] == $weizan_143['id']){
                                continue;
                            }
                            if ($weizan_141['ordercount'] > $weizan_143['ordercount']){
                                continue;
                            }
                        }
                    }
                    pdo_update('ewei_shop_member', array('agentlevel' => $weizan_143['id']), array('id' => $weizan_144['id']));
                    $this -> sendMessage($weizan_144['openid'], array('nickname' => $weizan_144['nickname'], 'oldlevel' => $weizan_141, 'newlevel' => $weizan_143,), TM_COMMISSION_UPGRADE);
                }
            }
        }
        function upgradeLevelByAgent($weizan_21){
            global $_W;
            if (empty($weizan_21)){
                return false;
            }
            $weizan_1 = $this -> getSet();
            if (empty($weizan_1['level'])){
                return false;
            }
            $weizan_133 = m('member') -> getMember($weizan_21);
            if (empty($weizan_133)){
                return;
            }
            $weizan_140 = intval($weizan_1['leveltype']);
            if ($weizan_140 < 6 || $weizan_140 > 9){
                return;
            }
            $weizan_145 = $this -> getInfo($weizan_133['id'], array());
            if ($weizan_140 == 6 || $weizan_140 == 8){
                $weizan_77 = array($weizan_133);
                if (!empty($weizan_133['agentid'])){
                    $weizan_11 = m('member') -> getMember($weizan_133['agentid']);
                    if (!empty($weizan_11)){
                        $weizan_77[] = $weizan_11;
                        if (!empty($weizan_11['agentid']) && $weizan_11['isagent'] == 1 && $weizan_11['status'] == 1){
                            $weizan_13 = m('member') -> getMember($weizan_11['agentid']);
                            if (!empty($weizan_13) && $weizan_13['isagent'] == 1 && $weizan_13['status'] == 1){
                                $weizan_77[] = $weizan_13;
                            }
                        }
                    }
                }
                if (empty($weizan_77)){
                    return;
                }
                foreach ($weizan_77 as $weizan_144){
                    $weizan_145 = $this -> getInfo($weizan_144['id'], array());
                    if (!empty($weizan_145['agentnotupgrade'])){
                        continue;
                    }
                    $weizan_141 = $this -> getLevel($weizan_144['openid']);
                    if (empty($weizan_141['id'])){
                        $weizan_141 = array('levelname' => empty($weizan_1['levelname']) ? '普通等级' : $weizan_1['levelname'], 'commission1' => $weizan_1['commission1'], 'commission2' => $weizan_1['commission2'], 'commission3' => $weizan_1['commission3']);
                    }
                    if ($weizan_140 == 6){
                        $weizan_146 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $weizan_133['id'], ':uniacid' => $_W['uniacid']), 'id');
                        $weizan_147 += count($weizan_146);
                        if (!empty($weizan_146)){
                            $weizan_148 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($weizan_146)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
                            $weizan_147 += count($weizan_148);
                            if (!empty($weizan_148)){
                                $weizan_149 = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($weizan_148)) . ') and uniacid=:uniacid', array(':uniacid' => $_W['uniacid']), 'id');
                                $weizan_147 += count($weizan_149);
                            }
                        }
                        $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$weizan_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
                    }else if ($weizan_140 == 8){
                        $weizan_147 = $weizan_145['level1'] + $weizan_145['level2'] + $weizan_145['level3'];
                        $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$weizan_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
                    }
                    if (empty($weizan_143)){
                        continue;
                    }
                    if ($weizan_143['id'] == $weizan_141['id']){
                        continue;
                    }
                    if (!empty($weizan_141['id'])){
                        if ($weizan_141['downcount'] > $weizan_143['downcount']){
                            continue;
                        }
                    }
                    pdo_update('ewei_shop_member', array('agentlevel' => $weizan_143['id']), array('id' => $weizan_144['id']));
                    $this -> sendMessage($weizan_144['openid'], array('nickname' => $weizan_144['nickname'], 'oldlevel' => $weizan_141, 'newlevel' => $weizan_143,), TM_COMMISSION_UPGRADE);
                }
            }else{
                if (!empty($weizan_133['agentnotupgrade'])){
                    return;
                }
                $weizan_141 = $this -> getLevel($weizan_133['openid']);
                if (empty($weizan_141['id'])){
                    $weizan_141 = array('levelname' => empty($weizan_1['levelname']) ? '普通等级' : $weizan_1['levelname'], 'commission1' => $weizan_1['commission1'], 'commission2' => $weizan_1['commission2'], 'commission3' => $weizan_1['commission3']);
                }
                if ($weizan_140 == 7){
                    $weizan_147 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid ', array(':agentid' => $weizan_133['id'], ':uniacid' => $_W['uniacid']));
                    $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$weizan_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
                }else if ($weizan_140 == 9){
                    $weizan_147 = $weizan_145['level1'];
                    $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$weizan_147} >= downcount and downcount>0  order by downcount desc limit 1", array(':uniacid' => $_W['uniacid']));
                }
                if (empty($weizan_143)){
                    return;
                }
                if ($weizan_143['id'] == $weizan_141['id']){
                    return;
                }
                if (!empty($weizan_141['id'])){
                    if ($weizan_141['downcount'] > $weizan_143['downcount']){
                        return;
                    }
                }
                pdo_update('ewei_shop_member', array('agentlevel' => $weizan_143['id']), array('id' => $weizan_133['id']));
                $this -> sendMessage($weizan_133['openid'], array('nickname' => $weizan_133['nickname'], 'oldlevel' => $weizan_141, 'newlevel' => $weizan_143,), TM_COMMISSION_UPGRADE);
            }
        }
        function upgradeLevelByCommissionOK($weizan_21){
            global $_W;
            if (empty($weizan_21)){
                return false;
            }
            $weizan_1 = $this -> getSet();
            if (empty($weizan_1['level'])){
                return false;
            }
            $weizan_133 = m('member') -> getMember($weizan_21);
            if (empty($weizan_133)){
                return;
            }
            $weizan_140 = intval($weizan_1['leveltype']);
            if ($weizan_140 != 10){
                return;
            }
            if (!empty($weizan_133['agentnotupgrade'])){
                return;
            }
            $weizan_141 = $this -> getLevel($weizan_133['openid']);
            if (empty($weizan_141['id'])){
                $weizan_141 = array('levelname' => empty($weizan_1['levelname']) ? '普通等级' : $weizan_1['levelname'], 'commission1' => $weizan_1['commission1'], 'commission2' => $weizan_1['commission2'], 'commission3' => $weizan_1['commission3']);
            }
            $weizan_145 = $this -> getInfo($weizan_133['id'], array('pay'));
            $weizan_150 = $weizan_145['commission_pay'];
            $weizan_143 = pdo_fetch('select * from ' . tablename('ewei_shop_commission_level') . " where uniacid=:uniacid  and {$weizan_150} >= commissionmoney and commissionmoney>0  order by commissionmoney desc limit 1", array(':uniacid' => $_W['uniacid']));
            if (empty($weizan_143)){
                return;
            }
            if ($weizan_141['id'] == $weizan_143['id']){
                return;
            }
            if (!empty($weizan_141['id'])){
                if ($weizan_141['commissionmoney'] > $weizan_143['commissionmoney']){
                    return;
                }
            }
            pdo_update('ewei_shop_member', array('agentlevel' => $weizan_143['id']), array('id' => $weizan_133['id']));
            $this -> sendMessage($weizan_133['openid'], array('nickname' => $weizan_133['nickname'], 'oldlevel' => $weizan_141, 'newlevel' => $weizan_143,), TM_COMMISSION_UPGRADE);
        }
        function sendMessage($weizan_21 = '', $weizan_151 = array(), $weizan_152 = ''){
            global $_W, $_GPC;
            $weizan_1 = $this -> getSet();
            $weizan_153 = $weizan_1['tm'];
            $weizan_154 = $weizan_153['templateid'];
            $weizan_23 = m('member') -> getMember($weizan_21);
            $weizan_155 = unserialize($weizan_23['noticeset']);
            if (!is_array($weizan_155)){
                $weizan_155 = array();
            }
            if ($weizan_152 == TM_COMMISSION_AGENT_NEW && !empty($weizan_153['commission_agent_new']) && empty($weizan_155['commission_agent_new'])){
                $weizan_156 = $weizan_153['commission_agent_new'];
                $weizan_156 = str_replace('[昵称]', $weizan_151['nickname'], $weizan_156);
                $weizan_156 = str_replace('[时间]', date('Y-m-d H:i:s', $weizan_151['childtime']), $weizan_156);
                $weizan_157 = array('keyword1' => array('value' => !empty($weizan_153['commission_agent_newtitle']) ? $weizan_153['commission_agent_newtitle'] : '新增下线通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $weizan_156, 'color' => '#73a68d'));
                if (!empty($weizan_154)){
                    m('message') -> sendTplNotice($weizan_21, $weizan_154, $weizan_157);
                }else{
                    m('message') -> sendCustomNotice($weizan_21, $weizan_157);
                }
            }else if ($weizan_152 == TM_COMMISSION_ORDER_PAY && !empty($weizan_153['commission_order_pay']) && empty($weizan_155['commission_order_pay'])){
                $weizan_156 = $weizan_153['commission_order_pay'];
                $weizan_156 = str_replace('[昵称]', $weizan_151['nickname'], $weizan_156);
                $weizan_156 = str_replace('[时间]', date('Y-m-d H:i:s', $weizan_151['paytime']), $weizan_156);
                $weizan_156 = str_replace('[订单编号]', $weizan_151['ordersn'], $weizan_156);
                $weizan_156 = str_replace('[订单金额]', $weizan_151['price'], $weizan_156);
                $weizan_156 = str_replace('[佣金金额]', $weizan_151['commission'], $weizan_156);
                $weizan_156 = str_replace('[商品详情]', $weizan_151['goods'], $weizan_156);
                $weizan_157 = array('keyword1' => array('value' => !empty($weizan_153['commission_order_paytitle']) ? $weizan_153['commission_order_paytitle'] : '下线付款通知'), 'keyword2' => array('value' => $weizan_156));
                if (!empty($weizan_154)){
                    m('message') -> sendTplNotice($weizan_21, $weizan_154, $weizan_157);
                }else{
                    m('message') -> sendCustomNotice($weizan_21, $weizan_157);
                }
            }else if ($weizan_152 == TM_COMMISSION_ORDER_FINISH && !empty($weizan_153['commission_order_finish']) && empty($weizan_155['commission_order_finish'])){
                $weizan_156 = $weizan_153['commission_order_finish'];
                $weizan_156 = str_replace('[昵称]', $weizan_151['nickname'], $weizan_156);
                $weizan_156 = str_replace('[时间]', date('Y-m-d H:i:s', $weizan_151['finishtime']), $weizan_156);
                $weizan_156 = str_replace('[订单编号]', $weizan_151['ordersn'], $weizan_156);
                $weizan_156 = str_replace('[订单金额]', $weizan_151['price'], $weizan_156);
                $weizan_156 = str_replace('[佣金金额]', $weizan_151['commission'], $weizan_156);
                $weizan_156 = str_replace('[商品详情]', $weizan_151['goods'], $weizan_156);
                $weizan_157 = array('keyword1' => array('value' => !empty($weizan_153['commission_order_finishtitle']) ? $weizan_153['commission_order_finishtitle'] : '下线确认收货通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $weizan_156, 'color' => '#73a68d'));
                if (!empty($weizan_154)){
                    m('message') -> sendTplNotice($weizan_21, $weizan_154, $weizan_157);
                }else{
                    m('message') -> sendCustomNotice($weizan_21, $weizan_157);
                }
            }else if ($weizan_152 == TM_COMMISSION_APPLY && !empty($weizan_153['commission_apply']) && empty($weizan_155['commission_apply'])){
                $weizan_156 = $weizan_153['commission_apply'];
                $weizan_156 = str_replace('[昵称]', $weizan_23['nickname'], $weizan_156);
                $weizan_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $weizan_156);
                $weizan_156 = str_replace('[金额]', $weizan_151['commission'], $weizan_156);
                $weizan_156 = str_replace('[提现方式]', $weizan_151['type'], $weizan_156);
                $weizan_157 = array('keyword1' => array('value' => !empty($weizan_153['commission_applytitle']) ? $weizan_153['commission_applytitle'] : '提现申请提交成功', 'color' => '#73a68d'), 'keyword2' => array('value' => $weizan_156, 'color' => '#73a68d'));
                if (!empty($weizan_154)){
                    m('message') -> sendTplNotice($weizan_21, $weizan_154, $weizan_157);
                }else{
                    m('message') -> sendCustomNotice($weizan_21, $weizan_157);
                }
            }else if ($weizan_152 == TM_COMMISSION_CHECK && !empty($weizan_153['commission_check']) && empty($weizan_155['commission_check'])){
                $weizan_156 = $weizan_153['commission_check'];
                $weizan_156 = str_replace('[昵称]', $weizan_23['nickname'], $weizan_156);
                $weizan_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $weizan_156);
                $weizan_156 = str_replace('[金额]', $weizan_151['commission'], $weizan_156);
                $weizan_156 = str_replace('[提现方式]', $weizan_151['type'], $weizan_156);
                $weizan_157 = array('keyword1' => array('value' => !empty($weizan_153['commission_checktitle']) ? $weizan_153['commission_checktitle'] : '提现申请审核处理完成', 'color' => '#73a68d'), 'keyword2' => array('value' => $weizan_156, 'color' => '#73a68d'));
                if (!empty($weizan_154)){
                    m('message') -> sendTplNotice($weizan_21, $weizan_154, $weizan_157);
                }else{
                    m('message') -> sendCustomNotice($weizan_21, $weizan_157);
                }
            }else if ($weizan_152 == TM_COMMISSION_PAY && !empty($weizan_153['commission_pay']) && empty($weizan_155['commission_pay'])){
                $weizan_156 = $weizan_153['commission_pay'];
                $weizan_156 = str_replace('[昵称]', $weizan_23['nickname'], $weizan_156);
                $weizan_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $weizan_156);
                $weizan_156 = str_replace('[金额]', $weizan_151['commission'], $weizan_156);
                $weizan_156 = str_replace('[提现方式]', $weizan_151['type'], $weizan_156);
                $weizan_157 = array('keyword1' => array('value' => !empty($weizan_153['commission_paytitle']) ? $weizan_153['commission_paytitle'] : '佣金打款通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $weizan_156, 'color' => '#73a68d'));
                if (!empty($weizan_154)){
                    m('message') -> sendTplNotice($weizan_21, $weizan_154, $weizan_157);
                }else{
                    m('message') -> sendCustomNotice($weizan_21, $weizan_157);
                }
            }else if ($weizan_152 == TM_COMMISSION_UPGRADE && !empty($weizan_153['commission_upgrade']) && empty($weizan_155['commission_upgrade'])){
                $weizan_156 = $weizan_153['commission_upgrade'];
                $weizan_156 = str_replace('[昵称]', $weizan_23['nickname'], $weizan_156);
                $weizan_156 = str_replace('[时间]', date('Y-m-d H:i:s', time()), $weizan_156);
                $weizan_156 = str_replace('[旧等级]', $weizan_151['oldlevel']['levelname'], $weizan_156);
                $weizan_156 = str_replace('[旧一级分销比例]', $weizan_151['oldlevel']['commission1'] . '%', $weizan_156);
                $weizan_156 = str_replace('[旧二级分销比例]', $weizan_151['oldlevel']['commission2'] . '%', $weizan_156);
                $weizan_156 = str_replace('[旧三级分销比例]', $weizan_151['oldlevel']['commission3'] . '%', $weizan_156);
                $weizan_156 = str_replace('[新等级]', $weizan_151['newlevel']['levelname'], $weizan_156);
                $weizan_156 = str_replace('[新一级分销比例]', $weizan_151['newlevel']['commission1'] . '%', $weizan_156);
                $weizan_156 = str_replace('[新二级分销比例]', $weizan_151['newlevel']['commission2'] . '%', $weizan_156);
                $weizan_156 = str_replace('[新三级分销比例]', $weizan_151['newlevel']['commission3'] . '%', $weizan_156);
                $weizan_157 = array('keyword1' => array('value' => !empty($weizan_153['commission_upgradetitle']) ? $weizan_153['commission_upgradetitle'] : '分销等级升级通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $weizan_156, 'color' => '#73a68d'));
                if (!empty($weizan_154)){
                    m('message') -> sendTplNotice($weizan_21, $weizan_154, $weizan_157);
                }else{
                    m('message') -> sendCustomNotice($weizan_21, $weizan_157);
                }
            }else if ($weizan_152 == TM_COMMISSION_BECOME && !empty($weizan_153['commission_become']) && empty($weizan_155['commission_become'])){
                $weizan_156 = $weizan_153['commission_become'];
                $weizan_156 = str_replace('[昵称]', $weizan_151['nickname'], $weizan_156);
                $weizan_156 = str_replace('[时间]', date('Y-m-d H:i:s', $weizan_151['agenttime']), $weizan_156);
                $weizan_157 = array('keyword1' => array('value' => !empty($weizan_153['commission_becometitle']) ? $weizan_153['commission_becometitle'] : '成为分销商通知', 'color' => '#73a68d'), 'keyword2' => array('value' => $weizan_156, 'color' => '#73a68d'));
                if (!empty($weizan_154)){
                    m('message') -> sendTplNotice($weizan_21, $weizan_154, $weizan_157);
                }else{
                    m('message') -> sendCustomNotice($weizan_21, $weizan_157);
                }
            }
        }
        function perms(){
            return array('commission' => array('text' => $this -> getName(), 'isplugin' => true, 'child' => array('cover' => array('text' => '入口设置'), 'agent' => array('text' => '分销商', 'view' => '浏览', 'check' => '审核-log', 'edit' => '修改-log', 'agentblack' => '黑名单操作-log', 'delete' => '删除-log', 'user' => '查看下线', 'order' => '查看推广订单(还需有订单权限)', 'changeagent' => '设置分销商'), 'level' => array('text' => '分销商等级', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'apply' => array('text' => '佣金审核', 'view1' => '浏览待审核', 'view2' => '浏览已审核', 'view3' => '浏览已打款', 'view_1' => '浏览无效', 'export1' => '导出待审核-log', 'export2' => '导出已审核-log', 'export3' => '导出已打款-log', 'export_1' => '导出无效-log', 'check' => '审核-log', 'pay' => '打款-log', 'cancel' => '重新审核-log'), 'notice' => array('text' => '通知设置-log'), 'increase' => array('text' => '分销商趋势图'), 'changecommission' => array('text' => '修改佣金-log'), 'set' => array('text' => '基础设置-log'))));
        }
    }
}
