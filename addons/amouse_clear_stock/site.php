<?php
error_reporting(0);
defined('IN_IA') or exit('Access Denied');
define('AMOUSE_CLEAR_STOCK', 'amouse_clear_stock');
define('AMOUSE_CLEAR_STOCK_RES', '../addons/' . AMOUSE_CLEAR_STOCK . '/style/');
class Amouse_Clear_StockModuleSite extends WeModuleSite
{
    public function doMobileAjaxAllData()
    {
        global $_W, $_GPC;
        $weid    = $_W['uniacid'];
        $pindex  = max(1, intval($_GPC['pageIndex']));
        $psize   = 15;
        $start   = ($pindex - 1) * $psize;
        $pid     = $_GPC['pid'];
        $list    = "";
        $contain = " WHERE uniacid =$weid ";
        $cset    = pdo_fetch("SELECT is_status FROM " . tablename('amouse_board_clear_sysset') . " where uniacid=$weid limit 1 ");
        if ($cset && $cset['is_status'] == 0) {
            $contain .= " AND status=1 ";
        }
        if ($pid == 0) {
            $list  = pdo_fetchall("SELECT * FROM " . tablename('amouse_board_clear_stock_goods') . " $contain ORDER BY uptime DESC, shuaxin DESC,createtime DESC limit $start,$psize");
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('amouse_board_clear_stock_goods') . $contain);
        } else {
            $list  = pdo_fetchall("SELECT * FROM " . tablename('amouse_board_clear_stock_goods') . " $contain AND pcateid=$pid ORDER BY uptime DESC,shuaxin DESC,id desc,createtime DESC limit
    $start,$psize");
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('amouse_board_clear_stock_goods') . " $contain AND pcateid=$pid ");
        }
        $tpage = ceil($total / $psize);
        if (count($list) > 0) {
            $ms = array();
            foreach ($list as $cid => $card) {
                if (strexists($card['logo'], 'http://') || strexists($card['logo'], 'https://')) {
                    $logo = $card['logo'];
                } else {
                    $logo = tomedia($card['logo']);
                }
                $pcateid              = $card['pcateid'];
                $card['viewcount']    = empty($card['viewcount']) ? 0 : $card['viewcount'];
                $card['product_name'] = $card['title'];
                $card['logo']         = $logo;
                $card['createtime']   = $this->get_timelineauction($card['createtime']);
                $ms[]                 = $card;
            }
            unset($card);
            $arr = array(
                'status' => 1,
                'gtotal' => $tpage + 1,
                'html' => $ms
            );
        } else {
            $arr = array(
                'status' => 0
            );
        }
        echo json_encode($arr);
    }
    public function doMobileAjaxStock()
    {
        global $_W, $_GPC;
        $weid    = $_W['uniacid'];
        $res     = array();
        $stockid = $_GPC['stockid'];
        $openid  = $_GPC['amouse_board_openid' . $weid];
        if (empty($openid)) {
            $openid = $_W['fans']['from_user'];
        }
        $data = array(
            'title' => $_GPC['product_name'],
            'uniacid' => $weid,
            'logo' => $_GPC['logo'],
            'thumb1' => $_GPC['thumb1'],
            'thumb2' => $_GPC['thumb2'],
            'thumb3' => $_GPC['thumb3'],
            'price' => $_GPC['price'],
            'detail' => $_GPC['detail'],
            'pcateid' => $_GPC['pcateid'],
            'shuaxin' => 0,
            'openid' => $openid,
            'createtime' => time(),
            'clear_price' => $_GPC['clear_price']
        );
        $cset = pdo_fetch("SELECT edit_credit,is_status FROM " . tablename('amouse_board_clear_sysset') . " where uniacid=$weid limit 1 ");
        if ($cset && $cset['is_status'] == 1) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        if (empty($stockid)) {
            $data['createtime'] = TIMESTAMP;
            $data['uptime']     = TIMESTAMP;
            if (pdo_insert('amouse_board_clear_stock_goods', $data)) {
                $res['code'] = 200;
                if ($cset['is_status'] == 1) {
                    $msg = "发布成功";
                } else {
                    $msg = "发布成功请等待管理员审核";
                }
                $res['msg'] = $msg;
                return json_encode($res);
            } else {
                $res['code'] = 0;
                $res['msg']  = "发布失败";
                return json_encode($res);
            }
        } else {
            $credit1     = $this->getCredit($openid, 'credit1');
            $edit_credit = $credit1 - $cset['edit_credit'];
            if ($credit1 >= $cset['edit_credit'] && $edit_credit >= 0) {
                $this->setCredit($openid, 'credit1', $cset['edit_credit'], 0, array(
                    0,
                    $openid . $_W['account']['name'] . '编辑货源积分-' . $cset['edit_credit']
                ));
                pdo_update('amouse_board_clear_stock_goods', $data, array(
                    'id' => $stockid
                ));
                $res['code'] = 200;
                if ($cset['is_status'] == 1) {
                    $msg = "更新成功";
                } else {
                    $msg = "更新成功,请等待管理员审核";
                }
                $res['msg'] = $msg;
            } else {
                $res['code'] = 0;
                $res['msg']  = "积分不够，赶紧去赚取积分吧！";
            }
            return json_encode($res);
        }
    }
    public function doMobileAjaxRefer()
    {
        global $_W, $_GPC;
        $weid        = $_W['uniacid'];
        $res         = array();
        $res['code'] = 201;
        $res['msg']  = '';
        $openid      = $_GPC['amouse_board_openid' . $weid];
        if (empty($openid)) {
            $openid = $_W['fans']['from_user'];
        }
        $id   = $_GPC['pk'];
        $g    = pdo_fetch('select * from ' . tablename('amouse_board_clear_stock_goods') . ' where uniacid=:uniacid AND id=:id ', array(
            ':uniacid' => $weid,
            ':id' => $id
        ));
        $cset = pdo_fetch("SELECT refer_credit FROM " . tablename('amouse_board_clear_sysset') . " where uniacid=$weid limit 1 ");
        if (empty($g)) {
            $res['code'] = 502;
            $res['msg']  = '您要刷新置顶的货源不存在!';
            die(json_encode($res));
        }
        $credit1 = $this->getCredit($openid, 'credit1');
        $top     = $credit1 - $cset['refer_credit'];
        if ($credit1 >= $cset['refer_credit'] && $top >= 0) {
            pdo_update('amouse_board_clear_stock_goods', array(
                'shuaxin' => 1
            ), array(
                'shuaxin' => 2
            ));
            pdo_update('amouse_board_clear_stock_goods', array(
                'shuaxin' => 2
            ), array(
                'shuaxin' => 3
            ));
            pdo_update('amouse_board_clear_stock_goods', array(
                'shuaxin' => 3,
                'uptime' => time(),
                'createtime' => time()
            ), array(
                'id' => $id
            ));
            $this->setCredit($openid, 'credit1', $cset['refer_credit'], 0, array(
                0,
                $openid . $_W['account']['name'] . '置顶货源积分-' . $cset['refer_credit']
            ));
            $res['code'] = 200;
        } else {
            $res['code'] = 0;
            $res['msg']  = "积分不够，赶紧去赚取积分吧！";
        }
        return json_encode($res);
    }
    public function doMobileAjaxDel()
    {
        global $_W, $_GPC;
        $weid        = $_W['uniacid'];
        $res         = array();
        $res['code'] = 201;
        $res['msg']  = '';
        $openid      = $_GPC['amouse_board_openid' . $weid];
        if (empty($openid)) {
            $openid = $_W['fans']['from_user'];
        }
        $id   = $_GPC['pk'];
        $g    = pdo_fetch('select * from ' . tablename('amouse_board_clear_stock_goods') . ' where uniacid=:uniacid AND id=:id ', array(
            ':uniacid' => $weid,
            ':id' => $id
        ));
        $cset = pdo_fetch("SELECT del_credit FROM " . tablename('amouse_board_clear_sysset') . " where uniacid=$weid limit 1 ");
        if (empty($g)) {
            $res['code'] = 502;
            $res['msg']  = '您要刷新置顶的货源不存在!';
            die(json_encode($res));
        }
        $credit1    = $this->getCredit($openid, 'credit1');
        $del_credit = $credit1 - $cset['del_credit'];
        if ($credit1 >= $cset['del_credit'] && $del_credit >= 0) {
            pdo_delete('amouse_board_clear_stock_goods', array(
                'id' => $g['id']
            ));
            $this->setCredit($openid, 'credit1', $cset['del_credit'], 0, array(
                0,
                $openid . $_W['account']['name'] . '删除货源积分-' . $cset['del_credit']
            ));
            $res['code'] = 200;
            $res['msg']  = "删除成功！";
        } else {
            $res['code'] = 0;
            $res['msg']  = "积分不够，赶紧去赚取积分吧！";
        }
        return json_encode($res);
    }
    private function setCredit($openid = '', $credittype = 'credit1', $credits = 0, $isadd = 0, $log = array())
    {
        global $_W;
        load()->model('mc');
        $uid = mc_openid2uid($openid);
        if (!empty($uid)) {
            $value = pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(
                ':uid' => $uid
            ));
            if ($isadd == 0) {
                $newcredit = $value - $credits;
            } else {
                $newcredit = $value + $credits;
            }
            if ($newcredit <= 0) {
                $newcredit = 0;
            }
            pdo_update('mc_members', array(
                $credittype => $newcredit
            ), array(
                'uid' => $uid
            ));
            if (empty($log) || !is_array($log)) {
                $log = array(
                    $uid,
                    '未记录'
                );
            }
            $data = array(
                'uid' => $uid,
                'credittype' => $credittype,
                'uniacid' => $_W['uniacid'],
                'num' => $credits,
                'module' => 'amouse_hufen',
                'createtime' => TIMESTAMP,
                'operator' => intval($log[0]),
                'remark' => $log[1]
            );
            pdo_insert('mc_credits_record', $data);
        } else {
            $value = pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('amouse_board_member') . " WHERE  weid=:uniacid and openid=:openid limit 1", array(
                ':uniacid' => $_W['uniacid'],
                ':openid' => $openid
            ));
            if ($isadd == 0) {
                $newcredit = $value - $credits;
            } else {
                $newcredit = $value + $credits;
            }
            if ($newcredit <= 0) {
                $newcredit = 0;
            }
            pdo_update('amouse_board_member', array(
                $credittype => $newcredit
            ), array(
                'weid' => $_W['uniacid'],
                'openid' => $openid
            ));
        }
    }
    private function getCredit($openid = '', $credittype = 'credit1')
    {
        global $_W;
        load()->model('mc');
        $uid = mc_openid2uid($openid);
        if (!empty($uid)) {
            return pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('mc_members') . " WHERE `uid` = :uid", array(
                ':uid' => $uid
            ));
        } else {
            return pdo_fetchcolumn("SELECT {$credittype} FROM " . tablename('amouse_board_member') . " WHERE openid=:openid and weid=:uniacid limit 1", array(
                ':openid' => $openid,
                ':uniacid' => $_W['uniacid']
            ));
        }
    }
    private function get_timelineauction($pubtime)
    {
        $time    = time();
        $seconds = $time - $pubtime;
        $days    = idate('z', $time) - idate('z', $pubtime);
        if ($days == 0) {
            if ($seconds < 3600) {
                if ($seconds < 60) {
                    if (3 > $seconds) {
                        return '刚刚';
                    } else {
                        return $seconds . '秒前';
                    }
                }
                return intval($seconds / 60) . '分钟前';
            }
            return idate('H', $time) - idate('H', $pubtime) . '小时前';
        }
        if ($days == 1) {
            return '昨天 ' . date('H:i', $pubtime);
        }
        if ($days == 2) {
            return '前天 ' . date('H:i', $pubtime);
        }
        if ($days <= 7 && $days > 0) {
            return $days . '天前';
        }
        return date('Y-m-d', $pubtime);
    }
    public function getSysset($weid = 0)
    {
        return pdo_fetch("SELECT * FROM " . tablename('amouse_board_sysset') . " WHERE weid=:weid limit 1", array(
            ':weid' => $weid
        ));
    }
}