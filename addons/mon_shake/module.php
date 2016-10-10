<?php
/**
 *
 *
 * @author  codeMonkey
 * qq:631872807
 * @url
 */
defined('IN_IA') or exit('Access Denied');

define("MON_SHAKE", "mon_shake");
define("MON_SHAKE_RES", "../addons/" . MON_SHAKE . "/");
require_once IA_ROOT . "/addons/" . MON_SHAKE . "/dbutil.class.php";
require_once IA_ROOT . "/addons/" . MON_SHAKE . "/monUtil.class.php";

class Mon_ShakeModule extends WeModule
{

    public $weid;

    public function __construct()
    {
        global $_W;
        $this->weid = IMS_VERSION < 0.6 ? $_W['weid'] : $_W['uniacid'];
    }

    public function fieldsFormDisplay($rid = 0)
    {
        global $_W;

        if (!empty($rid)) {
            $reply = DBUtil::findUnique(DBUtil::$TABLE_SHAKE, array(":rid" => $rid));

            $reply['starttime'] = date("Y-m-d  H:i", $reply['starttime']);
            $reply['endtime'] = date("Y-m-d  H:i", $reply['endtime']);
        }


        load()->func('tpl');


        include $this->template('form');


    }

    public function fieldsFormValidate($rid = 0)
    {
        global $_GPC, $_W;


        return '';
    }

    public function fieldsFormSubmit($rid)
    {
        global $_GPC;
        $sid = $_GPC['sid'];

        $data = array(
            'rid' => $rid,
            'weid' => $this->weid,
            'title' => $_GPC['title'],
            'starttime' => strtotime($_GPC['starttime']),
            'endtime' => strtotime($_GPC['endtime']),
            'follow_url' => $_GPC['follow_url'],
            'top_banner' => $_GPC['top_banner'],
            'top_banner_url' => $_GPC['top_banner_url'],
            'section1_bg' => $_GPC['section1_bg'],
            'section1_layer5_1' => $_GPC['section1_layer5_1'],
            'section1_layer3_2' => $_GPC['section1_layer3_2'],
            'section1_layer6_3' => $_GPC['section1_layer6_3'],
            'section1_layer7_4' => $_GPC['section1_layer7_4'],
            'section1_layer9_5' => $_GPC['section1_layer9_5'],
            'section2_bg' => $_GPC['section2_bg'],
            'section2_layer16_1' => $_GPC['section2_layer16_1'],
            'section2_layer18_2' => $_GPC['section2_layer18_2'],
            'section2_layer31_3' => $_GPC['section2_layer31_3'],
            'section2_layer17_4' => $_GPC['section2_layer17_4'],
            'good_dlg_bg' => $_GPC['good_dlg_bg'],
            'buy_btn_url' => $_GPC['buy_btn_url'],
            'section3_bg' => $_GPC['section3_bg'],
            'section3_layer15_1' => $_GPC['section3_layer15_1'],
            'section3_layer11_2' => $_GPC['section3_layer11_2'],
            'section3_layer14_3' => $_GPC['section3_layer14_3'],
            'section3_erweima' => $_GPC['section3_erweima'],
            'new_title' => $_GPC['new_title'],
            'new_icon' => $_GPC['new_icon'],
            'new_content' => $_GPC['new_content'],
            'share_title' => $_GPC['share_title'],
            'share_icon' => $_GPC['share_icon'],
            'share_content' => $_GPC['share_content'],
            'follow_dlg_tip' => $_GPC['follow_dlg_tip'],
            'follow_btn_name' => $_GPC['follow_btn_name'],
            'shake_day_limit' => $_GPC['shake_day_limit'],
            'total_limit' => $_GPC['total_limit'],
            'updatetime' => TIMESTAMP
        );

        if (empty($sid)) {
            $data['createtime'] = TIMESTAMP;
            DBUtil::create(DBUtil::$TABLE_SHAKE, $data);
        } else {
            DBUtil::updateById(DBUtil::$TABLE_SHAKE, $data, $sid);
        }

        return true;
    }

    public function ruleDeleted($rid)
    {
        $shake = DBUtil::findUnique(DBUtil::$TABLE_SHAKE, array(":rid" => $rid));
        pdo_delete(DBUtil::$TABLE_SHAKE_RECORD, array("sid" => $shake['id']));
        pdo_delete(DBUtil::$TABLE_SHAKE_PRIZE,array('sid'=>$shake['id']));
    }


}