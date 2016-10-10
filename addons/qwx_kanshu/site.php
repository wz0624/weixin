<?php
defined('IN_IA') or exit('Access Denied');
class qwx_kanshuModuleSite extends WeModuleSite
{
    public function __construct()
    {
        global $_W, $_GPC;
        if (0 && empty($_W['openid'])) {
            echo '请在微信下使用！';
            exit;
        }
    }
    public function doMobilePlay()
    {
        global $_W, $_GPC;
        $html = array(
            'jsconfig' => $_W['account']['jssdkconfig'],
            'web_config' => $this->module['config'],
            'share_logo' => $_W['attachurl'] . $this->module['config']['share_pic']
        );
        include $this->template('index');
    }
}