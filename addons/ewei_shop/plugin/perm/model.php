<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
if (!class_exists('PermModel')){
    class PermModel extends PluginModel{
        public function allPerms(){
            $weizan_0 = array('shop' => array('text' => '商城管理', 'child' => array('goods' => array('text' => '商品', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'category' => array('text' => '商品分类', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'dispatch' => array('text' => '配送方式', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'adv' => array('text' => '幻灯片', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'notice' => array('text' => '公告', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'comment' => array('text' => '评价', 'view' => '浏览', 'add' => '添加评论-log', 'edit' => '回复-log', 'delete' => '删除-log'), 'refundaddress' => array('text' => '退货地址', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'))), 'member' => array('text' => '会员管理', 'child' => array('member' => array('text' => '会员', 'view' => '浏览', 'edit' => '修改-log', 'delete' => '删除-log', 'export' => '导出-log'), 'group' => array('text' => '会员组', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'level' => array('text' => '会员等级', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'))), 'order' => array('text' => '订单管理', 'child' => array('view' => array('text' => '浏览', 'status_1' => '浏览关闭订单', 'status0' => '浏览待付款订单', 'status1' => '浏览已付款订单', 'status2' => '浏览已发货订单', 'status3' => '浏览完成的订单', 'status4' => '浏览退货申请订单', 'status5' => '浏览已退货订单',), 'op' => array('text' => '操作', 'pay' => '确认付款-log', 'send' => '发货-log', 'sendcancel' => '取消发货-log', 'finish' => '确认收货(快递单)-log', 'verify' => '确认核销(核销单)-log', 'fetch' => '确认取货(自提单)-log', 'close' => '关闭订单-log', 'refund' => '退货处理-log', 'export' => '导出订单-log', 'changeprice' => '订单改价-log' , 'changeaddress' => '修改订单地址-log'))), 'finance' => array('text' => '财务管理', 'child' => array('recharge' => array('text' => '充值', 'view' => '浏览', 'credit1' => '充值积分-log', 'credit2' => '充值余额-log', 'refund' => '充值退款-log', 'export' => '导出充值记录-log'), 'withdraw' => array('text' => '提现', 'view' => '浏览', 'withdraw' => '提现-log', 'export' => '导出提现记录-log'), 'downloadbill' => array('text' => '下载对账单'),)), 'statistics' => array('text' => '数据统计', 'child' => array('view' => array('text' => '浏览权限', 'sale' => '销售指标', 'sale_analysis' => '销售统计', 'order' => '订单统计', 'goods' => '商品销售统计', 'goods_rank' => '商品销售排行', 'goods_trans' => '商品销售转化率', 'member_cost' => '会员消费排行', 'member_increase' => '会员增长趋势'), 'export' => array('text' => '导出', 'sale' => '导出销售统计-log', 'order' => '导出订单统计-log', 'goods' => '导出商品销售统计-log', 'goods_rank' => '导出商品销售排行-log', 'goods_trans' => '商品销售转化率-log', 'member_cost' => '会员消费排行-log'),)), 'sysset' => array('text' => '系统设置', 'child' => array('view' => array('text' => '浏览', 'shop' => '商城设置', 'follow' => '引导及分享设置', 'notice' => '模板消息设置', 'trade' => '交易设置', 'pay' => '支付方式设置', 'template' => '模板设置', 'member' => '会员设置', 'category' => '分类层级设置', 'contact' => '联系方式设置'), 'save' => array('text' => '修改', 'shop' => '修改商城设置-log', 'follow' => '修改引导及分享设置-log', 'notice' => '修改模板消息设置-log', 'trade' => '修改交易设置-log', 'pay' => '修改支付方式设置-log', 'template' => '模板设置-log', 'member' => '会员设置-log', 'category' => '分类层级设置-log', 'contact' => '联系方式设置-log'))),);
            $weizan_1 = m('plugin') -> getAll();
            foreach ($weizan_1 as $weizan_2){
                $weizan_3 = p($weizan_2['identity']);
                if ($weizan_3){
                    if (method_exists($weizan_3, 'perms')){
                        $weizan_4 = $weizan_3 -> perms();
                        $weizan_0 = array_merge($weizan_0, $weizan_4);
                    }
                }
            }
            return $weizan_0;
        }
        public function isopen($weizan_5 = ''){
            if (empty($weizan_5)){
                return false;
            }
            $weizan_1 = m('plugin') -> getAll();
            foreach ($weizan_1 as $weizan_2){
                if ($weizan_2['identity'] == strtolower($weizan_5)){
                    if (empty($weizan_2['status'])){
                        return false;
                    }
                }
            }
            return true;
        }
        public function check_edit($weizan_6 = '', $weizan_7 = array()){
            if (empty($weizan_6)){
                return false;
            }
            if (!$this -> check_perm($weizan_6)){
                return false;
            }
            if (empty($weizan_7['id'])){
                $weizan_8 = $weizan_6 . '.add';
                if (!$this -> check($weizan_8)){
                    return false;
                }
                return true;
            }else{
                $weizan_9 = $weizan_6 . '.edit';
                if (!$this -> check($weizan_9)){
                    return false;
                }
                return true;
            }
        }
        public function check_perm($weizan_10 = ''){
            global $_W;
            $weizan_11 = true;
            if (empty($weizan_10)){
                return false;
            }
            if (!strexists($weizan_10, '&') && !strexists($weizan_10, '|')){
                $weizan_11 = $this -> check($weizan_10);
            }else if (strexists($weizan_10, '&')){
                $weizan_12 = explode('&', $weizan_10);
                foreach ($weizan_12 as $weizan_13){
                    $weizan_11 = $this -> check($weizan_13);
                    if (!$weizan_11){
                        break;
                    }
                }
            }else if (strexists($weizan_10, '|')){
                $weizan_12 = explode('|', $weizan_10);
                foreach ($weizan_12 as $weizan_13){
                    $weizan_11 = $this -> check($weizan_13);
                    if ($weizan_11){
                        break;
                    }
                }
            }
            return $weizan_11;
        }
        private function check($weizan_6 = ''){
            global $_W, $_GPC;
            if ($_W['role'] == 'manager' || $_W['role'] == 'founder'){
                return true;
            }
            $weizan_14 = $_W['uid'];
            if (empty($weizan_6)){
                return false;
            }
            $weizan_15 = pdo_fetch('select u.status as userstatus,r.status as rolestatus,u.perms as userperms,r.perms as roleperms,u.roleid from ' . tablename('ewei_shop_perm_user') . ' u ' . ' left join ' . tablename('ewei_shop_perm_role') . ' r on u.roleid = r.id ' . ' where uid=:uid limit 1 ', array(':uid' => $weizan_14));
            if (empty($weizan_15) || empty($weizan_15['userstatus'])){
                return false;
            }
            if(!empty($weizan_15['role']) && empty($weizan_15['rolestatus'])){
                return true;
            }
            $weizan_16 = explode(',', $weizan_15['roleperms']);
            $weizan_17 = explode(',', $weizan_15['userperms']);
            $weizan_0 = array_merge($weizan_16, $weizan_17);
            if (empty($weizan_0)){
                return false;
            }
            $weizan_18 = explode('.', $weizan_6);
            if (!in_array($weizan_18[0], $weizan_0)){
                return false;
            }
            if (isset($weizan_18[1]) && !in_array($weizan_18[0] . '.' . $weizan_18[1], $weizan_0)){
                return false;
            }
            if (isset($weizan_18[2]) && !in_array($weizan_18[0] . '.' . $weizan_18[1] . '.' . $weizan_18[2], $weizan_0)){
                return false;
            }
            return true;
        }
        function check_plugin($weizan_5 = ''){
            global $_W, $_GPC;
            $weizan_19 = m('cache') -> getString('permset', 'global');
            if(empty($weizan_19)){
                return true;
            }
            if ($_W['role'] == 'founder'){
                return true;
            }
            $weizan_20 = $this -> isopen($weizan_5);
            if (!$weizan_20){
                return false;
            }
            $weizan_21 = true;
            $weizan_22 = pdo_fetchcolumn('SELECT acid FROM ' . tablename('account_wechats') . ' WHERE `uniacid`=:uniacid LIMIT 1', array(':uniacid' => $_W['uniacid']));
            $weizan_23 = pdo_fetch('select  plugins from ' . tablename('ewei_shop_perm_plugin') . ' where acid=:acid limit 1', array(':acid' => $weizan_22));
            if (!empty($weizan_23)){
                $weizan_24 = explode(',', $weizan_23['plugins']);
                if (!in_array($weizan_5, $weizan_24)){
                    $weizan_21 = false;
                }
            }else{
                load() -> model('account');
                $weizan_25 = uni_owned($_W['founder']);
                if(in_array($_W['uniacid'], array_keys($weizan_25))){
                    $weizan_21 = true;
                }else{
                    $weizan_21 = false;
                }
            }
            if (!$weizan_21){
                return false;
            }
            return $this -> check($weizan_5);
        }
        public function getLogName($weizan_26 = '', $weizan_27 = null){
            if (!$weizan_27){
                $weizan_27 = $this -> getLogTypes();
            }
            foreach ($weizan_27 as $weizan_28){
                if ($weizan_28['value'] == $weizan_26){
                    return $weizan_28['text'];
                }
            }
            return '';
        }
        public function getLogTypes(){
            $weizan_29 = array();
            $weizan_0 = $this -> allPerms();
            foreach ($weizan_0 as $weizan_30 => $weizan_31){
                if (isset($weizan_31['child'])){
                    foreach ($weizan_31['child'] as $weizan_32 => $weizan_33){
                        foreach ($weizan_33 as $weizan_34 => $weizan_35){
                            if (strexists($weizan_35, '-log')){
                                $weizan_36 = str_replace('-log', "", $weizan_31['text'] . '-' . $weizan_33['text'] . '-' . $weizan_35);
                                if ($weizan_34 == 'text'){
                                    $weizan_36 = str_replace('-log', "", $weizan_31['text'] . '-' . $weizan_33['text']);
                                }
                                $weizan_29[] = array('text' => $weizan_36, 'value' => str_replace('.text', "", $weizan_30 . '.' . $weizan_32 . '.' . $weizan_34));
                            }
                        }
                    }
                }else{
                    foreach ($weizan_31 as $weizan_34 => $weizan_35){
                        if (strexists($weizan_35, '-log')){
                            $weizan_36 = str_replace('-log', "", $weizan_31['text'] . '-' . $weizan_35);
                            if ($weizan_34 == 'text'){
                                $weizan_36 = str_replace('-log', "", $weizan_31['text']);
                            }
                            $weizan_29[] = array('text' => $weizan_36, 'value' => str_replace('.text', "", $weizan_30 . '.' . $weizan_34));
                        }
                    }
                }
            }
            return $weizan_29;
        }
        public function log($weizan_26 = '', $weizan_37 = ''){
            global $_W;
            static $weizan_38;
            if (!$weizan_38){
                $weizan_38 = $this -> getLogTypes();
            }
            $weizan_39 = array('uniacid' => $_W['uniacid'], 'uid' => $_W['uid'], 'name' => $this -> getLogName($weizan_26, $weizan_38), 'type' => $weizan_26, 'op' => $weizan_37, 'ip' => CLIENT_IP, 'createtime' => time());
            pdo_insert('ewei_shop_perm_log', $weizan_39);
        }
        public function perms(){
            return array('perm' => array('text' => $this -> getName(), 'isplugin' => true, 'child' => array('set' => array('text' => '基础设置'), 'role' => array('text' => '角色', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'user' => array('text' => '操作员', 'view' => '浏览', 'add' => '添加-log', 'edit' => '修改-log', 'delete' => '删除-log'), 'log' => array('text' => '操作日志', 'view' => '浏览', 'delete' => '删除-log', 'clear' => '清除-log'),)));
        }
    }
}
