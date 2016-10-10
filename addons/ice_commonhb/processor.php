<?php
defined('IN_IA') or exit('Access Denied');
class Ice_commonhbModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        $content = $this->message['content'];
    }
}
