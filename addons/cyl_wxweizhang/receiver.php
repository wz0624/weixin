<?php
defined('IN_IA') or exit('Access Denied');
class Cyl_wxweizhangModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        $type = $this->message['type'];
    }
}