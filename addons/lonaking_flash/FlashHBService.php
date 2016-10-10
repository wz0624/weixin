<?php
require_once 'pay/FlashHongBaoHelper.php';
require_once 'pay/FlashHongBaoException.php';
class FlashHBService
{
    private $DS;
    private $SIGNTYPE = "sha1";
    private $APPID;
    private $MCHID;
    private $PASSKEY;
    private $NICK_NAME;
    private $SEND_NAME;
    private $WISHING;
    private $ACT_NAME;
    private $REMARK;
    private $apiclient_cert;
    private $apiclient_key;
    private $rootca;
    private $money;
    private $openid;
    private $client_ip;
    public function __construct($openid, $money, $config)
    {
        $this->DS             = DIRECTORY_SEPARATOR;
        $this->SIGNTYPE       = "sha1";
        $this->APPID          = $config['appid'];
        $this->MCHID          = $config['mchid'];
        $this->PASSKEY        = $config['passkey'];
        $this->NICK_NAME      = $config['nick_name'];
        $this->SEND_NAME      = $config['send_name'];
        $this->WISHING        = $config['wishing'];
        $this->ACT_NAME       = $config['act_name'];
        $this->REMARK         = $config['remark'];
        $this->apiclient_cert = $config['apiclient_cert'];
        $this->apiclient_key  = $config['apiclient_key'];
        $this->rootca         = $config['rootca'];
        $this->client_ip      = $config['client_ip'];
        $this->money          = intval($money * 100);
        $this->openid         = $openid;
    }
    public function send()
    {
        $mch_billno      = date('YmdHis') . rand(10, 9999);
        $commonUtil      = new CommonUtil();
        $wxHongBaoHelper = new FlashHongBaoHelper($this->apiclient_cert, $this->apiclient_key, $this->rootca, $this->PASSKEY);
        $wxHongBaoHelper->setParameter("nonce_str", $commonUtil->create_noncestr());
        $wxHongBaoHelper->setParameter("mch_billno", $mch_billno);
        $wxHongBaoHelper->setParameter("mch_id", $this->MCHID);
        $wxHongBaoHelper->setParameter("wxappid", $this->APPID);
        $wxHongBaoHelper->setParameter("nick_name", $this->NICK_NAME);
        $wxHongBaoHelper->setParameter("send_name", $this->SEND_NAME);
        $wxHongBaoHelper->setParameter("re_openid", $this->openid);
        $wxHongBaoHelper->setParameter("total_amount", $this->money);
        $wxHongBaoHelper->setParameter("total_num", 1);
        $wxHongBaoHelper->setParameter("wishing", $this->WISHING);
        $wxHongBaoHelper->setParameter("client_ip", empty($this->client_ip) ? "127.0.0.1" : $this->client_ip);
        $wxHongBaoHelper->setParameter("act_name", $this->ACT_NAME);
        $wxHongBaoHelper->setParameter("remark", $this->REMARK);
        $postXml     = $wxHongBaoHelper->create_hongbao_xml();
        $url         = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml);
        $responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $return_code = $responseObj->return_code;
        $result_code = $responseObj->result_code;
        if ($return_code == 'SUCCESS') {
            if ($result_code == 'SUCCESS') {
                $total_amount = $responseObj->total_amount * 1.0 / 100;
                return '红包发放成功！金额为：' . $total_amount . "元！";
            } else {
                if ($responseObj->err_code == 'NOTENOUGH') {
                    throw new FlashHongBaoException('您来迟了，红包已经发完！！！', 10401);
                } else if ($responseObj->err_code == 'TIME_LIMITED') {
                    throw new FlashHongBaoException('现在非红包发放时间，请在北京时间0:00-8:00之外的时间前来领取', 10402);
                } else if ($responseObj->err_code == 'SYSTEMERROR') {
                    throw new FlashHongBaoException('系统繁忙，请稍后再试!', 10403);
                } else if ($responseObj->err_code == 'DAY_OVER_LIMITED') {
                    throw new FlashHongBaoException('今日红包已达上限，请明日再试!', 10404);
                } else if ($responseObj->err_code == 'SECOND_OVER_LIMITED') {
                    throw new FlashHongBaoException('每分钟红包已达上限，请稍后再试!', 10405);
                }
                throw new FlashHongBaoException('红包发放失败！' . $responseObj->return_msg . "！请稍后再试！", 10406);
            }
        } else {
            if ($responseObj->err_code == 'NOTENOUGH') {
                throw new FlashHongBaoException('您来迟了，红包已经发完！！！', 10401);
            } else if ($responseObj->err_code == 'TIME_LIMITED') {
                throw new FlashHongBaoException('现在非红包发放时间，请在北京时间0:00-8:00之外的时间前来领取', 10402);
            } else if ($responseObj->err_code == 'SYSTEMERROR') {
                throw new FlashHongBaoException('系统繁忙，请稍后再试!', 10403);
            } else if ($responseObj->err_code == 'DAY_OVER_LIMITED') {
                throw new FlashHongBaoException('今日红包已达上限，请明日再试!', 10404);
            } else if ($responseObj->err_code == 'SECOND_OVER_LIMITED') {
                throw new FlashHongBaoException('每分钟红包已达上限，请稍后再试!', 10405);
            }
            throw new FlashHongBaoException('红包发放失败！' . $responseObj->return_msg . "！请稍后再试！", 10406);
        }
    }
}
?>