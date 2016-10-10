<?php
defined('IN_IA') or exit('Access Denied');
class wdl_dangerModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        $content = $this->message['content'];
    }
}