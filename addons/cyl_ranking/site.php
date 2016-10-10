<?php


defined('IN_IA') or exit('Access Denied');
class Cyl_rankingModuleSite extends WeModuleSite
{
    public function doMobileIndex()
    {
        global $_W, $_GPC;
        $settings           = $this->module['config'];
        $condition          = ' uniacid = :uniacid AND LENGTH(avatar)>0';
        $params[':uniacid'] = $_W['uniacid'];
        if (!empty($_GPC['keyword'])) {
            $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
        }
        $groups = pdo_fetchall('SELECT * FROM ' . tablename('mc_groups') . ' WHERE ' . 'uniacid = :uniacid ', $params, 'groupid');
        if ($settings['num']) {
            $psize = $settings['num'];
        } else {
            $psize = 20;
        }
        $total      = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('mc_members') . ' WHERE ' . $condition, $params);
        $list       = pdo_fetchall('SELECT * FROM ' . tablename('mc_members') . ' WHERE ' . $condition . ' ORDER BY credit1 DESC LIMIT ' . $psize, $params);
        $list_today = pdo_fetchall('SELECT * FROM ' . tablename('mc_members') . ' WHERE ' . $condition . ' ORDER BY uid DESC LIMIT 4', $params);
        $_share     = array(
            'desc' => $settings['share_desc'],
            'title' => $settings['share_title'],
            'imgUrl' => tomedia($settings['thumb'])
        );
        load()->func('tpl');
        $title = "积分排行榜";
        include $this->template('index');
    }
    public function doWebNews()
    {
        include $this->template('news');
    }
}