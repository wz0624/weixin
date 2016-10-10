<?php
defined('IN_IA') or exit('Access Denied');
class Hao_certModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        $content = $this->message['content'];
    }
}