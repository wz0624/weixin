<?php

//www.012wz.com 
if (!defined('IN_IA')) {
    exit('Access Denied');
}
class Ewei_shopModule extends WeModule
{
    public function fieldsFormDisplay($rid = 0)
    {
    }
    public function fieldsFormSubmit($rid = 0)
    {
        return true;
    }
    public function settingsDisplay($settings)
    {
    }
}