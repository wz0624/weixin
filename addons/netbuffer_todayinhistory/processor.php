<?php
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT . '/addons/netbuffer_todayinhistory/ShowApi.class.php';
class Netbuffer_todayinhistoryModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W, $_GPC;
        if (!$this->inContext) {
            $this->beginContext();
            return $this->respText("请输入要查询的时间\r\n回复1查询今天\r\n回复时间格式为:1111,则代表查询历史上的11月11日\r\n输入q退出");
        } else {
            if ($this->message['content'] == "q") {
                $this->endContext();
                return $this->respText("已退出");
            }
            $date   = trim($this->message['content']);
            $result = null;
            if ($date == 1) {
                $result = ShowApiSdk::getHistoryEvent(null);
            } else {
                if (strlen($date) == 4) {
                    $result = ShowApiSdk::getHistoryEvent($date);
                } else {
                    return $this->respText("请确认您输入的时间格式正确后再试哦~");
                }
            }
            if (null != $result && is_object($result) && count($result->list) > 0) {
                $this->endContext();
                $count = $result->list > 10 ? 10 : $result->list;
                $news  = array();
                for ($i = 0; $i < $count; $i++) {
                    $temp = $result->list[$i];
                    array_push($news, array(
                        "title" => $temp->title . "\r\n时间:" . $temp->year . "-" . $temp->month . "-" . $temp->day,
                        "picurl" => $temp->img,
                        "url" => $this->module['config']['nbrouteurl']
                    ));
                }
                return $this->respNews($news);
            } else {
                return $this->respText("请确认您输入的时间格式正确后再试哦~");
            }
        }
    }
}
?><?php