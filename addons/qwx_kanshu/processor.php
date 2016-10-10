<?php

defined('IN_IA') or exit('Access Denied');
class qwx_kanshuModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        $content = $this->message['content'];
    }
}