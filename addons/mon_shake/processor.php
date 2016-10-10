<?php

defined('IN_IA') or exit('Access Denied');
define("MON_SHAKE", "mon_shake");
require_once IA_ROOT . "/addons/" . MON_SHAKE . "/dbutil.class.php";
require_once IA_ROOT . "/addons/" . MON_SHAKE . "/monUtil.class.php";
require_once IA_ROOT . "/addons/" . MON_SHAKE . "/value.class.php";

class Mon_ShakeModuleProcessor extends WeModuleProcessor
{
	public function respond()
	{
		$rid = $this->rule;


		$shake = pdo_fetch("select * from " . tablename(DBUtil::$TABLE_SHAKE) . " where rid=:rid", array(":rid" => $rid));

		if (!empty($shake)) {
			if (TIMESTAMP < $shake['starttime']) {
				return $this->respText("摇一摇动还未开始!");
			}
			$news = array();
			$news [] = array('title' => $shake['new_title'], 'description' => $shake['new_content'], 'picurl' => MonUtil::getpicurl($shake ['new_icon']), 'url' => $this->createMobileUrl('Index', array('sid' => $shake['id'])));
			return $this->respNews($news);
		} else {
			return $this->respText("摇一摇活动不存在");
		}

		return null;


	}


}
