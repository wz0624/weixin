<?php
/**
 * Created by PhpStorm.
 * User: stevezheng
 * Date: 15/12/24
 * Time: 17:21
 */

global $_W, $_GPC;

checklogin();

load()->func('tpl');
load()->model('mc');
load()->model('activity');
load()->func('communication');


$uniacid = $_W['uniacid'];

$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'list';


if ($operation == 'list') {
    $_W['page']['title'] = '粉丝列表 - 粉丝 - 会员中心';
    $accounts = uni_accounts();
    if (empty($accounts) || !is_array($accounts) || count($accounts) == 0) {
        message('请指定公众号');
    }
    if (!isset($_GPC['acid'])) {
        $account = current($accounts);
        if ($account !== false) {
            $acid = intval($account['acid']);
        }
    } else {
        $acid = intval($_GPC['acid']);
        if (!empty($acid) && !empty($accounts[$acid])) {
            $account = $accounts[$acid];
        }
    }
    reset($accounts);

    $pindex = max(1, intval($_GPC['page']));
    $psize = 20;
    $condition = ' WHERE `uniacid`=:uniacid';
    $pars = array();
    $pars[':uniacid'] = $_W['uniacid'];
    if (!empty($acid)) {
        $condition .= ' AND `acid`=:acid';
        $pars[':acid'] = $acid;
    }
    if ($_GPC['type'] == 'bind') {
        $condition .= ' AND `uid`>0';
        $type = 'bind';
    }
    if ($_GPC['type'] == 'unbind') {
        $condition .= ' AND `uid`=0';
        $type = 'unbind';
    }
    $nickname = trim($_GPC['nickname']);
    if (!empty($nickname)) {
        $condition .= " AND nickname LIKE '%{$nickname}%'";
    }
    $starttime = empty($_GPC['time']['start']) ? strtotime('-360 days') : strtotime($_GPC['time']['start']);
    $endtime = empty($_GPC['time']['end']) ? TIMESTAMP + 86399 : strtotime($_GPC['time']['end']) + 86399;
    $follow = intval($_GPC['follow']);
    if (!$follow) {
        $orderby = ' ORDER BY fanid DESC';
        $condition .= ' AND ((followtime >= :starttime AND followtime <= :endtime) OR (unfollowtime >= :starttime AND unfollowtime <= :endtime))';
    } elseif ($follow == 1) {
        $orderby = ' ORDER BY followtime DESC';
        $condition .= ' AND follow = 1 AND followtime >= :starttime AND followtime <= :endtime';
    } elseif ($follow == 2) {
        $orderby = ' ORDER BY unfollowtime DESC';
        $condition .= ' AND follow = 0 AND unfollowtime >= :starttime AND unfollowtime <= :endtime';
    }
    $pars[':starttime'] = $starttime;
    $pars[':endtime'] = $endtime;

    $groups_data = pdo_fetchall('SELECT * FROM ' . tablename('mc_fans_groups') . ' WHERE uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
    if (!empty($groups_data)) {
        $groups = array();
        foreach ($groups_data as $gr) {
            $groups[$gr['acid']] = iunserializer($gr['groups']);
        }
    }
    $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('mc_mapping_fans') . $condition, $pars);
    $list = pdo_fetchall("SELECT * FROM " . tablename('mc_mapping_fans') . $condition . $orderby . ' LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, $pars);
    if (!empty($list)) {
        foreach ($list as &$v) {
            if (!empty($v['uid'])) {
                $user = mc_fetch($v['uid'], array('realname', 'nickname', 'mobile', 'email', 'avatar'));
                if (!empty($user['avatar'])) {
                    $user['avatar'] = tomedia($user['avatar']);
                }
            }
            if (!empty($v['tag']) && is_string($v['tag'])) {
                if (is_base64($v['tag'])) {
                    $v['tag'] = base64_decode($v['tag']);
                }
                if (is_serialized($v['tag'])) {
                    $v['tag'] = @iunserializer($v['tag']);
                }
                if (!empty($v['tag']['headimgurl'])) {
                    $v['tag']['avatar'] = tomedia($v['tag']['headimgurl']);
                    unset($v['tag']['headimgurl']);
                }
            }
            if (empty($v['tag'])) {
                $v['tag'] = array();
            }
            if (!empty($user)) {
                $niemmo = $user['realname'];
                if (empty($niemmo)) {
                    $niemmo = $user['nickname'];
                }
                if (empty($niemmo)) {
                    $niemmo = $user['mobile'];
                }
                if (empty($niemmo)) {
                    $niemmo = $user['email'];
                }
                if (empty($niemmo) || (!empty($niemmo) && substr($niemmo, -6) == 'yike1908.com' && strlen($niemmo) == 39)) {
                    $niemmo_effective = 0;
                } else {
                    $niemmo_effective = 1;
                }
                $v['user'] = array('niemmo_effective' => $niemmo_effective, 'niemmo' => $niemmo, 'nickname' => $user['nickname']);
            }
            if (empty($v['user']['nickname']) && !empty($v['tag']['nickname'])) {
                $v['user']['nickname'] = $v['tag']['nickname'];
            }
            if (empty($v['user']['avatar']) && !empty($v['tag']['avatar'])) {
                $v['user']['avatar'] = $v['tag']['avatar'];
            }
            $v['account'] = $accounts[$v['acid']]['name'];

            unset($user, $niemmo, $niemmo_effective);
        }
    }
    $pager = pagination($total, $pindex, $psize);
    include $this->template('web/send/list');
} else if ($operation == 'do') {

    $uid = $_GPC['uid'];
    $activity = pdo_fetch("select * from" . tablename('yike_activity_coupon') . "where uniacid='{$_W['uniacid']}'");
    $activity_id = $activity['id'];
    $coupon_ids = explode(',', $activity['coupon_ids']);
    $template=pdo_fetch("select * from" . tablename('yike_vouchers_template_message') . "where uniacid='{$_W['uniacid']}' and type=1");
    //准备数据
    $_GPC['template_id']=$template['template_id'];

    for ($i = 0; $i < count($coupon_ids); $i++) { //生成代金券领取记录并且推送获取代金券通知
        $coupon = activity_token_grant(intval($uid), intval($coupon_ids[$i]), 'system', '管理员主动送');
        $activity_coupon=pdo_fetch("select * from" . tablename('activity_coupon') . "where couponid='{$coupon_ids[$i]}'");
        $data = array('user_id'=>$uid, 'activity_id'=>$activity_id, 'uniacid'=>$uniacid, 'create_time'=>time());
        $record = pdo_insert('yike_activity_coupon_record', $data);
        $_GPC['data']=array(
            'first'=>array('value'=>$template['first_data'],'color'=>'FF0000'),
            'keyword1'=>array('value'=>$activity_coupon['title'],'color'=>'#173177'),
            'keyword2'=>array('value'=>'无','color'=>'#173177'),
            'keyword3'=>array('value'=>date('Y-m-d', $activity_coupon['endtime']),'color'=>'#173177'),
            'remark'=>array('value'=>$template['remark_data'],'FF0000')
        );
        $result = $this->send();
    }
    $this->show_json(1, $result);
}else if ($operation == 'send'){
    $activity = pdo_fetch("select * from" . tablename('yike_activity_coupon') . "where uniacid='{$_W['uniacid']}'");
    $user = pdo_fetchall("SELECT * FROM " . tablename('mc_mapping_fans') . "where uniacid='{$_W['uniacid']}'" );
    if( $_GPC['send_type']=='use'){
        $template=pdo_fetch("select * from" . tablename('yike_vouchers_template_message') . "where uniacid='{$_W['uniacid']}' and type=3");
        for($i=0;$i < count($user);$i++){
            $uid=$user[$i]['uid'];
            $data = pdo_fetch("SELECT sum(discount) as discount FROM " . tablename('activity_coupon_record')." as r left join " . tablename('activity_coupon') . " as c on r.couponid = c.couponid" . " where r.uniacid='{$_W['uniacid']}' and r.uid=$uid and r.status=1" );
            if($data["discount"] != 0){
                $_GPC['data']=array(
                    'first'=>array('value'=>$template['first_data'],'color'=>'FF0000'),
                    'keyword1'=>array('value'=>$data["discount"].'元','color'=>'#173177'),
                    'keyword2'=>array('value'=>'无','color'=>'#173177'),
                    'keyword3'=>array('value'=>date('Y-m-d',  strtotime('7 days')).'截止','color'=>'#173177'),
                    'remark'=>array('value'=>$template['remark_data'],'FF0000')
                );
                $_GPC['openid'] = $user[$i]['openid'];
                $_GPC['template_id'] = $template['template_id'];
                $result = $this->send();
                $this->show_json(1, $result);
            }
        }
    }else if( $_GPC['send_type']=='overdue'){
        $template=pdo_fetch("select * from" . tablename('yike_vouchers_template_message') . "where uniacid='{$_W['uniacid']}' and type=2");
        for($i=0;$i < count($user);$i++){
            $uid=$user[$i]['uid'];
            $data = pdo_fetch("SELECT sum(discount) as discount FROM " . tablename('activity_coupon_record')." as r left join " . tablename('activity_coupon') . " as c on r.couponid = c.couponid" . " where r.uniacid='{$_W['uniacid']}' and r.uid=$uid and r.status=1" );
            if($data["discount"] != 0){
                $_GPC['data']=array(
                    'first'=>array('value'=>$template['first_data'],'color'=>'FF0000'),
                    'keyword1'=>array('value'=>$data["discount"].'元','color'=>'#173177'),
                    'keyword2'=>array('value'=>'无','color'=>'#173177'),
                    'keyword3'=>array('value'=>date('Y-m-d', strtotime('7 days')),'color'=>'#173177'),
                    'remark'=>array('value'=>$template['remark_data'],'FF0000')
                );
                $_GPC['openid'] = $user[$i]['openid'];
                $_GPC['template_id'] = $template['template_id'];
                $result = $this->send();
                $this->show_json(1, $result);
            }
        }
    }
}

