<?php
/**
 * 美图秀吧模块定义
 *
 */
defined('IN_IA') or exit('Access Denied');

class Yuexiage_communityModule extends WeModule {


	public function settingsDisplay($settings) {
        global $_GPC, $_W;

        if (checksubmit()) {
            $cfg = array(
                'communityname' => $_GPC['communityname'],
                'officialweb' => $_GPC['officialweb'],
                'phone' => $_GPC['phone'],
                'description'=>  htmlspecialchars_decode($_GPC['description']),
                'filter' => $_GPC['filter'],
                'comment' => $_GPC['comment'],
				'credit'  => $_GPC['credit'],
				'share_credit' => $_GPC['share_credit'],
				'chick_credit' => $_GPC['chick_credit'],
                'limit_credit' => $_GPC['limit_credit'],
                'hits_credit'  => $_GPC['hits_credit'],
                'comm_credit'  => $_GPC['comm_credit'],
                'appid'  => $_GPC['appid'],
                'appsecret'  => $_GPC['appsecret'],
                'shared_credit'  => $_GPC['shared_credit'],
                'admin'  => $_GPC['admin'],
                'content_credit'  => $_GPC['content_credit'],
                'public_member' =>$_GPC['public_member']
            );
            if (!empty($_GPC['logo'])) {
                $cfg['logo'] = $_GPC['logo'];
            }
            if ($this->saveSettings($cfg)) {
                message('保存成功', 'refresh');
            }
        }
        include $this->template('setting');
    }

}