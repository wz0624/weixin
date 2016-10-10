<?php
/**
 * 捷讯求缘分模块处理程序
 *
 * @author 捷讯设计
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class J_shakeluckyModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
		load()->app('common');
		$rid = $this->rule;
		$sql = "SELECT * FROM " . tablename('j_shakelucky_reply') . " WHERE `rid`=:rid LIMIT 1";
		$row = pdo_fetch($sql, array(':rid' => $rid));
		if (empty($row['id'])) {
			return array();
		}
		$title = pdo_fetchcolumn("SELECT name FROM ".tablename('rule')." WHERE id = :rid LIMIT 1", array(':rid' => $rid));
		return $this->respNews(array(
			'Title' => $title,
			'Description' => $row['description'],
			'PicUrl' => $_W['attachurl'] . $row['picture'],
			'Url' => "index.php?i=".$_W['uniacid']."&c=entry&m=j_shakelucky&do=enter&id=".$rid."&wxref=mp.weixin.qq.com#wechat_redirect",
		));
	}
}