<?php


defined('IN_IA') or exit('Access Denied');
class Bm_inbarkModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        $type = $this->message['type'];
    }
}