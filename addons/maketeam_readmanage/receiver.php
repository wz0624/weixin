<?php
defined('IN_IA') or exit('Access Denied');
class Maketeam_readmanageModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        $type = $this->message['type'];
    }
}