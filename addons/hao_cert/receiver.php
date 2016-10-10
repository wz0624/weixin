<?php
defined('IN_IA') or exit('Access Denied');
class Hao_certModuleReceiver extends WeModuleReceiver
{
    public function receive()
    {
        $type = $this->message['type'];
    }
}