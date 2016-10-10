<?php
defined('IN_IA') or exit('Access Denied');
require_once IA_ROOT . '/addons/netbuffer_domainsearch/ShowApi.class.php';
class Netbuffer_domainsearchModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W, $_GPC;
        if (!$this->inContext) {
            $this->beginContext();
            return $this->respText('请输入要查询的域名:\r\n输入q退出');
        } else {
            if ($this->message['content'] == "q") {
                $this->endContext();
                return $this->respText("已退出");
            }
            $domain = trim($this->message['content']);
            if ($domain != "" && strpos($domain, ".") != false && ShowApiSdk::endWith($domain, ".") == false) {
search:
                $info = ShowApiSdk::getDomainInfo($domain);
                if (isset($info) && is_object($info)) {
                    $this->endContext();
                    $returninfo = "主办单位名称:" . $info->com_name . "\r\n" . "主办单位性质:" . $info->type . "\r\n" . "网站备案/许可证号:" . $info->num . "\r\n" . "网站名称:" . $info->sys_name . "\r\n" . "网站首页网址:" . $info->domain . "\r\n" . "审核时间:" . $info->update_time;
                    return $this->respText($returninfo);
                } else {
                    goto search;
                }
            } else {
                return $this->respText("请确认您输入的域名无误后再试哦");
            }
        }
    }
}
?><?php