<?php


defined('IN_IA') or exit('Access Denied');
class bm_weizhangModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        global $_W;
        $key  = $_W['account']['modules']['bm_weizhang']['config']['kkk'];
        $city = $_W['account']['modules']['yobyweizahng']['config']['city'];
        return $this->respText("<a href=mobile.php?act=module&weid=" . $_W['weid'] . "&name=bm_weizhang&do=detail'>违章查询</a>");
    }
}