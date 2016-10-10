<?php
/**
 * 微招聘模块处理程序
 *
 * @author Camaro
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Amouse_weijobModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
        $fromuser = $this->message['from'];
		$reply = pdo_fetchall("SELECT * FROM ".tablename('amouse_weijob_reply')." WHERE rid = :rid", array(':rid' => $this->rule));
		if (!empty($reply)) {
			foreach ($reply as $row) {
				$companyids[$row['companyid']] = $row['companyid'];
			}
			$company = pdo_fetchall("SELECT id, title, thumb, content FROM ".tablename('amouse_weijob_company')." WHERE id IN (".implode(',', $companyids).")", array(), 'id');
			$response = array();
			foreach ($reply as $row) {
				$row = $company[$row['companyid']];
				$response[] = array(
					'title' => $row['title'],
					'description' => $row['content'],
					'picurl' => $row['thumb'],
					'url' => $this->buildSiteUrl($this->createMobileUrl('company', array('id' => $row['id'],'wid'=>$fromuser),true)),
				);
			}
			return $this->respNews($response);
		}
	}
}