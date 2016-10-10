<?php
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT . '/addons/netbuffer_musicsearch/ShowApi.class.php';
class Netbuffer_musicsearchModuleProcessor extends WeModuleProcessor
{
    static $errtip = "请确认您的输入正确哦~";
    static $tip = "请输入要查询的关键词\r\n可以输入歌手或歌名\r\n输入q退出";
    public function respond()
    {
        global $_W, $_GPC;
        if (!$this->inContext) {
            $this->beginContext();
            return $this->respText(Netbuffer_musicsearchModuleProcessor::$tip);
        } else {
            if ($this->message['content'] == "q") {
                $this->endContext();
                return $this->respText("已退出");
            }
            $keyword = trim($this->message['content']);
            if (strlen($keyword) >= 1) {
                $result = ShowApiSdk::getMusicByKeyWord($keyword);
                if (null != $result && is_object($result) && count($result->pagebean->contentlist) > 0) {
                    $this->endContext();
                    $count = $result->pagebean->contentlist > 10 ? 10 : $result->pagebean->contentlist;
                    $news  = array();
                    for ($i = 0; $i < $count; $i++) {
                        if ($i > 9) {
                            break;
                        }
                        $temp = $result->pagebean->contentlist[$i];
                        if ($temp->songname == "" && $temp->songname == "" && $temp->albumname) {
                            continue;
                        }
                        array_push($news, array(
                            "picurl" => $temp->albumpic_big,
                            "title" => (empty($temp->songname) || "" == $temp->songname ? "" : "歌名:" . $temp->songname) . (empty($temp->singername) || "" == $temp->singername ? "" : " 歌手:" . $temp->singername) . "\r\n专辑:" . $temp->albumname,
                            "url" => $this->createMobileUrl("index", array(
                                "suri" => $temp->m4a
                            ))
                        ));
                    }
                    return $this->respNews($news);
                } else {
                    return $this->respText(Netbuffer_musicsearchModuleProcessor::$errtip);
                }
            } else {
                return $this->respText(Netbuffer_musicsearchModuleProcessor::$errtip);
            }
        }
    }

}
?>