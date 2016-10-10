<?php

defined('IN_IA') or exit('Access Denied');
class Meepo_onlineModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        $content = $this->message['content'];
    }
}