<?php
defined('IN_IA') or exit('Access Denied');
class hc_chuansongModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W;
        $rid   = $this->rule;
        $reply = pdo_fetch("select * from " . tablename('hc_chuansong_reply') . " where rid = " . $rid . " and weid = " . $_W['uniacid'] . " ");
        if ($reply == false) {
            return $this->respText('后台未设置回复');
        }
        if ($reply['istype'] == 1) {
            $item = pdo_fetch('select * from ' . tablename('hc_chuansong_list') . ' where weid=:weid AND is_default=1', array(
                ':weid' => $_W['uniacid']
            ));
            if ($item == false) {
                return $this->respText('活动不存在');
            }
            if ($item['status'] == 0) {
                return $this->respText('活动还没有开始');
            }
            if ($item['starttime'] > time()) {
                return $this->respText('活动未开始，开始时间为:' . date('Y-m-d H:i:s', $item['starttime']));
            }
            if ($item['endtime'] < time()) {
                return $this->respText('活动已结束，结束时间为:' . date('Y-m-d H:i:s', $item['endtime']));
            }
            if ($item['join_nums'] >= $item['total_nums']) {
                return $this->respText('参加人次已经达到' . $item['total_nums'] . ',请等待下次机会');
            }
            $user = pdo_fetch('select id,create_time from' . tablename('hc_chuansong_user') . ' where weid=:weid AND pid=:pid AND from_user=:from_user', array(
                ':weid' => $_W['uniacid'],
                ':from_user' => $this->message['from'],
                ':pid' => $item['id']
            ));
            if ($user != false) {
                if (((time() - $user['create_time']) < $item['part_time']) && $item['part_time'] > 0) {
                    return $this->respText('上次参与的时间是:' . date('Y-m-d H:i:s', $user['create_time']) . "\n下次参与时间为:" . date('Y-m-d H:i:s', ($user['create_time'] + $item['part_time'])));
                }
            }
            $news   = array();
            $news[] = array(
                'title' => $reply['title'],
                'description' => $reply['desc'],
                'picurl' => toimage($reply['cover']),
                'url' => $this->createMobileUrl('index', array(
                    'pid' => $item['id'],
                    't' => time(),
                    'time' => TIMESTAMP,
                    'test' => 'test'
                ))
            );
            $a      = strval(time());
            return $this->respNews($news);
        } else {
            if (empty($reply['hc_chuansongid'])) {
                return $this->respText('活动不存在或已经删除');
            }
            $item = pdo_fetch('select id,title,str1,page_title from ' . tablename('hc_chuansong_list') . ' where weid=:weid AND id=:hc_chuansongid', array(
                ':weid' => $_W['uniacid'],
                ':hc_chuansongid' => $reply['hc_chuansongid']
            ));
            if ($item == false) {
                return $this->respText('活动不存在或已经删除');
            }
            $userlist = pdo_fetchall('select id,award_no from' . tablename('hc_chuansong_user') . ' where weid=:weid AND pid=:pid AND from_user=:from_user', array(
                ':weid' => $_W['uniacid'],
                ':from_user' => $this->message['from'],
                ':pid' => $item['id']
            ));
            if ($userlist == false) {
                return $this->respText('您没有参加' . $item['str1'] . $item['title'] . '活动.');
            }
            $xuhaoArr = array();
            foreach ($userlist as $row) {
                $xuhaoArr[] = $row['award_no'];
            }
            $msg = '您参加了《' . $item['str1'] . $item['title'] . '》活动.序号是' . implode(',', $xuhaoArr) . '。请关注获奖者提示！';
            return $this->respText($msg);
        }
    }
}