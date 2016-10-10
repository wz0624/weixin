<?php


defined('IN_IA') or exit('Access Denied');
class Hc_mynzjModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        $content = $this->message['content'];
    }
}