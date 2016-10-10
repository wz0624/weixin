<?php


defined('IN_IA') or exit('Access Denied');
include '../addons/bm_meetingx/phpqrcode.php';
class Bm_meetingxModule extends WeModule
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
            $dir = '../attachment/images/bm_payx';
            if (is_dir($dir)) {
            } else {
                mkdir("../attachment/images/bm_payx");
            }
            $reply = pdo_fetch("SELECT * FROM " . tablename('bm_meetingx_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(
                ':rid' => $rid
            ));
            if (empty($reply['qrcode'])) {
                $value                = $_W['siteroot'] . 'app/' . $this->createmobileurl('show', array(
                    'rid' => $rid
                ));
                $errorCorrectionLevel = 'H';
                $matrixPointSize      = '16';
                $rand_file            = rand() . '.png';
                $att_target_file      = 'qr-' . $rand_file;
                $target_file          = '../attachment/images/bm_meetingx/' . $att_target_file;
                QRcode::png($value, $target_file, $errorCorrectionLevel, $matrixPointSize);
                $reply['qrcode'] = $target_file;
            }
        }
        load()->func('tpl');
        include $this->template('form');
    }
    public function fieldsFormValidate($rid = 0)
    {
        return '';
    }
    public function fieldsFormSubmit($rid)
    {
        global $_W, $_GPC;
        $weid = $_W['uniacid'];
        $data = array(
            'rid' => $rid,
            'weid' => $weid,
            'desc' => $_GPC['desc'],
            'pictype' => $_GPC['pictype'],
            'picurl' => $_GPC['picurl'],
            'urlx' => $_GPC['urlx'],
            'title' => $_GPC['title'],
            'starttime' => $_GPC['starttime'],
            'endtime' => $_GPC['endtime'],
            'qrcode' => $_GPC['qrcode'],
            'urly' => $_GPC['urly'],
            'url1' => $_GPC['url1'],
            'url2' => $_GPC['url2'],
            'memo1' => $_GPC['memo1'],
            'memo2' => $_GPC['memo2'],
            'memo' => $_GPC['memo'],
            'templateid' => $_GPC['templateid'],
            'openid' => $_GPC['openid'],
            'templateid1' => $_GPC['templateid1'],
            'mtitle' => $_GPC['mtitle'],
            'mdesc' => $_GPC['mdesc'],
            'price' => $_GPC['price'],
            'count' => $_GPC['count'],
            'memo3' => $_GPC['memo3'],
            'url3' => $_GPC['url3'],
            'tmpsmstitle' => $_GPC['tmpsmstitle'],
            'tmpsmsdesc' => $_GPC['tmpsmsdesc'],
            'tmpsmstitle1' => $_GPC['tmpsmstitle1'],
            'tmpsmsdesc1' => $_GPC['tmpsmsdesc1'],
            'templateid2' => $_GPC['templateid2'],
            'tel' => $_GPC['tel']
        );
        if ($_W['ispost']) {
            if (empty($_GPC['reply_id'])) {
                pdo_insert('bm_meetingx_reply', $data);
            } else {
                pdo_update('bm_meetingx_reply', $data, array(
                    'id' => $_GPC['reply_id']
                ));
            }
            message('更新成功', referer(), 'success');
        }
    }
    public function ruleDeleted($rid)
    {
        global $_W;
        $replies  = pdo_fetchall("SELECT *  FROM " . tablename('bm_meetingx_reply') . " WHERE rid = '$rid'");
        $deleteid = array();
        if (!empty($replies)) {
            foreach ($replies as $index => $row) {
                $deleteid[] = $row['id'];
            }
        }
        pdo_delete('bm_meetingx_reply', "id IN ('" . implode("','", $deleteid) . "')");
        return true;
    }
}
