<?php
defined('IN_IA') or exit('Access Denied');
$table = 'hc_chuansong_list';
$op    = empty($_GPC['op']) ? 'display' : $_GPC['op'];
if ($op == 'detail') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        message('参数错误');
    }
    $where  = "WHERE weid=" . $_W['uniacid'] . " AND  pid=" . $id . " ";
    $pindex = max(1, intval($_GPC['page']));
    $psize  = 20;
    $total  = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hc_chuansong_user') . $where);
    $start  = ($pindex - 1) * $psize;
    $where .= "  order by `id` desc   LIMIT {$start},{$psize}";
    $list  = pdo_fetchall("SELECT * FROM " . tablename('hc_chuansong_user') . " " . $where);
    $pager = pagination($total, $pindex, $psize);
} elseif ($_GPC['op'] == 'search_detail') {
    $id      = intval($_GPC['id']);
    $keyword = $_GPC['keyword'];
    if (empty($id) || empty($keyword)) {
        message('参数错误');
    }
    $where  = "WHERE weid=" . $_W['uniacid'] . " AND  pid=" . $id . " AND realname  like '%" . $keyword . "%' ";
    $pindex = max(1, intval($_GPC['page']));
    $psize  = 20;
    $total  = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('hc_chuansong_user') . $where);
    $start  = ($pindex - 1) * $psize;
    $where .= "  order by `id` desc   LIMIT {$start},{$psize}";
    $list  = pdo_fetchall("SELECT * FROM " . tablename('hc_chuansong_user') . " " . $where);
    $pager = pagination($total, $pindex, $psize);
} elseif ($_GPC['op'] == 'post') {
    $field = array(
        'title',
        'thumb',
        'str1',
        'page_title',
        'total_nums',
        'limit_nums',
        'is_default',
        'status',
        'share_title',
        'share_desc',
        'share_thumb',
        'share_link',
        'share_kouhao',
        'part_time',
        'page_parttime',
        'result_thumb',
        'houxuan_thumb'
    );
    $id    = intval($_GPC['id']);
    if ($_W['ispost']) {
        foreach ($field as $v) {
            $insert[$v] = $_GPC[$v];
        }
        $insert['share_detail'] = htmlspecialchars_decode($_GPC['share_detail']);
        $insert['desc']         = htmlspecialchars_decode($_GPC['desc']);
        $insert['regist_color'] = $_GPC['regist_color'];
        $insert['home_color']   = $_GPC['home_color'];
        if (isset($_GPC['starttime']) && isset($_GPC['endtime'])) {
            $insert['starttime'] = strtotime($_GPC['starttime']);
            $insert['endtime']   = strtotime($_GPC['endtime']);
        }
        if ($insert['is_default'] == 1) {
            pdo_query('update ' . tablename($table) . ' set is_default=0 where weid=' . $_W['uniacid']);
        }
        if ($id > 0) {
            $temp = pdo_update($table, $insert, array(
                'id' => $id,
                'weid' => $_W['uniacid']
            ));
        } else {
            $insert['weid'] = $_W['uniacid'];
            $temp           = pdo_insert($table, $insert);
        }
        if ($temp === false) {
            message('抱歉，数据操作失败！', '', 'error');
        } else {
            message('更新数据成功！', $this->createWeburl('list'), 'success');
        }
    }
    if ($id > 0) {
        $item = pdo_fetch('select * from ' . tablename($table) . ' where weid=:weid AND id=:id', array(
            ':weid' => $_W['uniacid'],
            ':id' => $id
        ));
    }
    if ($item == false) {
        $item = array(
            'isdefault' => 0
        );
    }
} elseif ($op == 'delete') {
    $id = intval($_GPC['id']);
    if (empty($id)) {
        message('参数错误，请确认操作');
    }
    $temp = pdo_delete($table, array(
        'id' => $id,
        'weid' => $_W['uniacid']
    ));
    if ($temp == false) {
        message('抱歉，刚才修改的数据失败！', '', 'error');
    } else {
        message('删除数据成功！', $this->createWeburl('cate'), 'success');
    }
} elseif ($op == 'display') {
    $where  = "WHERE weid=" . $_W['uniacid'] . " ";
    $pindex = max(1, intval($_GPC['page']));
    $psize  = 20;
    $total  = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($table) . $where);
    $start  = ($pindex - 1) * $psize;
    $where .= "  order by `id` desc   LIMIT {$start},{$psize}";
    $list  = pdo_fetchall(" SELECT l.*, COUNT(u.id) AS join_num FROM " . tablename('hc_chuansong_list') . " AS l LEFT JOIN " . tablename('hc_chuansong_user') . " AS u ON l.id=u.pid WHERE l.weid='" . $_W['uniacid'] . "' GROUP BY l.id ");
    $pager = pagination($total, $pindex, $psize);
}
include $this->template('adv_list');
