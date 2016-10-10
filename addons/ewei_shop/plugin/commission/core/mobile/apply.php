<?php
global $_W, $_GPC;
$openid = m('user')->getOpenid();
if ($_W['isajax']) {
    $level                   = $this->set['level'];
    $member        = $this->model->getInfo($openid, array());
    $time                    = time();
    $day_times               = intval($this->set['settledays']) * 3600 * 24;
    $commission_ok = 0;
    $agentLevel    = $this->model->getLevel($openid);
    $commission_ok = 0;
        $orderids = array();
        if ($level >= 1) {
            $level1_orders = pdo_fetchall('select distinct o.id from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid=:agentid and o.status>=3  and og.status1=0 and og.nocommission=0 and ({$time} - o.finishtime > {$day_times}) and o.uniacid=:uniacid  group by o.id", array(
                ':uniacid' => $_W['uniacid'],
                ':agentid' => $member['id']
            ));
            foreach ($level1_orders as $o) {
                if (empty($o['id'])) {
                    continue;
                }
                $orderids[] = array(
                    'orderid' => $o['id'],
                    'level' => 1
                );
            }
        }
        if ($level >= 2) {
            if ($member['level1'] > 0) {
                $level2_orders = pdo_fetchall('select distinct o.id from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid in( " . implode(',', array_keys($member['level1_agentids'])) . ")  and o.status>=3  and og.status2=0 and og.nocommission=0 and ({$time} - o.finishtime > {$day_times}) and o.uniacid=:uniacid  group by o.id", array(
                    ':uniacid' => $_W['uniacid']
                ));
                foreach ($level2_orders as $o) {
                    if (empty($o['id'])) {
                        continue;
                    }
                    $orderids[] = array(
                        'orderid' => $o['id'],
                        'level' => 2
                    );
                }
            }
        }
        if ($level >= 3) {
            if ($member['level2'] > 0) {
                $level3_orders = pdo_fetchall('select distinct o.id from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . " where o.agentid in( " . implode(',', array_keys($member['level2_agentids'])) . ")  and o.status>=3  and  og.status3=0 and og.nocommission=0 and ({$time} - o.finishtime > {$day_times})   and o.uniacid=:uniacid  group by o.id", array(
                    ':uniacid' => $_W['uniacid']
                ));
                foreach ($level3_orders as $o) {
                    if (empty($o['id'])) {
                        continue;
                    }
                    $orderids[] = array(
                        'orderid' => $o['id'],
                        'level' => 3
                    );
            }
        }
    }
    foreach ($orderids as $o) {
        $goods = pdo_fetchall("SELECT " . "og.commission1,og.commission2,og.commission3,og.commissions," . "og.status1,og.status2,og.status3," . "og.content1,og.content2,og.content3 from " . tablename("ewei_shop_order_goods") . " og" . " left join " . tablename("ewei_shop_goods") . " g on g.id=og.goodsid  " . " where og.orderid=:orderid and og.nocommission=0 and og.uniacid = :uniacid order by og.createtime  desc ", array(
            ":uniacid" => $_W["uniacid"],
            ":orderid" => $o["orderid"]
        ));
        foreach ($goods as $g) {
            $commissions = iunserializer($g["commissions"]);
            if ($o["level"] == 1 && $g["status1"] == 0) {
                $commission1 = iunserializer($g["commission1"]);
                if (empty($commissions)) {
                    $commission_ok += isset($commission1["level" . $agentLevel["id"]]) ? $commission1["level" . $agentLevel["id"]] : $commission1["default"];
                } else {
                    $commission_ok += isset($commissions["level1"]) ? floatval($commissions["level1"]) : 0;
                }
            }
            if ($o["level"] == 2 && $g["status2"] == 0) {
                $commission2 = iunserializer($g["commission2"]);
                if (empty($commissions)) {
                    $commission_ok += isset($commission2["level" . $agentLevel["id"]]) ? $commission2["level" . $agentLevel["id"]] : $commission2["default"];
                } else {
                    $commission_ok += isset($commissions["level2"]) ? floatval($commissions["level2"]) : 0;
                }
            }
            if ($o["level"] == 3 && $g["status3"] == 0) {
                $commission3 = iunserializer($g["commission3"]);
                if (empty($commissions)) {
                    $commission_ok += isset($commission3["level" . $agentLevel["id"]]) ? $commission3["level" . $agentLevel["id"]] : $commission3["default"];
                } else {
                    $commission_ok += isset($commissions["level3"]) ? floatval($commissions["level3"]) : 0;
                }
            }
        }
    }
    $cansettle               = $commission_ok >= floatval($this->set["withdraw"]);
    $member["commission_ok"] = number_format($commission_ok, 2);
    if ($_W["ispost"]) {
        $time = time();
        foreach ($orderids as $o) {
            pdo_update('ewei_shop_order_goods', array(
                'status' . $o['level'] => 1,
                'applytime' . $o['level'] => $time
            ), array(
                'orderid' => $o['orderid'],
                'uniacid' => $_W['uniacid']
            ));
        }
        $applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');
        $apply   = array(
            'uniacid' => $_W['uniacid'],
            'applyno' => $applyno,
            'orderids' => iserializer($orderids),
            'mid' => $member['id'],
            'commission' => $commission_ok,
            'type' => intval($_GPC['type']),
            'status' => 1,
            'applytime' => $time
        );
        pdo_insert('ewei_shop_commission_apply', $apply);
        $returnurl = urlencode($this->createMobileUrl('member/withdraw'));
        $infourl   = $this->createMobileUrl('member/info', array(
            'returnurl' => $returnurl
        ));
        $this->model->sendMessage($openid, array(
            'commission' => $commission_ok,
            'type' => $apply['type'] == 1 ? '微信' : '余额'
        ), TM_COMMISSION_APPLY);
        show_json(1, '已提交,请等待审核!');
    }
    $returnurl = urlencode($this->createPluginMobileUrl('commission/apply'));
    $infourl   = $this->createMobileUrl('member/info', array(
        'returnurl' => $returnurl
    ));
    show_json(1, array(
        'commission_ok' => $member['commission_ok'],
        'cansettle' => $cansettle,
        'member' => $member,
        'set' => $this->set,
        'infourl' => $infourl,
        'noinfo' => empty($member['realname'])
    ));
}
include $this->template('apply');