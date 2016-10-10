<?php

//微赞科技 by QQ:800083075 http://www.012wz.com/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class VerifyMobile extends Plugin
{
    public function __construct()
    {
        parent::__construct('verify');
    }
    public function check()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function complete()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function qrcode()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
    public function detail()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
}