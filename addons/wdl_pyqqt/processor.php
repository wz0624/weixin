<?php

defined('IN_IA') or exit('Access Denied');
class wdl_pyqqtModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        $content = $this->message['content'];
    }
}