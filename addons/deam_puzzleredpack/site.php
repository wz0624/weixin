<?php
defined('IN_IA') or exit('Access Denied');
define('DM_ROOT', IA_ROOT . '/addons/deam_puzzleredpack');

class Deam_puzzleredpackModuleSite extends WeModuleSite
{
    public function Checkeduseragent()
    {
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
            message('非法访问，请通过微信打开！');
            die();
        }
    }
    public function doWebActset()
    {
        global $_W, $_GPC;
        $uniacid   = $_W['uniacid'];
        $nowtime   = TIMESTAMP;
        $operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM " . tablename('deam_puzzleredpack_packetset') . " WHERE id = :id AND uniacid = :uniacid", array(
                    ':id' => $id,
                    ':uniacid' => $uniacid
                ));
                if (empty($item)) {
                    message('抱歉，活动不存在或是已经删除！', '', 'error');
                }
            }
            $item['starttime'] = empty($item['starttime']) ? $nowtime : $item['starttime'];
            $item['endtime']   = empty($item['endtime']) ? $nowtime : $item['endtime'];
            if (checksubmit('submit')) {
                $date      = $_GPC['datelimit'];
                $starttime = strtotime($date['start']);
                $starttime = date('Y-m-d H:i', $starttime);
                $starttime = strtotime($starttime);
                $endtime   = strtotime($date['end']);
                $endtime   = date('Y-m-d H:i', $endtime);
                $endtime   = strtotime($endtime);
                $data      = array(
                    'uniacid' => intval($_W['uniacid']),
                    'send_name' => $_GPC['send_name'],
                    'act_name' => $_GPC['act_name'],
                    'wishing' => $_GPC['wishing'],
                    'remark' => $_GPC['remark'],
                    'total_num' => $_GPC['total_num'],
                    'total_prize' => intval($_GPC['total_prize']),
                    'pagetitle' => $_GPC['pagetitle'],
                    'puzzleimage' => $_GPC['puzzleimg'],
                    'qrcodeimage' => $_GPC['qrcodeimage'],
                    'total_prize' => number_format($_GPC['total_prize'], 2),
                    'createtime' => TIMESTAMP,
                    'status' => intval($_GPC['status']),
                    'minprize' => number_format($_GPC['singel_amountMin'], 2),
                    'maxprize' => number_format($_GPC['singel_amountMax'], 2),
                    'is_subscribe' => intval($_GPC['is_subscribe']),
                    'share_img' => $_GPC['share_img'],
                    'share_title' => $_GPC['share_title'],
                    'noshare_title' => $_GPC['noshare_title'],
                    'ads_type' => intval($_GPC['ads_type']),
                    'isshare' => intval($_GPC['isshare']),
                    'ads_color' => $_GPC['ads_color'],
                    'ads_button_color' => $_GPC['ads_button_color'],
                    'ads_text' => $_GPC['ads_text'],
                    'ads_link' => $_GPC['ads_link'],
                    'starttime' => $starttime,
                    'endtime' => $endtime,
                    'puzzlelevel' => intval($_GPC['puzzlelevel'])
                );
                if (empty($id)) {
                    pdo_insert('deam_puzzleredpack_packetset', $data);
                    $result = pdo_insertid();
                } else {
                    unset($data['createtime']);
                    $result = pdo_update("deam_puzzleredpack_packetset", $data, array(
                        "id" => $id
                    ));
                }
                if (!empty($result)) {
                    message('更新成功！', $this->createWebUrl('actset', array(
                        'op' => 'post',
                        'id' => $id
                    )), 'success');
                }
            }
        } elseif ($operation == 'display') {
            $pindex    = max(1, intval($_GPC['page']));
            $psize     = 50;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }
            $list  = pdo_fetchall("SELECT * FROM " . tablename('deam_puzzleredpack_packetset') . " WHERE uniacid = '{$_W['uniacid']}' $condition ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('deam_puzzleredpack_packetset') . " WHERE uniacid = '{$_W['uniacid']}' $condition");
            $pager = pagination($total, $pindex, $psize);
        }
        include $this->template('actset');
    }
    public function doWebPacketRecord()
    {
        global $_W, $_GPC;
        $uniacid           = $_W['uniacid'];
        $actid             = intval($_GPC['id']);
        $openid            = trim($_GPC['openid']);
        $item              = pdo_fetch("SELECT * FROM " . tablename('deam_puzzleredpack_packetset') . " WHERE id = :id AND uniacid = :uniacid", array(
            ':id' => $actid,
            ':uniacid' => $uniacid
        ));
        $firstpacket       = pdo_fetch("SELECT * FROM " . tablename('deam_puzzleredpack_record') . " WHERE actid = :actid AND uniacid = :uniacid ORDER BY `id` ASC", array(
            ':actid' => $actid,
            ':uniacid' => $uniacid
        ));
        $lastpacket        = pdo_fetch("SELECT * FROM " . tablename('deam_puzzleredpack_record') . " WHERE actid = :actid AND uniacid = :uniacid ORDER BY `id` DESC", array(
            ':actid' => $actid,
            ':uniacid' => $uniacid
        ));
        $firstpacketStatus = $this->getPacketStatus($firstpacket['mch_billno']);
        $lastpacketStatus  = $this->getPacketStatus($lastpacket['mch_billno']);
        $allCount          = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('deam_puzzleredpack_record') . " WHERE uniacid = '{$uniacid}' AND actid = '{$actid}' ");
        $allSum            = pdo_fetchcolumn('SELECT SUM(total_amount) FROM ' . tablename('deam_puzzleredpack_record') . " WHERE uniacid = '{$uniacid}' AND actid = '{$actid}' ");
        $pindex            = max(1, intval($_GPC['page']));
        $psize             = 50;
        $condition         = '';
        if (!empty($openid)) {
            $condition .= " AND openid = '" . $openid . "'";
        }
        $getPacketRecord = pdo_fetchall("SELECT * FROM " . tablename('deam_puzzleredpack_record') . " WHERE actid = '{$actid}' AND uniacid = '{$_W['uniacid']}' $condition ORDER BY `id` DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
        include $this->template('packetrecord');
    }
    public function doWebPacketRecordDel()
    {
        global $_W, $_GPC;
        $uniacid  = $_W['uniacid'];
        $actid    = intval($_GPC['actid']);
        $packetid = intval($_GPC['packetid']);
        $result   = pdo_delete('deam_puzzleredpack_record', array(
            'actid' => $actid,
            'id' => $packetid,
            'uniacid' => $uniacid
        ));
        if (!empty($result)) {
            message('删除成功', $this->createWebUrl('packetrecord', array(
                'id' => $actid
            )), 'success');
        }
    }
    public function getPacketStatus($mch_billno)
    {
        global $_W, $_GPC;
        $packetStateUrl         = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
        $configarr              = $this->module['config'];
        $mch_id                 = $configarr['mch_id'];
        $appid                  = $configarr['appid'];
        $postDate['nonce_str']  = Deam_puzzleredpackModuleSite::random_str(32);
        $postDate['mch_billno'] = $mch_billno;
        $postDate['mch_id']     = $mch_id;
        $postDate['appid']      = $appid;
        $postDate['bill_type']  = 'MCHT';
        $unSignParaStr          = $this->formatQueryParaMap($postDate, false);
        $signStr                = $unSignParaStr . "&key=" . $configarr['partnerkey'];
        $postDate['sign']       = strtoupper(md5($signStr));
        $packetStateinfo        = simplexml_load_string($this->curl_post_ssl($packetStateUrl, Deam_puzzleredpackModuleSite::arrayToXml($postDate)), 'SimpleXMLElement', LIBXML_NOCDATA);
        $packetStateinfo        = json_decode(json_encode($packetStateinfo), true);
        return $packetStateinfo;
    }
    static function random_str($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    public function checkIsSubscribe($subscribe_model)
    {
        global $_W, $_GPC;
        $openid  = $_W['openid'];
        $uniacid = $_W['uniacid'];
        load()->classs('weixin.account');
        $accObj       = WeixinAccount::create($acid);
        $access_token = $accObj->fetch_token();
        if ($subscribe_model == '2') {
            $getuserInfoUrl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
            $getuserInfo    = $this->httpcurl($getuserInfoUrl);
            $getuserInfo    = @json_decode($getuserInfo, true);
            $isSubscribe    = $getuserInfo['subscribe'];
        } else if ($subscribe_model == '1') {
            if ($_COOKIE['deam_openid']) {
                $jopenid   = $_COOKIE['deam_openid'];
                $isGuanzhu = pdo_fetch("SELECT * FROM " . tablename('deam_puzzleredpack_guanzhu') . " WHERE openid = :openid AND uniacid = :uniacid", array(
                    ':openid' => $_COOKIE['deam_openid'],
                    ':uniacid' => $uniacid
                ));
                if (!empty($isGuanzhu)) {
                    $isSubscribe = '1';
                } else {
                    $isSubscribe = '0';
                }
            } else {
                $isSubscribe = '0';
            }
        }
        return $isSubscribe;
    }
    public function formatQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if (null != $v && "null" != $v && "sign" != $k) {
                if ($urlencode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar;
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
    public function curl_post_ssl($url, $vars, $second = 30, $aHeader = array())
    {
        global $_W;
        $pemaddress = $this->getPemAddress();
        $ch         = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLCERT, DM_ROOT . '/tmpdata/cert/' . $_W['uniacid'] . '/apiclient_cert.pem');
        curl_setopt($ch, CURLOPT_SSLKEY, DM_ROOT . '/tmpdata/cert/' . $_W['uniacid'] . '/apiclient_key.pem');
        curl_setopt($ch, CURLOPT_CAINFO, DM_ROOT . '/tmpdata/cert/' . $_W['uniacid'] . '/rootca.pem');
        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
    public function httpcurl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    public function getPemAddress()
    {
        return $this->module['config'];
    }
    static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
	public function json_encode_ex($value){
		if(version_compare(PHP_VERSION,'5.4.0','<')){
			$str =json_encode($value);
			$str =preg_replace_callback("#\\\u([0-9a-f]{4})#i", function($matchs){
				return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
			}, $str );
			return $str;
		}else{
			return json_encode($value, JSON_UNESCAPED_UNICODE);
		}
	}
}