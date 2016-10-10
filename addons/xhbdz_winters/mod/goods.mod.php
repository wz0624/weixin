<?php

defined('IN_IA') or exit('Access Denied');

class goods {

    //列出所有有效商品部分信息
	public function get_goodslist() {
	    global $_W;
	    $uniacid = $_W['uniacid'];
		$exist = pdo_fetchall('SELECT `id`,`title`,`price`,`picimg` FROM '.tablename('xhbdz_goods')." WHERE `uniacid` = $uniacid AND `status` = 1 AND `del` = 0 ORDER BY `id` ASC");
		if (!empty($exist)) {
			return $exist;
		}
		return false;
	}
    //获取单个有效商品信息
	public function get_goods($id) {
	    global $_W;
        $uniacid = $_W['uniacid'];
		if(empty($id)){
		$exist = pdo_fetchall('SELECT * FROM '.tablename('xhbdz_goods')." WHERE uniacid = $uniacid AND `status` = 1 AND `del` = 0  ");
		}else {
			$exist = pdo_fetch('SELECT * FROM '.tablename('xhbdz_goods')." WHERE uniacid = $uniacid AND `status` = 1 AND `del` = 0 and id=".$id);
			}
		if (!empty($exist)) {
			return $exist;
		}
		return false;
	}
	
}