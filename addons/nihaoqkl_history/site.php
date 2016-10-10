<?php
/**
 * 打卡系统模块微站定义
 *
 * @author 
 * @url http://www.012wz.com/
 */
 
defined('IN_IA') or exit('Access Denied');

class Nihaoqkl_historyModuleSite extends WeModuleSite {

    public function doMobileIndex(){
        global $_GPC,$_W;
        // 处理 GET 提交
        $pageindex = max(intval($_GPC['page']), 1); // 当前页码
        $pagesize = 8; // 设置分页大小

        $where = ' WHERE uniacid=:uniacid AND status = 1';
        $params = array(
            ':uniacid'=>$_W['uniacid']
        );

        $sql = 'SELECT COUNT(*) FROM '.tablename('addons_history').$where;
        $total = pdo_fetchcolumn($sql, $params);
        $pager = pagination($total, $pageindex, $pagesize);

        $sql = 'SELECT * FROM '.tablename('addons_history')." {$where} ORDER BY create_time desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
        $lists=pdo_fetchall($sql, $params);

        $mode = pdo_fetchcolumn("select mode from ". tablename('addons_history_mode') . ' where uniacid='.$_W['uniacid']);

        include $this->template('index');
    }

    public function doMobileGetmore(){
        global $_GPC,$_W;
        // 处理 GET 提交
        $pageindex = max(intval($_GPC['page']), 2); // 当前页码
        $pagesize = 8; // 设置分页大小

        $where = ' WHERE uniacid=:uniacid AND status = 1';
        $params = array(
            ':uniacid'=>$_W['uniacid']
        );

        $sql = 'SELECT COUNT(*) FROM '.tablename('addons_history').$where;
        $total = pdo_fetchcolumn($sql, $params);
        $totalpage=ceil($total / $pagesize);

        if($pageindex > $totalpage) { message(array('status'=>0,'data'=>'没有数据了'),'','ajax'); }

        $sql = 'SELECT * FROM '.tablename('addons_history')." {$where} ORDER BY create_time desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
        $lists=pdo_fetchall($sql, $params);

        $tpl = <<<HTMLEOF
<a href="%s">
        <li class="history weui_cells_access">
            <p class="time">%s</p>
            <p class="title">%s</p>
            %s
            <p class="summary">%s</p>
            <div class="weui_cell readme" style="border:0;padding:0;">
                <div class="weui_cell_bd weui_cell_primary">
                    <p>阅读原文</p>
                </div>
                <div class="weui_cell_ft">
                </div>
            </div>
        </li>
    </a>
HTMLEOF;

        if($lists){
            $html='';
            $mode = pdo_fetchcolumn("select mode from ". tablename('addons_history_mode') . ' where uniacid='.$_W['uniacid']);
            foreach($lists as $key => $val) {
                if($mode){
                    $cover = '<p class="cover" style="background:url(/attachment/'.$val['cover'].')"></p>';
                } else {
                    $cover='';
                }
                $html.= sprintf($tpl,$val['url'], date('Y年m月d日 H:i',$val['time']),$val['title'],
                    $cover, mb_substr(strip_tags(htmlspecialchars_decode($val['summary'])),0,50,'utf-8'));
            }

            message(array('status'=>1,'data'=>$html),'','ajax');
        }
        else
        {
            message(array('status'=>0,'data'=>'没有数据了'),'','ajax');
        }

    }

    public function doWebAdd(){
        global $_W,$_GPC;
        if(checksubmit()){
            $r=pdo_insert(
                'addons_history',
                array(
                    'uniacid'=>$_W['uniacid'],
                    'title'=>$_GPC['title'],
                    'summary'=>$_GPC['summary'],
                    'url'=>$_GPC['url'],
                    'cover'=>$_GPC['cover'],
                    'create_time'=>TIMESTAMP,
                    'update_time'=>TIMESTAMP,
                )
            );
            $r ? message('添加成功') : message('添加失败啦','','error');
        }
        $op = 'add';
        include $this->template('lists');
    }

    public function doWebModify(){
        global $_W,$_GPC;
        if(checksubmit()){
            $r=pdo_update(
                'addons_history',
                array(
                    'title'=>$_GPC['title'],
                    'summary'=>$_GPC['summary'],
                    'url'=>$_GPC['url'],
                    'cover'=>$_GPC['cover'],
                    'update_time'=>TIMESTAMP,
                ),
                array(
                    'id'=>$_GPC['id']
                )
            );
            $r ? message('修改成功') : message('修改失败啦','','error');
        }

        $op = 'modify';
        $info = pdo_fetch("select * from " . tablename('addons_history') . ' where id = :id',array(':id'=>$_GPC['id']));
        include $this->template('lists');
    }

    public function doWebDel(){
        global $_GPC;
        $r=pdo_update(
            'addons_history',
            array('status'=>0),
            array('id'=>$_GPC['id'])
        );
        $r ? message('成功删除') : message('删除失败啦','','error');
    }

    public function doWebLists(){
        global $_GPC,$_W;

        $op = 'lists';
        // 处理 GET 提交
        $pageindex = max(intval($_GPC['page']), 1); // 当前页码
        $pagesize = 15; // 设置分页大小

        $where = ' WHERE uniacid=:uniacid AND status = 1';
        $params = array(
            ':uniacid'=>$_W['uniacid']
        );
        if (!empty($_GPC['title'])) {
            $where .= ' AND (`title` like :name )';
            $params[':title'] = "%{$_GPC['title']}%";
        }
        if (!empty($_GPC['id'])) {
            $where .= ' AND (id = :id)';
            $params[':id'] = intval($_GPC['id']);
        }

        $sql = 'SELECT COUNT(*) FROM '.tablename('addons_history').$where;
        $total = pdo_fetchcolumn($sql, $params);
        $pager = pagination($total, $pageindex, $pagesize);

        $sql = 'SELECT * FROM '.tablename('addons_history')." {$where} ORDER BY create_time desc LIMIT ".(($pageindex -1) * $pagesize).','. $pagesize;
        $lists=pdo_fetchall($sql, $params);

        include $this->template('lists');
    }

    public function doWebSet(){
        global $_W,$_GPC;
        if(checksubmit()){
            
            $sql="select * from " . tablename('addons_history_mode') . ' where uniacid = ' .$_W['uniacid'];
            $result=pdo_fetch($sql);

            if(!$result){
                $r=pdo_insert(
                    'addons_history_mode',
                    array(
                        'mode'=>$_GPC['mode'],
                        'uniacid'=>$_W['uniacid']
                    )
                );
            }
            else
            {
                $r=pdo_update(
                    'addons_history_mode',
                    array(
                        'mode'=>$_GPC['mode']
                    ),
                    array(
                        'uniacid'=>$_W['uniacid']
                    )
                );
            }

            $r ? message('设置成功') : message('设置失败啦','','error');
        }
        $op = 'set';

        $mode = pdo_fetchcolumn("select mode from ". tablename('addons_history_mode') . ' where uniacid='.$_W['uniacid']);

        include $this->template('lists');
    }
}