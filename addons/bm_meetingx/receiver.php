<?php


defined('IN_IA') or exit('Access Denied');
class Bm_meetingxModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        $type = $this->message['type'];
    }
}