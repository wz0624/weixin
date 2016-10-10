<?php


defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT . '/addons/netbuffer_qqmusiclist/ShowApi.class.php';
class Netbuffer_qqmusiclistModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W, $_GPC;
        if (!$this->inContext) {
            $this->beginContext();
            return $this->respText("请输入要查询的榜单ID\r\n3=欧美\r\n5=内地\r\n6=港台\r\n16=韩国\r\n17=日本\r\n18=民谣\r\n19=摇滚\r\n23=销量\r\n26=热歌\r\n输入q退出");
        } else {
            if ($this->message['content'] == "q") {
                $this->endContext();
                return $this->respText("已退出");
            }
            $topid = (int) trim($this->message['content']);
            if (is_integer($topid)) {
                $result = ShowApiSdk::getQQmusiclist($topid);
                if (null != $result && is_object($result) && count($result->pagebean->songlist) > 0) {
                    $this->endContext();
                    $count = $result->pagebean->songlist > 10 ? 10 : $result->pagebean->songlist;
                    $news  = array();
                    for ($i = 0; $i < $count; $i++) {
                        $temp = $result->pagebean->songlist[$i];
                        array_push($news, array(
                            "title" => "歌名:" . $temp->songname . "-歌手:" . $temp->singername . "\r\n时长:" . $temp->seconds . "秒",
                            "url" => $this->createMobileUrl("index", array(
                                "suri" => $temp->url
                            ))
                        ));
                    }
                    return $this->respNews($news);
                } else {
                    return $this->respText("请确认您正确输入榜单ID哦~");
                }
            } else {
                return $this->respText("请确认您正确输入榜单ID哦~");
            }
        }
    }
}
