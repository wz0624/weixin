<?php


defined('IN_IA') or exit('Access Denied');
class Hc_mynzjModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        $type = $this->message['type'];
    }
}