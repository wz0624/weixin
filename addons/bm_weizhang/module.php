<?php


defined('IN_IA') or exit('Access Denied');
class bm_weizhangModule extends WeModule
{
    public function settingsDisplay($settings)
    {
        global $_GPC, $_W;
        $site1 = "<a href='" . $_W['siteroot'] . "app/index.php?i=" . $_W['weid'] . "&c=entry&m=bm_weizhang&do=detail'>违章查询</a>";
        $site2 = $_W['siteroot'] . "app/index.php?i=" . $_W['weid'] . "&c=entry&m=bm_weizhang&do=detail";
        if (!isset($settings['city'])) {
            $settings['city'] = '盐城';
        }
        $file = fopen(IA_ROOT . '/addons/bm_weizhang/pro.csv', 'r');
        while ($data = fgetcsv($file)) {
            $arr[] = $data;
        }
        fclose($file);
        if (checksubmit()) {
            $dat = array(
                'city' => $_GPC['city'],
                'kkk' => $_GPC['kkk']
            );
            $this->saveSettings($dat);
            message('保存成功', 'refresh');
        }
        if ($_GPC['ajax'] == 1) {
            $file1 = fopen(IA_ROOT . '/addons/bm_weizhang/citys.csv', 'r');
            while ($data1 = fgetcsv($file1)) {
                $arr1[] = $data1;
            }
            fclose($file1);
            foreach ($arr1 as $v) {
                if ($_GPC['pro'] == $v[6]) {
                    $q[] = array(
                        "text" => $v[0],
                        'cid' => $v[1]
                    );
                }
            }
            echo json_encode($q);
        } else {
            include $this->template('settings');
        }
    }
}