<?php
/**
 * 客服消息群发模块微站定义
 *
 * @author zjl
 * @url http://bbs.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');
require_once 'include/core.class.php';

class Zjl_mass_custom_msgModuleSite extends WeModuleSite {

    public function sendCustomMsg($toUser, $optionId, $isAjax = false) {
        global $_W;
        if($_W['cache']['setting']['remote']['type']==2){
            $imgBoot = $_W['cache']['setting']['remote']['alioss']['url']."/";
        } else {
            $imgBoot = $_W['siteroot'] . "/attachment/";
        }
        $optionData = pdo_fetch("SELECT * FROM " . tablename($this->modulename . "_options") . " WHERE id= :oid", array(":oid" => $optionId));
        $postData = array();
        $postData['touser'] = "";
        $tempArray = urlencodeForArray(json_decode(htmlspecialchars_decode($optionData['options']), true));
        switch ($optionData['type']) {
            case 6:
                $postData['msgtype'] = 'news';
                //$postData['news'] = urlencodeForArray(json_decode(htmlspecialchars_decode($optionData['options']), true));
                foreach ($tempArray['articles'] as $index => $val) {
                    if (!preg_match("/^(http|https):/", urldecode($val['url']))) {
                        $tempArray['articles'][$index]['url'] = urlencode($_W['siteroot'] . "/app/") . $val['url'];
                    }
                    if (!preg_match("/^(http|https):/", urldecode($val['picurl']))) {
                        $tempArray['articles'][$index]['picurl'] = urlencode($imgBoot) . $val['picurl'];
                    }
                }
                $postData['news'] = $tempArray;
                break;
            case 7:
                $postData['msgtype'] = 'text';
                $postData['text'] = $tempArray;
                break;
        }
        $postData['touser'] = $toUser;
        $acc = WeAccount::create($optionData['weid']);
        $status = $acc->sendCustomNotice($postData); //测试
        if ($isAjax) {
            echo $status;
        }
        return $status;
    }

}