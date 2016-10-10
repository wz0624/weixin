<?php
defined('IN_IA') or exit('Access Denied');
class Yuyi_heartModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        $content = $this->message['content'];
    }
}