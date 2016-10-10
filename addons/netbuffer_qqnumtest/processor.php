<?php
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT . '/addons/netbuffer_qqnumtest/JuHeApi.class.php';
class Netbuffer_qqnumtestModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W, $_GPC;
        if (!$this->inContext) {
            $this->beginContext();
            return $this->respText('请输入要查询的qq号码:');
        } else {
search:
            $qq = trim($this->message['content']);
            if ($qq != "" && is_numeric($qq)) {
                $info = JuHeApiSdk::getQQnumInfo($qq);
                if (null != $info && is_array($info)) {
                    $this->endContext();
                    $returninfo = "QQ号码测试结论:" . $info["conclusion"] . "\r\n" . "结论分析:" . $info["analysis"];
                    return $this->respText($returninfo);
                } else {
                    goto search;
                }
            } else {
                return $this->respText("请确认您输入的QQ无误后再试哦");
            }
        }
    }
}
?><?php