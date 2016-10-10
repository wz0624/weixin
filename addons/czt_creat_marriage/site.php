<?php
/**
 * 生成结婚证模块微站定义
 *
 * @author czt
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Czt_creat_marriageModuleSite extends WeModuleSite {

    public function doMobileIndex() {
        //这个操作被定义用来呈现 功能封面
        global $_W, $_GPC;
        include $this->template('index');
    }
    public function doMobileUpload() {
        //这个操作被定义用来呈现 功能封面
        global $_W, $_GPC;
        $name=time() . rand(1000, 9999) . '.jpg';
        $path = ATTACHMENT_ROOT . '/czt_creat_marriage/' . $_W['uniacid'] . '/' ;
        if (!file_exists($path)) {
            load()->func('file');
            mkdirs($path);
        }
        $postdata = file_get_contents("php://input");
        $postdata = str_replace(' ', '+', $postdata);
        $postdata = base64_decode($postdata);
        file_put_contents($path.$name, $postdata);
        echo $_W['attachurl'] . 'czt_creat_marriage/' . $_W['uniacid'] . '/' . $name;
    }

}