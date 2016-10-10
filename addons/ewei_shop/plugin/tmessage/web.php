<?php

//微赞科技 by QQ:800083075 http://www.012wz.com/
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class TmessageWeb extends Plugin
{
    public function __construct()
    {
        parent::__construct('tmessage');
    }
    public function index()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
}