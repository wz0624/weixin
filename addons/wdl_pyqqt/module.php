<?php
defined('IN_IA') or exit('Access Denied');
class wdl_pyqqtModule extends WeModule
{
    public function settingsDisplay($settings)
    {
        global $_W, $_GPC;
        if (checksubmit()) {
            $cfg = array(
                'index_page_title' => htmlspecialchars($_GPC['index_page_title']),
                'share_tip' => htmlspecialchars($_GPC['share_tip']),
                'bottom_tip' => htmlspecialchars($_GPC['bottom_tip']),
                'top_tip' => htmlspecialchars($_GPC['top_tip']),
                'share_title' => htmlspecialchars($_GPC['share_title']),
                'share_desc' => htmlspecialchars($_GPC['share_desc']),
                'share_cover' => $_GPC['share_cover'],
                'qdymaa_img' => $_GPC['qdymaa_img'],
                'qdymab_img' => $_GPC['qdymab_img'],
                'qdymac_img' => $_GPC['qdymac_img'],
                'mp3' => $_GPC['mp3'],
                'subscribe_url' => $_GPC['subscribe_url'],
                'share_ok_url' => $_GPC['share_ok_url'],
                'withdraw_line' => floatval($_GPC['withdraw_line']),
                'withdraw_max' => floatval($_GPC['withdraw_max']),
                'withdraw_discount' => floatval($_GPC['withdraw_discount']),
                'withdraw_tip' => htmlspecialchars($_GPC['withdraw_tip']),
                'tip_1' => htmlspecialchars($_GPC['tip_1']),
                'tip_2' => htmlspecialchars($_GPC['tip_2']),
                'tip_3' => htmlspecialchars($_GPC['tip_3']),
                'tip_4' => htmlspecialchars($_GPC['tip_4']),
                'tip_5' => htmlspecialchars($_GPC['tip_5']),
                'tip_6' => htmlspecialchars($_GPC['tip_6']),
                'tip_7' => htmlspecialchars($_GPC['tip_7']),
                'tip_8' => htmlspecialchars($_GPC['tip_8']),
                'tip_9' => htmlspecialchars($_GPC['tip_9']),
                'tip_10' => htmlspecialchars($_GPC['tip_10']),
                'tip_11' => htmlspecialchars($_GPC['tip_11'])
            );
            if (!empty($_GPC['sslcert']) && !empty($_GPC['sslkey'])) {
                $cfg['sslcert'] = htmlspecialchars($_GPC['sslcert']);
                $cfg['sslkey']  = htmlspecialchars($_GPC['sslkey']);
            } else {
                $config         = $this->module['config'];
                $cfg['sslcert'] = $config['sslcert'];
                $cfg['sslkey']  = $config['sslkey'];
            }
            if ($this->saveSettings($cfg)) {
                message('保存设置成功', 'refresh', 'success');
            }
        }
        include $this->template('setting');
    }
    private function upload_pem($name)
    {
        global $_W, $_GPC;
        $file             = $_FILES[$name];
        $file_name        = $file['name'];
        $file_tmp_name    = $file['tmp_name'];
        $file_type        = strtolower(substr($file_name, strpos($file_name, '.') + 1));
        $allow_type       = array(
            'pem'
        );
        $upload_path      = ATTACHMENT_ROOT . 'cert';
        $upload_file_name = $file['name'];
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777);
        }
        if (!in_array($file_type, $allow_type)) {
            $return['errcode'] = 0000;
            $return['errmsg']  = '仅支持上传pem格式的证书文件';
        }
        if (!is_uploaded_file($file_tmp_name)) {
            $return['errcode'] = 0000;
            $return['errmsg']  = '不是通过HTTP POST上传的文件';
        }
        if (!move_uploaded_file($file_tmp_name, $upload_path . '/' . $upload_file_name)) {
            $return['errcode'] = 0000;
            $return['errmsg']  = '上传失败';
        } else {
            $return['errcode'] = 1111;
            $return['errmsg']  = '上传证书成功';
            $return['path']    = $_W['attachurl'] . 'cert/' . $upload_file_name;
        }
        return $return;
    }
}