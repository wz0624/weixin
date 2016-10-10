<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Ewei_DShop_Plugin{
    public function getSet($weizan_0 = '', $weizan_1 = '', $weizan_2 = 0){
        global $_W, $_GPC;
        if (empty($weizan_2)){
            $weizan_2 = $_W['uniacid'];
        }
        $weizan_3 = m('cache') -> getArray('sysset', $weizan_2);
        if(empty($weizan_3)){
            $weizan_3 = pdo_fetch('select * from ' . tablename('ewei_shop_sysset') . ' where uniacid=:uniacid limit 1', array(':uniacid' => $weizan_2));
        }
        if (empty($weizan_3)){
            return array();
        }
        $weizan_4 = unserialize($weizan_3['sets']);
        if (empty($weizan_1)){
            return $weizan_4;
        }
        return $weizan_4[$weizan_1];
    }
    public function exists($weizan_5 = ''){
        $weizan_6 = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . ' where identity=:identyty limit  1', array(':identity' => $weizan_5));
        if(empty($weizan_6)){
            return false;
        }
        return true;
    }
    public function getAll(){
        global $_W;
        $weizan_7 = m('cache') -> getArray('plugins', 'global');
        if(empty($weizan_7)){
            $weizan_7 = pdo_fetchall('select * from ' . tablename('ewei_shop_plugin') . ' order by displayorder asc');
            m('cache') -> set('plugins', $weizan_7, 'global');
        }
        return $weizan_7;
    }
    public function getCategory(){
        return array('biz' => array('name' => '业务类'), 'sale' => array('name' => '营销类'), 'tool' => array('name' => '工具类'), 'help' => array('name' => '辅助类'));
    }
}
