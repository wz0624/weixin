<?php
class money
{
    public $uid;
    public $fanid;
    public $openid;
    public $student_id;
    public $do_action;
    public $uniacid;
    public $money_limit = array();
    public $money_record_last = array();
    public $table_pe;
    public function __construct($do_action, $table_pe)
    {
        global $_W, $_GPC;
        $this->table_pe  = $table_pe;
        $this->do_action = lcfirst($do_action);
        $this->uid       = $_W['member']['uid'];
        $this->openid    = $_W['openid'];
        $this->uniacid   = $_W['uniacid'];
        $student_id      = $this->uid_to_studentId();
        if ($student_id)
            $this->student_id = $student_id;
    }
    public function uid_to_studentId()
    {
        $uid         = $this->uid;
        $fanid       = pdo_fetchcolumn("select fanid from " . tablename('mc_mapping_fans') . " where uid={$this->uid} ");
        $this->fanid = $fanid;
        $student_id  = pdo_fetchcolumn("select student_id from " . $this->table_pe . "lianhu_student where uniacid=:uniacid and school_id=:sid and (fanid=:fanid or fanid1=:fanid1 or fanid2=:fanid2) ", array(
            ':uniacid' => $this->uniacid,
            ':sid' => $_SESSION['school_id'],
            ':fanid' => $fanid,
            ':fanid1' => $fanid1,
            ':fanid2' => $fanid2
        ));
        return $student_id;
    }
    public function get_money_limit()
    {
        $result            = pdo_fetch("select * from " . $this->table_pe . "lianhu_money_limit where uniacid=:uniacid and school_id=:sid and limit_module=:limit_module and  status=1 order by addtime desc  ", array(
            ':uniacid' => $this->uniacid,
            ':sid' => $_SESSION['school_id'],
            ':limit_module' => $this->do_action
        ));
        $this->money_limit = $result;
    }
    public function last_money_record()
    {
        $limit_id                = $this->money_limit['limit_id'];
        $result                  = pdo_fetch("select * from " . $this->table_pe . "lianhu_money_record where limit_id=:lid and status=1 and school_id=:sid and uid=:uid", array(
            ':lid' => $limit_id,
            ':sid' => $_SESSION['school_id'],
            ':uid' => $this->uid
        ));
        $this->money_record_last = $result;
    }
    public function money_judge()
    {
        $this->get_money_limit();
        if (empty($this->money_limit))
            return true;
        $this->last_money_record();
        if ($this->money_limit['limit_type'] == 1 && $this->money_record_last)
            return true;
        if ($this->money_limit['limit_type'] == 2) {
            $next_need_time = $this->money_record_last['addtime'] + 3600 * 24 * 365;
            if ($next_need_time >= TIMESTAMP)
                return true;
        }
        if ($this->money_limit['limit_type'] == 3) {
            $next_need_time = $this->money_record_last['addtime'] + 3600 * 24 * 31;
            if ($next_need_time >= TIMESTAMP)
                return true;
        }
        return false;
    }
    public function money_to_order()
    {
        $this->get_money_limit();
        if ($this->money_limit) {
            $in['uniacid']    = $this->uniacid;
            $in['school_id']  = $_SESSION['school_id'];
            $in['limit_id']   = $this->money_limit['limit_id'];
            $in['limit_much'] = $this->money_limit['limit_much'];
            $in['student_id'] = $this->student_id;
            $in['fan_id']     = $this->fanid;
            $in['addtime']    = TIMESTAMP;
            $in['status']     = 0;
            pdo_insert('lianhu_money_record', $in);
            $insert_id = pdo_insertid();
            $params    = array(
                'tid' => $insert_id,
                'ordersn' => "MMD" . $insert_id,
                'title' => $this->money_limit['limit_name'],
                'fee' => $this->money_limit['limit_much'],
                'user' => $this->uid
            );
            return $params;
        } else {
            return false;
        }
    }
}