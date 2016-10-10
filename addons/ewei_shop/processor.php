<?php

//www.012wz.com 
if (!defined('IN_IA')) {
    exit('Access Denied');
}
require IA_ROOT . '/addons/ewei_shop/version.php';
require IA_ROOT . '/addons/ewei_shop/defines.php';
require EWEI_SHOP_INC . 'functions.php';
require EWEI_SHOP_INC . 'processor.php';
require EWEI_SHOP_INC . 'plugin/plugin_model.php';
class Ewei_shopModuleProcessor extends Processor
{
    public function respond()
    {
        return parent::respond();
    }
}
