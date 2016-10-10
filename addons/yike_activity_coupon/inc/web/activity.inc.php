<?php
/**
 * Created by PhpStorm.
 * User: stevezheng
 * Date: 15/12/24
 * Time: 17:18
 */
global $_W, $_GPC;

checklogin();

load()->func('tpl');
$uniacid = $_W['uniacid'];

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'details';

if ($operation == 'list') {
    $all_activity = pdo_fetchall("select * from" . tablename('yike_activity_coupon') . "where uniacid='{$_W['uniacid']}'");
    include $this->template('web/activity/list');
} else if ($operation == 'send') {   //存入消息模板ID
    if ($_W['ispost']) {
        if($_GPC['getId']==''||$_GPC['getHead']==''||$_GPC['getTail']==''){
            $get=0;
        }else{
            $get=array(
                'uniacid' => $uniacid,
                'template_id'=>$_GPC['getId'],
                'first_data'=>$_GPC['getHead'],
                'remark_data'=>$_GPC['getTail'],
                'type'=>1,

            );
        }
        if($_GPC['overId']==''||$_GPC['overHead']==''||$_GPC['overTail']==''){
            $over=0;
        }else{
            $over=array(
                'uniacid' => $uniacid,
                'template_id'=>$_GPC['overId'],
                'first_data'=>$_GPC['overHead'],
                'remark_data'=>$_GPC['overTail'],
                'type'=>2
            );
        }
        if($_GPC['useId']==''||$_GPC['useHead']==''||$_GPC['useTail']==''){
            $use=0;
        }else{
            $use=array(
                'uniacid' => $uniacid,
                'template_id'=>$_GPC['useId'],
                'first_data'=>$_GPC['useHead'],
                'remark_data'=>$_GPC['useTail'],
                'type'=>3,

            );
        }
        //查询是否有消息模板1是获得代金券2是过期代金券3是提示使用
        $isget = pdo_fetch("select * from" . tablename('yike_vouchers_template_message') . "where type=1");
        $isover = pdo_fetch("select * from" . tablename('yike_vouchers_template_message') . "where type=2");
        $isuse = pdo_fetch("select * from" . tablename('yike_vouchers_template_message') . "where type=3");
        if($get!=0){
            if($isget==false){
                $ret=pdo_insert('yike_vouchers_template_message', $get);
            }else{
                $ret=pdo_update('yike_vouchers_template_message', $get,array('id' => $isget['id']));
            }
        }
        if($over!=0){
            if($isover==false){
                $ret=pdo_insert('yike_vouchers_template_message', $over);
            }else{
                $ret=pdo_update('yike_vouchers_template_message', $over,array('id' => $isover['id']));
            }
        }
        if($use!=0){
            if($isuse==false){
                $ret=pdo_insert('yike_vouchers_template_message', $use);
            }else{
                $ret=pdo_update('yike_vouchers_template_message', $use,array('id' => $isuse['id']));
            }
        }

        $this->show_json(1, $ret);
    }
    include $this->template('web/activity/details');
} else if ($operation == 'add') {
    if ($_W['ispost']) {
        $data = array(
            'name' => $_GPC['name'],
            'start_time' => 0,
            'end_time' => 0,
            'coupon_ids' => $_GPC['coupon_ids'],
            'is_activity' => '',
            'create_time' => 0,
            'total' => $_GPC['total'],
            'used' => 0,
            'uniacid' => $uniacid,
            'thumb' => $_GPC['thumb'],
            'url' => $_GPC['url']
        );
        $ret = pdo_insert('yike_activity_coupon', $data);
        $this->show_json(1, $ret);
    }
    include $this->template('web/activity/add');
} else if ($operation == 'edit') {
    include $this->template('web/activity/edit');
} else if ($operation == 'details') {
    if ($_W['ispost']) {
        $data = array(
            'name' => $_GPC['name'],
            'start_time' => 0,
            'end_time' => 0,
            'coupon_ids' => $_GPC['coupon_ids'],
            'is_activity' => '',
            'create_time' => 0,
            'total' => $_GPC['total'],
            'used' => 0,
            'uniacid' => $uniacid,
            'thumb' => $_GPC['thumb'],
            'url' => $_GPC['url']
        );
        if (!empty($_GPC['id'])) {
            $ret = pdo_update('yike_activity_coupon', $data, array('id' => $_GPC['id']));
        } else {
            $ret = pdo_insert('yike_activity_coupon', $data);
        }
        $this->show_json(1, $ret);
    } else {
        $activity = pdo_fetch("select * from" . tablename('yike_activity_coupon') . "where uniacid='{$_W['uniacid']}'");
        $all_activity = pdo_fetchall("select * from" . tablename('activity_coupon') . "where uniacid='{$_W['uniacid']}'");
        include $this->template('web/activity/details');
    }
}

