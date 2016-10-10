<?php


defined('IN_IA') or exit('Access Denied');
class Bm_inbarkModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        $content = $this->message['content'];
        global $_W;
        load()->func('compat.biz');
        $rid   = $this->rule;
        $sql   = "SELECT * FROM " . tablename('bm_inbark_reply') . " WHERE `rid`=:rid LIMIT 1";
        $reply = pdo_fetch($sql, array(
            ':rid' => $rid
        ));
        if (empty($reply['id'])) {
            return $this->respText("系统升级中，请稍候！");
        }
        $url                      = $_W['siteroot'] . 'app/' . $this->createMobileUrl('show', array(
            'rid' => $rid,
            'fromuser' => $this->message['from']
        ));
        $response['FromUserName'] = $this->message['to'];
        $response['ToUserName']   = $this->message['from'];
        $response['MsgType']      = 'news';
        $response['ArticleCount'] = 1;
        $response['Articles']     = array();
        $response['Articles'][]   = array(
            'Title' => $reply['title'],
            'Description' => $reply['desc'],
            'PicUrl' => !strexists($reply['picurl'], 'http://') ? $_W['attachurl'] . $reply['picurl'] : $reply['picurl'],
            'Url' => $url,
            'TagName' => 'item'
        );
        return $response;
    }
}