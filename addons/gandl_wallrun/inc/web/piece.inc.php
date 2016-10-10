<?php
global $_W, $_GPC;
$id = $_GPC['id'];
if(empty($id)){
    returnError("请选择要操作的内容");
}
$piece = pdo_fetch("select * from " . tablename('gandl_wall_piece') . " where uniacid=" . $_W['uniacid'] . " and id=" . $id);
if(empty($piece)){
    returnError("内容不存在");
}
$cmd = $_GPC['cmd'];
if($cmd == 'rob'){
    if($_GPC['submit'] == 'save'){
        $total_num = intval($_GPC['total_num']);
        if($total_num <= 0){
            returnError("请填写要抢的份数");
        }
        $list = pdo_fetchall("select R.uid from " . tablename('gandl_wallrun_robot') . " R  WHERE R.uniacid=:uniacid AND R.uid NOT IN (SELECT user_id FROM " . tablename('gandl_wall_rob') . " B WHERE B.uniacid=:uniacid AND B.wall_id=:wall_id AND B.piece_id=:piece_id ) ORDER BY RAND() LIMIT 0," . $total_num, array('uniacid' => $_W['uniacid'], 'wall_id' => $piece['wall_id'], 'piece_id' => $piece['id']), 'uid');
        if(empty($list) || count($list) == 0){
            returnError('目前没有可用的机器人');
        }
        if(count($list) < $total_num){
            returnError('符合条件的机器人数量不足，只有' . count($list) . '个可用');
        }
        $uids = array_keys($list);
        $robots = pdo_fetchall("SELECT * FROM " . tablename('gandl_wall_user') . " WHERE uniacid=:uniacid AND wall_id=:wall_id AND user_id IN(" . implode(',', $uids) . ") ", array('uniacid' => $_W['uniacid'], 'wall_id' => $piece['wall_id']), 'user_id');
        $num = 0;
        $msg = '';
        for($i = 0;$i < count($uids);$i++){
            $uid = $uids[$i];
            $robot = $robots[$uid];
            if(empty($robot)){
                $robot = array();
                $robot['uniacid'] = $_W['uniacid'];
                $robot['wall_id'] = $piece['wall_id'];
                $robot['user_id'] = $uid;
                $robot['followed'] = 0;
                $robot['money'] = 0;
                $robot['money_in'] = 0;
                $robot['money_out'] = 0;
                $robot['send_times'] = 0;
                $robot['send_total'] = 0;
                $robot['rob_times'] = 0;
                $robot['rob_total'] = 0;
                $robot['rob_luck'] = 0;
                $robot['create_time'] = time();
                pdo_insert('gandl_wall_user', $robot);
                $robot['id'] = pdo_insertid();
                if(empty($robot['id'])){
                    $msg = '自动创建机器人信息失败';
                    continue;
                }
            }
            $_piece = pdo_fetch("select id,wall_id,total_num,rob_users,rob_plan,total_amount,status from " . tablename('gandl_wall_piece') . " where uniacid=" . $_W['uniacid'] . " and id=" . $piece['id']);
            if($_piece['rob_users'] >= $_piece['total_num']){
                $msg = '抢完了';
                break;
            }
            if($_piece['status'] != 1){
                $msg = '抢结束了';
                break;
            }
            $rob_index = $_piece['rob_users'] + 1;
            $rob_plan = explode(',', $_piece['rob_plan']);
            $rob_money = $rob_plan[$rob_index-1];
            if(empty($rob_money) || $rob_money <= 0 || $rob_money > $_piece['total_amount']){
                continue;
            }
            if($rob_index >= $_piece['total_num']){
                $ret1 = pdo_query('UPDATE ' . tablename('gandl_wall_piece') . ' SET rob_amount=rob_amount+:rob_money,rob_users=rob_users+1,rob_end_time=:rob_end_time,status=2 where id=:piece_id and rob_users=:rob_users', array(':piece_id' => $_piece['id'], ':rob_users' => $_piece['rob_users'], ':rob_money' => $rob_money, ':rob_end_time' => time()));
                if(false === $ret1 || 0 == $ret1){
                    continue;
                }
            }else{
                $ret1 = pdo_query('UPDATE ' . tablename('gandl_wall_piece') . ' SET rob_amount=rob_amount+:rob_money,rob_users=rob_users+1 where id=:piece_id and rob_users=:rob_users', array(':piece_id' => $_piece['id'], ':rob_users' => $_piece['rob_users'], ':rob_money' => $rob_money));
                if(false === $ret1 || 0 == $ret1){
                    continue;
                }
            }
            $rob_next_time = 0;
            $ret2 = pdo_query('UPDATE ' . tablename('gandl_wall_user') . ' SET money=money+:rob_money,rob_times=rob_times+1,rob_total=rob_total+:rob_money,rob_last_time=:rob_last_time,rob_next_time=:rob_next_time,rob_luck=rob_luck+:rob_money where uniacid=:uniacid and wall_id=:wall_id and user_id=:user_id and id=:id', array(':uniacid' => $_W['uniacid'], ':wall_id' => $_piece['wall_id'], ':user_id' => $robot['user_id'], ':id' => $robot['id'], ':rob_money' => $rob_money, ':rob_last_time' => time(), ':rob_next_time' => $rob_next_time));
            if(false === $ret2){
                continue;
            }
            $num++;
            pdo_insert('gandl_wall_rob', array('uniacid' => $_W['uniacid'], 'wall_id' => $_piece['wall_id'], 'piece_id' => $_piece['id'], 'user_id' => $robot['user_id'], 'money' => $rob_money, 'is_luck' => 0, 'is_shit' => 0, 'create_time' => time()));
        }
        if($num == 0){
            returnError('没有抢到，原因：' . $msg);
        }
        if($num == $total_num){
            returnSuccess('已成功抢到' . $num . '份');
        }else{
            returnError('抢到' . $num . '份，失败' . ($total_num - $num) . '份，原因：' . $msg);
        }
    }else{
        include $this -> template('web/piece_rob');
    }
}else if($cmd == 'views'){
    if($_GPC['submit'] == 'save'){
        $views = intval($_GPC['views']);
        $links = intval($_GPC['links']);
        if($views <= 0 && $links <= 0){
            returnError("请填写要增加的人气数");
        }
        pdo_query('UPDATE ' . tablename('gandl_wall_piece') . ' SET views=views+:views,links=links+:links where uniacid=:uniacid and id=:id', array(':uniacid' => $_W['uniacid'], ':id' => $piece['id'], 'views' => $views, 'links' => $links));
        returnSuccess('操作成功');
    }else{
        include $this -> template('web/piece_views');
    }
}
