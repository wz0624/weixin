
<?php
if (!defined("IN_IA")) {
    print ("Access Denied");
}
if (!class_exists("ArticleModel")) {
    class ArticleModel extends PluginModel {
        public function doShare($zym_var_19, $zym_var_22, $zym_var_23) {
            global $_W, $_GPC;
            if (empty($zym_var_19) || empty($zym_var_22) || empty($zym_var_23) || $zym_var_22 == $zym_var_23) {
                return;
            }
            $zym_var_21 = m("member")->getMember($zym_var_22);
            $zym_var_20 = m("member")->getMember($zym_var_23);
            if (empty($zym_var_20) || empty($zym_var_21)) {
                return;
            }
            $zym_var_18 = m("common")->getSysset("shop");
            $zym_var_25 = intval($zym_var_19["article_rule_credit"]);
            $zym_var_24 = floatval($zym_var_19["article_rule_money"]);
            $zym_var_26 = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_article_share") . " WHERE aid=:aid and click_user=:click_user and uniacid=:uniacid ", array(
                ":aid" => $zym_var_19["id"],
                ":click_user" => $zym_var_23,
                ":uniacid" => $_W["uniacid"]
            ));
            if (!empty($zym_var_26)) {
                $zym_var_25 = intval($zym_var_19["article_rule_credit2"]);
                $zym_var_24 = floatval($zym_var_19["article_rule_money2"]);
            }
            if (!empty($zym_var_19["article_hasendtime"]) && time() > $zym_var_19["article_endtime"]) {
                return;
            }
            $zym_var_31 = $zym_var_19["article_readtime"];
            if ($zym_var_31 <= 0) {
                $zym_var_31 = 4;
            }
            $zym_var_30 = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_article_share") . " WHERE aid=:aid and share_user=:share_user and click_user=:click_user and uniacid=:uniacid ", array(
                ":aid" => $zym_var_19["id"],
                ":share_user" => $zym_var_22,
                ":click_user" => $zym_var_23,
                ":uniacid" => $_W["uniacid"]
            ));
            if ($zym_var_30 >= $zym_var_31) {
                return;
            }
            $zym_var_29 = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_article_share") . " WHERE aid=:aid and share_user=:share_user and uniacid=:uniacid ", array(
                ":aid" => $zym_var_19["id"],
                ":share_user" => $zym_var_22,
                ":uniacid" => $_W["uniacid"]
            ));
            if ($zym_var_29 >= $zym_var_19["article_rule_allnum"]) {
                $zym_var_25 = 0;
                $zym_var_24 = 0;
            } else {
                $zym_var_27 = mktime(0, 0, 0, date("m") , date("d") , date("Y"));
                $zym_var_28 = mktime(0, 0, 0, date("m") , date("d") + 1, date("Y")) - 1;
                $zym_var_17 = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_article_share") . " WHERE aid=:aid and share_user=:share_user and click_date>:day_start and click_date<:day_end and uniacid=:uniacid ", array(
                    ":aid" => $zym_var_19["id"],
                    ":share_user" => $zym_var_22,
                    ":day_start" => $zym_var_27,
                    ":day_end" => $zym_var_28,
                    ":uniacid" => $_W["uniacid"]
                ));
                if ($zym_var_17 >= $zym_var_19["article_rule_daynum"]) {
                    $zym_var_25 = 0;
                    $zym_var_24 = 0;
                }
            }
            $zym_var_16 = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename("ewei_shop_article_share") . " WHERE aid=:aid and share_user=:click_user and click_user=:share_user and uniacid=:uniacid ", array(
                ":aid" => $zym_var_19["id"],
                ":share_user" => $zym_var_22,
                ":click_user" => $zym_var_23,
                ":uniacid" => $_W["uniacid"]
            ));
            if (!empty($zym_var_16)) {
                return;
            }
            if ($zym_var_19["article_rule_credittotal"] > 0 || $zym_var_19["article_rule_moneytotal"] > 0) {
                $zym_var_6 = 0;
                $zym_var_7 = 0;
                $zym_var_5 = pdo_fetchcolumn("select count(distinct click_user) from " . tablename("ewei_shop_article_share") . " where aid=:aid and uniacid=:uniacid limit 1", array(
                    ":aid" => $zym_var_19["id"],
                    ":uniacid" => $_W["uniacid"]
                ));
                $zym_var_4 = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_article_share") . " where aid=:aid and uniacid=:uniacid limit 1", array(
                    ":aid" => $zym_var_19["id"],
                    ":uniacid" => $_W["uniacid"]
                ));
                $zym_var_1 = $zym_var_4 - $zym_var_5;
                if ($zym_var_19["article_rule_credittotal"] > 0) {
                    $zym_var_6 = $zym_var_19["article_rule_credittotal"] - ($zym_var_5 + $zym_var_19["article_readnum_v"]) * $zym_var_19["article_rule_creditm"] - $zym_var_1 * $zym_var_19["article_rule_creditm2"];
                }
                if ($zym_var_19["article_rule_moneytotal"] > 0) {
                    $zym_var_7 = $zym_var_19["article_rule_moneytotal"] - ($zym_var_5 + $zym_var_19["article_readnum_v"]) * $zym_var_19["article_rule_moneym"] - $zym_var_1 * $zym_var_19["article_rule_moneym2"];
                }
                $zym_var_6 <= 0 && $zym_var_6 = 0;
                $zym_var_7 <= 0 && $zym_var_7 = 0;
                if ($zym_var_6 <= 0) {
                    $zym_var_25 = 0;
                }
                if ($zym_var_7 <= 0) {
                    $zym_var_24 = 0;
                }
            }
            $zym_var_2 = array(
                "aid" => $zym_var_19["id"],
                "share_user" => $zym_var_22,
                "click_user" => $zym_var_23,
                "click_date" => time() ,
                "add_credit" => $zym_var_25,
                "add_money" => $zym_var_24,
                "uniacid" => $_W["uniacid"]
            );
            pdo_insert("ewei_shop_article_share", $zym_var_2);
            if ($zym_var_25 > 0) {
                m("member")->setCredit($zym_var_21["openid"], "credit1", $zym_var_25, array(
                    0,
                    $zym_var_18["name"] . " 文章营销奖励积分"
                ));
            }
            if ($zym_var_24 > 0) {
                m("member")->setCredit($zym_var_21["openid"], "credit2", $zym_var_24, array(
                    0,
                    $zym_var_18["name"] . " 文章营销奖励余额"
                ));
            }
            if ($zym_var_25 > 0 || $zym_var_24 > 0) {
                $zym_var_3 = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_article_sys") . " WHERE uniacid=:uniacid limit 1 ", array(
                    ":uniacid" => $_W["uniacid"]
                ));
                $zym_var_8 = $_W["siteroot"] . "app/index.php?i=" . $_W["uniacid"] . "&c=entry&m=ewei_shop&do=member";
                $zym_var_9 = '';
                if ($zym_var_25 > 0) {
                    $zym_var_9.= $zym_var_25 . "个积分、";
                }
                if ($zym_var_24 > 0) {
                    $zym_var_9.= $zym_var_24 . "元余额";
                }
                $zym_var_14 = array(
                    "first" => array(
                        "value" => "您的奖励已到帐！",
                        "color" => "#4a5077"
                    ) ,
                    "keyword1" => array(
                        "title" => "任务名称",
                        "value" => "分享得奖励",
                        "color" => "#4a5077"
                    ) ,
                    "keyword2" => array(
                        "title" => "通知类型",
                        "value" => "用户通过您的分享进入文章《" . $zym_var_19["article_title"] . "》，系统奖励您" . $zym_var_9 . "。",
                        "color" => "#4a5077"
                    ) ,
                    "remark" => array(
                        "value" => "奖励已发放成功，请到会员中心查看。",
                        "color" => "#4a5077"
                    )
                );
                if (!empty($zym_var_3["article_message"])) {
                    m("message")->sendTplNotice($zym_var_21["openid"], $zym_var_3["article_message"], $zym_var_14, $zym_var_8);
                } else {
                    m("message")->sendCustomNotice($zym_var_21["openid"], $zym_var_14, $zym_var_8);
                }
            }
        }
        function mid_replace($zym_var_15) {
            global $_GPC;
            preg_match_all('/href\=[" | ]( . * ?) ["|\']/is', $zym_var_15, $zym_var_13);
            foreach ($zym_var_13[1] as $zym_var_12 => $zym_var_10) {
                $zym_var_11 = $this->href_replace($zym_var_10);
                $zym_var_15 = str_replace($zym_var_13[0][$zym_var_12], "href=\"{$zym_var_11}\"", $zym_var_15);
            }
            return $zym_var_15;
        }
        function href_replace($zym_var_10) {
            global $_GPC;
            $zym_var_11 = $zym_var_10;
            if (strexists($zym_var_10, "ewei_shop") && !strexists($zym_var_10, "&mid")) {
                if (strexists($zym_var_10, "?")) {
                    $zym_var_11 = $zym_var_10 . "&mid=" . intval($_GPC["mid"]);
                } else {
                    $zym_var_11 = $zym_var_10 . "?mid=" . intval($_GPC["mid"]);
                }
            }
            return $zym_var_11;
        }
        function perms() {
            return array(
                "article" => array(
                    "text" => $this->getName() ,
                    "isplugin" => true,
                    "child" => array(
                        "cate" => array(
                            "text" => "分类设置",
                            "addcate" => "添加分类-log",
                            "editcate" => "编辑分类-log",
                            "delcate" => "删除分类-log"
                        ) ,
                        "page" => array(
                            "text" => "文章设置",
                            "add" => "添加文章-log",
                            "edit" => "修改文章-log",
                            "delete" => "删除文章-log",
                            "showdata" => "查看数据统计",
                            "otherset" => "其他设置",
                            "report" => "举报记录"
                        )
                    )
                )
            );
        }
    }
} ?>
