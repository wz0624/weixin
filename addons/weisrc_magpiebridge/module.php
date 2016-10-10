<?php
defined('IN_IA') or exit('Access Denied');
include 'model.php';
class weisrc_magpiebridgeModule extends WeModule
{
    public $tablename = 'weisrc_magpiebridge_reply';
    public $tableaward = 'weisrc_magpiebridge_award';
    public function fieldsFormDisplay($rid = 0)
    {
        global $_W;
        load()->func('tpl');
        if (!empty($rid)) {
            $reply = pdo_fetch("SELECT * FROM " . tablename($this->tablename) . " WHERE rid = :rid ORDER BY `id` DESC", array(
                ':rid' => $rid
            ));
            $prize = pdo_fetchall("SELECT * FROM " . tablename($this->tableaward) . " WHERE rid = :rid ORDER BY `id` asc", array(
                ':rid' => $rid
            ));
        }
        $time = date('Y-m-d H:i', TIMESTAMP + 3600 * 24);
        if (!$reply) {
            $now   = TIMESTAMP;
            $reply = array(
                "title" => "七夕走鹊桥,粉丝来相“惠”",
                "start_picurl" => "../addons/weisrc_magpiebridge/template/mobile/bridge/image/game.jpg",
                "description" => "七夕走鹊桥,粉丝来相“惠",
                "rule" => '<p>走对70步，尽可能快，别掉水里。小伙伴只需要不断踩着图案快快前进即可。</p>
								<p>
									很简单吧，谁都可以会玩，但并不是谁都能玩得很好噢。
								</p>
								<p>
									小伙伴，不服来战。
								</p>',
                "award" => '<p>1、 手机号码为兑奖重要凭证，填写应当真实有效，如若有误，作废处理。</p>
							<p>2、 优惠券使用规则参照商家实际制定。</p>
							<p>3、 本活动最终解释权归xxxx所有。</p>',
                "awardtip" => "注:活动时间截止{$time},活动结束后依次按排行榜名次发奖",
                "starttime" => $now,
                "endtime" => strtotime(date("Y-m-d H:i", $now + 7 * 24 * 3600)),
                "end_picurl" => "../addons/weisrc_magpiebridge/template/mobile/bridge/image/game.jpg",
                "share_image" => "../addons/weisrc_magpiebridge/icon.jpg",
                "end_theme" => "活动结束了",
                "end_instruction" => "活动已经结束了",
                "bg" => "../addons/weisrc_magpiebridge/template/mobile/bridge/image/bg.jpg",
                "btn_start" => "../addons/weisrc_magpiebridge/template/mobile/bridge/image/btn_start.png",
                "game_page_bg" => "../addons/weisrc_magpiebridge/template/mobile/bridge/image/game_page_bg.jpg",
                "result_page_bg" => "../addons/weisrc_magpiebridge/template/mobile/bridge/image/result_page_bg.jpg",
                "game_tile" => "../addons/weisrc_magpiebridge/template/mobile/bridge/image/game_tile.png",
                "number_times" => 0,
                "gametime" => 60,
                "gamelevel" => 60,
                "showusernum" => 20,
                "cover" => "../addons/weisrc_magpiebridge/template/mobile/boat/App_Content/Game/Boats/style/images/cover.jpg",
                "ad" => "../addons/weisrc_magpiebridge/template/mobile/bridge/image/bottom_ads.jpg",
                "adurl" => "#",
                "most_num_times" => 1,
                "daysharenum" => 1,
                "mode" => 0,
                "sharelotterynum" => 1,
                'copyright' => '',
                'isneedfollow' => 1,
                'copyrighturl' => '',
                "share_title" => "欢迎参加七夕走鹊桥",
                "share_desc" => "亲，欢迎参加七夕走鹊桥活动，祝您好运哦！！ "
            );
        }
        include $this->template('form');
    }
    public function fieldsFormValidate($rid = 0)
    {
        return '';
    }
    public function fieldsFormSubmit($rid)
    {
        global $_GPC, $_W;
        load()->func('tpl');
        $id     = intval($_GPC['reply_id']);
        $insert = array(
            'rid' => $rid,
            'weid' => $_W['uniacid'],
            'title' => trim($_GPC['title']),
            'content' => trim($_GPC['content']),
            'description' => trim($_GPC['description']),
            'rule' => trim($_GPC['rule']),
            'award' => trim($_GPC['award']),
            'end_theme' => $_GPC['end_theme'],
            'end_instruction' => $_GPC['end_instruction'],
            'number_times' => intval($_GPC['number_times']),
            'most_num_times' => intval($_GPC['most_num_times']),
            'daysharenum' => intval($_GPC['daysharenum']),
            'starttime' => strtotime($_GPC['datelimit']['start']),
            'endtime' => strtotime($_GPC['datelimit']['end']),
            'dateline' => TIMESTAMP,
            'copyright' => $_GPC['copyright'],
            'copyrighturl' => $_GPC['copyrighturl'],
            "gametime" => intval($_GPC['gametime']),
            "gamelevel" => intval($_GPC['gamelevel']),
            "number_times" => intval($_GPC['number_times']),
            "showusernum" => intval($_GPC['showusernum']),
            "most_num_times" => intval($_GPC['most_num_times']),
            "daysharenum" => intval($_GPC['daysharenum']),
            "mode" => intval($_GPC['mode']),
            "isneedfollow" => intval($_GPC['isneedfollow']),
            "sharelotterynum" => intval($_GPC['sharelotterynum']),
            'share_title' => $_GPC['share_title'],
            'share_desc' => $_GPC['share_desc'],
            'share_url' => $_GPC['share_url'],
            'follow_url' => $_GPC['follow_url'],
            'adurl' => $_GPC['adurl'],
            'awardtip' => $_GPC['awardtip']
        );
        if (!empty($_GPC['start_picurl'])) {
            $insert['start_picurl'] = $_GPC['start_picurl'];
        }
        if (!empty($_GPC['end_picurl'])) {
            $insert['end_picurl'] = $_GPC['end_picurl'];
        }
        if (!empty($_GPC['share_image'])) {
            $insert['share_image'] = $_GPC['share_image'];
        }
        if (!empty($_GPC['cover'])) {
            $insert['cover'] = $_GPC['cover'];
        }
        if (!empty($_GPC['ad'])) {
            $insert['ad'] = $_GPC['ad'];
        }
        if (!empty($_GPC['bg'])) {
            $insert['bg'] = $_GPC['bg'];
        }
        if (!empty($_GPC['btn_start'])) {
            $insert['btn_start'] = $_GPC['btn_start'];
        }
        if (!empty($_GPC['game_page_bg'])) {
            $insert['game_page_bg'] = $_GPC['game_page_bg'];
        }
        if (!empty($_GPC['result_page_bg'])) {
            $insert['result_page_bg'] = $_GPC['result_page_bg'];
        }
        if (!empty($_GPC['game_tile'])) {
            $insert['game_tile'] = $_GPC['game_tile'];
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
            pdo_update($this->tablename, $insert, array(
                'id' => $id
            ));
        }
        if (!empty($_GPC['prizename'])) {
            foreach ($_GPC['prizename'] as $index => $prizename) {
                if (empty($prizename)) {
                    continue;
                }
                $insertprize = array(
                    'rid' => $rid,
                    'weid' => $_W['uniacid'],
                    'prizetype' => $_GPC['prizetype'][$index],
                    'prizename' => $_GPC['prizename'][$index],
                    'prizetotal' => $_GPC['prizetotal'][$index],
                    'prizepic' => $_GPC['prizepic'][$index],
                    'dateline' => TIMESTAMP
                );
                pdo_update($this->tableaward, $insertprize, array(
                    'id' => $index
                ));
            }
        }
        if (!empty($_GPC['prizename_new']) && count($_GPC['prizename_new']) > 1) {
            foreach ($_GPC['prizename_new'] as $index => $credit_type) {
                if (empty($credit_type) || $index == 0) {
                    continue;
                }
                $insertprize = array(
                    'rid' => $rid,
                    'weid' => $_W['uniacid'],
                    'prizetype' => $_GPC['prizetype_new'][$index],
                    'prizename' => $_GPC['prizename_new'][$index],
                    'prizetotal' => $_GPC['prizetotal_new'][$index],
                    'prizepic' => $_GPC['prizepic_new'][$index]
                );
                pdo_insert($this->tableaward, $insertprize);
            }
        }
        return true;
    }
    public function ruleDeleted($rid)
    {
        pdo_delete('weisrc_magpiebridge_reply', array(
            'rid' => $rid
        ));
        pdo_delete('weisrc_magpiebridge_fans', array(
            'rid' => $rid
        ));
        pdo_delete('weisrc_magpiebridge_record', array(
            'rid' => $rid
        ));
    }
    public function settingsDisplay($settings)
    {
        global $_GPC, $_W;
    }
}