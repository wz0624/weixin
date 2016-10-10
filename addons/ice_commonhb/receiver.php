<?php

defined('IN_IA') or exit('Access Denied');
class Ice_commonhbModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        $type = $this->message['type'];
    }
}
