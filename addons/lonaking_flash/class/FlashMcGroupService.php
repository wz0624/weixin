<?php
require_once dirname(__FILE__) . '/../FlashCommonService.php';
class FlashMcGroupService extends FlashCommonService
{
    public function __construct()
    {
        $this->table_name  = "mc_groups";
        $this->columns     = "groupid,uniacid,title,orderlist,isdefault,credit";
        $this->plugin_name = "lonaking_flash";
    }
    public function selectByIds($groupIds)
    {
        if (!is_array($groupIds)) {
            throw new Exception('查询参数异常', 404);
        }
        if (sizeof($groupIds) <= 0) {
            throw new Exception('参数为空', 404);
        }
        $ids       = array_unique($groupIds);
        $idsStr    = implode(",", $groupIds);
        $in        = "(" . $idsStr . ")";
        $data_list = pdo_fetchall("SELECT " . $this->columns . " FROM " . tablename($this->table_name) . " WHERE groupid in {$in}");
        return $data_list;
    }
    public function getUserMcGroupByUid($uid)
    {
        $member  = pdo_fetch("select * from " . tablename('mc_members') . " where uid='{$uid}'");
        $groupID = $member['groupid'];
        $data    = pdo_fetch("SELECT " . $this->columns . " FROM " . tablename($this->table_name) . " WHERE groupid='{$groupID}'");
        return $data;
    }
}
