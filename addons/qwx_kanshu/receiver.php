<?php
defined('IN_IA') or exit('Access Denied');
class qwx_kanshuModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        $type = $this->message['type'];
    }
}