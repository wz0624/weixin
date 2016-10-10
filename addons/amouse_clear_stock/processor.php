<?php

/**
 *  处理程序
 *
 * @author  ZOMBIESZY
 */
defined('IN_IA') or exit ('Access Denied');
class Amouse_Clear_StockModuleProcessor  extends WeModuleProcessor{

    public function respond() {
        global $_W;
        $uniacid= $_W['uniacid'];
        $from = $this->message['from'];
    }

}