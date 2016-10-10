<?php
require_once dirname(__FILE__) . '/../FlashCommonService.php';
class FlashQrcodeService extends FlashCommonService
{
    const TMP_QRCODE = 1;
    const FOREVER_QRCODE = 2;
    public function __construct()
    {
        $this->table_name  = "qrcode";
        $this->columns     = "id,uniacid,acid,qrcid,name,keyword,model,ticket,expire,subnum,createtime,status,type,extra,url,scene_str";
        $this->plugin_name = "lonaking_flash";
    }
    public function createTempQrcode($name, $keyword, $expireSeconds = 2592000)
    {
        global $_W;
        $acid    = intval($_W['acid']);
        $uniacid = intval($_W['uniacid']);
        $qrcid   = pdo_fetchcolumn("SELECT qrcid FROM " . tablename('qrcode') . " WHERE acid = :acid AND uniaicd=:uniacid AND model = '2' ORDER BY qrcid DESC LIMIT 1", array(
            ':acid' => $acid,
            ':uniacid' => $uniacid
        ));
        $barcode = array(
            'expire_seconds' => $expireSeconds,
            'action_name' => 'QR_SCENE',
            'action_info' => array(
                'scene' => array(
                    'scene_id' => !empty($qrcid) ? ($qrcid + 1) : 100001
                )
            )
        );
        $account = $this->createWexinAccount();
        $qrcode  = $account->barCodeCreateDisposable($barcode);
        if (is_error($qrcode)) {
            throw new Exception("抱歉，生成二维码失败，您的公众号可能不支持参数二维码", 9001);
        } else {
            $insert = array(
                'uniacid' => $_W['uniacid'],
                'acid' => $acid,
                'qrcid' => $barcode['action_info']['scene']['scene_id'],
                'scene_str' => '',
                'keyword' => $keyword,
                'name' => $name,
                'model' => self::TMP_QRCODE,
                'ticket' => $qrcode['ticket'],
                'url' => $qrcode['url'],
                'expire' => $expireSeconds,
                'createtime' => time(),
                'status' => '1',
                'type' => 'scene'
            );
            return $this->insertData($insert);
        }
    }
    public function createForeverQrcode($name, $keyword)
    {
        global $_W;
        $acid    = intval($_W['acid']);
        $uniacid = intval($_W['uniacid']);
        $qrcid   = pdo_fetchcolumn("SELECT qrcid FROM " . tablename('qrcode') . " WHERE acid = :acid AND uniaicd=:uniacid AND model = '2' ORDER BY qrcid DESC LIMIT 1", array(
            ':acid' => $acid,
            ':uniacid' => $uniacid
        ));
        $barcode = array(
            'action_name' => 'QR_LIMIT_SCENE',
            'action_info' => array(
                'scene' => array(
                    'scene_id' => !empty($qrcid) ? ($qrcid + 1) : 100001
                )
            )
        );
        $account = $this->createWexinAccount();
        $qrcode  = $account->barCodeCreateFixed($barcode);
        if (is_error($qrcode)) {
            throw new Exception("抱歉，生成二维码失败，您的公众号可能不支持参数二维码", 9001);
        } else {
            $insert = array(
                'uniacid' => $_W['uniacid'],
                'acid' => $acid,
                'qrcid' => $barcode['action_info']['scene']['scene_id'],
                'scene_str' => '',
                'keyword' => $keyword,
                'name' => $name,
                'model' => self::FOREVER_QRCODE,
                'ticket' => $qrcode['ticket'],
                'url' => $qrcode['url'],
                'expire' => 0,
                'createtime' => time(),
                'status' => '1',
                'type' => 'scene'
            );
            return $this->insertData($insert);
        }
    }
    public function createForeverStrSceneQrcode($name, $keyword, $sceneStr)
    {
        global $_W;
        $acid    = intval($_W['acid']);
        $uniacid = intval($_W['uniacid']);
        $barcode = array(
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => array(
                'scene' => array(
                    'scene_str' => $sceneStr
                )
            )
        );
        $account = $this->createWexinAccount();
        $qrcode  = $account->barCodeCreateFixed($barcode);
        if (is_error($qrcode)) {
            throw new Exception("抱歉，生成二维码失败，您的公众号可能不支持参数二维码", 9001);
        } else {
            $insert = array(
                'uniacid' => $uniacid,
                'acid' => $acid,
                'qrcid' => '0',
                'scene_str' => $sceneStr,
                'keyword' => $keyword,
                'name' => $name,
                'model' => self::FOREVER_QRCODE,
                'ticket' => $qrcode['ticket'],
                'url' => $qrcode['url'],
                'expire' => 0,
                'createtime' => time(),
                'status' => '1',
                'type' => 'scene'
            );
            return $this->insertData($insert);
        }
    }
}
