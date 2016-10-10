<?php
class FlashUserService
{
    public function addUserScore($score, $openid, $log = '')
    {
        $this->updateUserScore($score, $openid, $log);
    }
    public function reduceUserScore($score, $openid, $log = '')
    {
        if ($score < 0) {
            $this->updateUserScore($score, $openid, $log);
        } else {
            $this->updateUserScore($score * -1, $openid, $log);
        }
    }
    public function updateUserScore($score, $openid, $log = '')
    {
        $this->updateUserCredit("credit1", $score, $openid, $log);
    }
    public function updateUserMoney($money, $openid, $log = '')
    {
        $this->updateUserCredit("credit2", $money, $openid, $log);
    }
    public function addUserMoney($money, $openid, $log = '')
    {
        if ($money < 0) {
            $money = $money * -1;
        }
        $this->updateUserMoney($money, $openid, $log);
    }
    public function reduceUserMoney($money, $openid, $log = '')
    {
        if ($money < 0) {
            $this->updateUserMoney($money, $openid, $log);
        } else {
            $this->updateUserMoney($money * -1, $openid, $log);
        }
    }
    private function updateUserCredit($type = "credit1", $value, $openid, $log = '')
    {
        load()->model('mc');
        $uid        = mc_openid2uid($openid);
        $log_arr    = array();
        $log_arr[0] = $uid;
        $log_arr[1] = ($log == '' ? '未记录' : $log);
        mc_credit_update($uid, $type, $value, $log_arr);
    }
    public function fetchUserScore($openid)
    {
        load()->model('mc');
        $uid     = mc_openid2uid($openid);
        $credits = mc_credit_fetch($uid, array(
            'credit1'
        ));
        return $credits['credit1'];
    }
    public function fetchUserMoney($openid)
    {
        load()->model('mc');
        $uid     = mc_openid2uid($openid);
        $credits = mc_credit_fetch($uid, array(
            'credit2'
        ));
        return $credits['credit2'];
    }
    public function fetchUserCredit($openid)
    {
        load()->model('mc');
        $uid     = mc_openid2uid($openid);
        $credits = mc_credit_fetch($uid);
        return $credits;
    }
    public function fetchFansInfo($openid)
    {
        global $_W;
        load()->model('mc');
        $user = mc_fansinfo($openid, $_W['account']['acid']);
        if (empty($user)) {
            $this->oauthFansInfo();
            return $this->fetchFansInfo($openid);
        }
        $user['credit'] = $this->fetchUserCredit($openid);
        $user['score']  = intval($user['credit']['credit1']);
        $user['money']  = $user['credit']['credit2'];
        return $user;
    }
    public function authFansInfo()
    {
        global $_W;
        load()->model('mc');
        $user = $this->fetchFansInfo($_W['openid']);
        if (is_null($user) || empty($user['tag']['nickname'])) {
            $tagUserInfo = mc_oauth_userinfo();
            $user        = $this->fetchFansInfo($_W['openid']);
            $user['tag'] = $tagUserInfo;
        }
        $user['credit'] = $this->fetchUserCredit($_W['openid']);
        $user['score']  = intval($user['credit']['credit1']);
        $user['money']  = $user['credit']['credit2'];
        return $user;
    }
    public function oauthFansInfo()
    {
        global $_W;
        load()->model('mc');
        $user = mc_oauth_userinfo();
        return $user;
    }
    public function fetchUid($openid)
    {
        load()->model('mc');
        $uid = mc_openid2uid($openid);
        return $uid;
    }
}
