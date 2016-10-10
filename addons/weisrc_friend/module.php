<?php
/**
 * 火爆朋友圈
 *
 * 作者:迷失卍国度
 *
 * qq : 15595755
 */
defined('IN_IA') or exit('Access Denied');
include "model.php";

class weisrc_friendModule extends WeModule {

    public $tablename = 'weisrc_friend_reply';

    public function fieldsFormDisplay($rid = 0) {
        global $_W;
        load()->func('tpl');
        if (!empty($rid)) {
            $reply = pdo_fetch("SELECT * FROM " . tablename($this->tablename) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
        }
        if (!$reply) {
            $now = TIMESTAMP;
            $reply = array(
                "title" => "生成我的土豪朋友圈",
                "start_picurl" => "../addons/weisrc_friend/template/style/game.jpg",
                "description" => "生成我的土豪朋友圈",
                "rule" => '',
                "starttime" => $now,
                "endtime" => strtotime(date("Y-m-d H:i", $now + 7 * 24 * 3600)),
                "end_picurl" => "../addons/weisrc_friend/template/style/game.jpg",
                "share_image" => "../addons/weisrc_friend/icon.jpg",
                "end_theme" => "活动结束了",
                "end_instruction" => "活动已经结束了",
                "bg" => "../addons/weisrc_friend/template/mobile/images/topbg.jpg",
                'copyright' => '（此朋友圈纯属虚构）',
                'ad_url' => 'http://mp.weixin.qq.com',
                'nickname' => '微信圈',
                'desc' => '牛逼的玩法,只有少数人才懂！',
                'logo' => '../addons/weisrc_friend/icon.jpg',
                'qrcode' => '../addons/weisrc_friend/icon.jpg',
                "share_title" => "欢迎参加火爆朋友圈",
                "share_desc" => "亲，欢迎参加火爆朋友圈活动，祝您好运哦！！ ",
            );
        }
        include $this->template('form');
    }

    public function fieldsFormValidate($rid = 0) {
        //规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
        return '';
    }

    public function fieldsFormSubmit($rid) {
        global $_GPC, $_W;
        load()->func('tpl');
        $id = intval($_GPC['reply_id']);

        $insert = array(
            'rid' => $rid,
            'weid' => $_W['uniacid'],
            'title' => trim($_GPC['title']),
            'content' => trim($_GPC['content']),
            'description' => trim($_GPC['description']),
            'rule' => trim($_GPC['rule']),
            'end_theme' => $_GPC['end_theme'],
            'end_instruction' => $_GPC['end_instruction'],
            'starttime' => strtotime($_GPC['datelimit']['start']),
            'endtime' => strtotime($_GPC['datelimit']['end']),
            'share_title' => $_GPC['share_title'],
            'share_desc' => $_GPC['share_desc'],
            'share_url' => $_GPC['share_url'],
            'follow_url' => $_GPC['follow_url'],
            'ad_url' => $_GPC['ad_url'],
            'nickname' => $_GPC['nickname'],
            'desc' => $_GPC['desc'],
            'copyright' => $_GPC['copyright'],
            'copyrighturl' => $_GPC['copyrighturl'],
            'dateline' => TIMESTAMP,
        );

        if (!empty($_GPC['start_picurl'])) {
            $insert['start_picurl'] = $_GPC['start_picurl'];
        }

        if (!empty($_GPC['end_picurl'])) {
            $insert['end_picurl'] = $_GPC['end_picurl'];
        }

        if (!empty($_GPC['bg'])) {
            $insert['bg'] = $_GPC['bg'];
        }
        if (!empty($_GPC['logo'])) {
            $insert['logo'] = $_GPC['logo'];
        }
        if (!empty($_GPC['qrcode'])) {
            $insert['qrcode'] = $_GPC['qrcode'];
        }

        if (empty($id)) {
            if ($insert['starttime'] <= time()) {
                $insert['status'] = 1;
            } else {
                $insert['status'] = 0;
            }
            $id = pdo_insert($this->tablename, $insert);
        } else {
            unset($insert['dateline']);
            pdo_update($this->tablename, $insert, array('id' => $id));
        }
        return true;
    }

    public function ruleDeleted($rid) {
        pdo_delete('weisrc_friend_reply', array('rid' => $rid));
        pdo_delete('weisrc_friend_fans', array('rid' => $rid));
    }

    public function settingsDisplay($settings) {
        global $_GPC, $_W;
    }
}
