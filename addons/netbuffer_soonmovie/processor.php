<?php
@eval('//www.phpjiami.com 专属VIP会员加密! ');
?><?php
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT . '/addons/netbuffer_latestmovie/JuHeApi.class.php';
class Netbuffer_soonmovieModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W, $_GPC;
        if (!$this->inContext) {
            $this->beginContext();
            return $this->respText('请输入要查询的城市名:');
        } else {
            $city = trim($this->message['content']);
            if (strlen($city) > 0) {
                $result = JuHeApiSdk::getLatestMovie($city);
                if (null != $result && is_array($result) && count($result) > 0) {
                    $this->endContext();
                    $count = count($result["data"][1]["data"]) > 10 ? 10 : count($result["data"][1]["data"]);
                    $news  = array();
                    for ($i = 0; $i < $count; $i++) {
                        $temp = $result["data"][1]["data"][$i];
                        array_push($news, array(
                            "title" => "影片名:" . $temp["tvTitle"] . "\r\n上映日期:" . $temp["playDate"]["data"] . "\r\n剧情:" . $temp["story"]["data"]["storyBrief"],
                            "picurl" => $temp["iconaddress"],
                            "url" => $temp["m_iconlinkUrl"]
                        ));
                    }
                    return $this->respNews($news);
                } else {
                    return $this->respText("请输入正确的城市名后再试哦~");
                }
            } else {
                return $this->respText("请输入正确的城市名后再试哦~");
            }
        }
    }
}
?><?php