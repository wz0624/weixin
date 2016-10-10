
<?php
global $_W, $_GPC;
function getIpAddress() {
    $zym_var_2 = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js");
    $zym_var_3 = explode("=", $zym_var_2);
    $zym_var_1 = substr($zym_var_3[1], 0, -1);
    return $zym_var_1;
}
$areaok = false;
load()->func("tpl");
$aid = intval($_GPC["aid"]);
if (!empty($aid)) {
    $article = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_article") . " WHERE id=:aid and article_state=1 and uniacid=:uniacid limit 1 ", array(
        ":aid" => $aid,
        ":uniacid" => $_W["uniacid"]
    ));
    if (!empty($article["article_areas"])) {
        $address = json_decode(getIpAddress() , true);
        $areas = explode(",", $article["article_areas"]);
        foreach ($areas as $a) {
            if (trim($a) == $address["province"] || trim($a) == $address["city"]) {
                $areaok = true;
                break;
            }
        }
        if (!$areaok) {
            include $this->template("index");
            exit;
        }
    } else {
        $areaok = true;
    }
    if (!empty($article)) {
        $article["article_content"] = $this->model->mid_replace($article["article_content"]);
        $readnum = intval($article["article_readnum"] + $article["article_readnum_v"]);
        $readnum = $readnum > 100000 ? "100000+" : $readnum;
        $likenum = intval($article["article_likenum"] + $article["article_likenum_v"]);
        $likenum = $likenum > 100000 ? "100000+" : $likenum;
        if (empty($article["article_mp"])) {
            $mp = pdo_fetch("SELECT acid,uniacid,name FROM " . tablename("account_wechats") . " WHERE uniacid=:uniacid limit 1 ", array(
                ":uniacid" => $_W["uniacid"]
            ));
            $article["article_mp"] = $mp["name"];
        }
        $shop = m("common")->getSysset(array(
            "shop",
            "share"
        ));
        if ($_GPC["preview"] == 1) {
            $openid = "fromUser";
        } else {
            $openid = m("user")->getOpenid();
        }
        if (!empty($openid)) {
            $state = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_article_log") . " WHERE openid=:openid and aid=:aid and uniacid=:uniacid limit 1 ", array(
                ":openid" => $openid,
                ":aid" => $article["id"],
                ":uniacid" => $_W["uniacid"]
            ));
            if (empty($state["id"])) {
                $insert = array(
                    "aid" => $aid,
                    "read" => 1,
                    "uniacid" => $_W["uniacid"],
                    "openid" => $openid
                );
                pdo_insert("ewei_shop_article_log", $insert);
                $sid = pdo_insertid();
                pdo_update("ewei_shop_article", array(
                    "article_readnum" => $article["article_readnum"] + 1
                ) , array(
                    "id" => $article["id"]
                ));
            } else {
                $readtime = $article["article_readtime"];
                if ($readtime <= 0) {
                    $readtime = 4;
                }
                if ($state["read"] < $readtime) {
                    pdo_update("ewei_shop_article_log", array(
                        "read" => $state["read"] + 1
                    ) , array(
                        "id" => $state["id"]
                    ));
                    pdo_update("ewei_shop_article", array(
                        "article_readnum" => $article["article_readnum"] + 1
                    ) , array(
                        "id" => $article["id"]
                    ));
                }
            }
        }
        $article["product_advs"] = $article["product_advs"];
        $advs = json_decode($article["product_advs"], true);
        foreach ($advs as $i => & $v) {
            $v["link"] = $this->model->href_replace($v["link"]);
        }
        unset($v);
        $article["product_advs_link"] = $this->model->href_replace($article["product_advs_link"]);
        $article["article_linkurl"] = $this->model->href_replace($article["article_linkurl"]);
        if (!empty($advs)) {
            $advnum = count($advs);
            if ($article["product_advs_type"] == 1) {
                $advrand = 0;
            } elseif ($article["product_advs_type"] == 2) {
                $advrand = rand(0, $advnum - 1);
            } elseif ($article["product_advs_type"] == 3 && $advnum >= 1) {
                $advrand = - 1;
            }
        }
        $myid = m("member")->getMid();
        $shareid = intval($_GPC["shareid"]);
        $this->model->doShare($article, $shareid, $myid);
        $_W["shopshare"] = array(
            "title" => $article["article_title"],
            "imgUrl" => $article["resp_img"],
            "desc" => $article["resp_desc"],
            "link" => $this->createPluginMobileUrl("article", array(
                "aid" => $article["id"],
                "directopenid" => 1,
                "shareid" => $myid
            ))
        );
        if (p("commission")) {
            $set = p("commission")->getSet();
            if (!empty($set["level"])) {
                $member = m("member")->getMember($openid);
                if (!empty($member) && $member["status"] == 1 && $member["isagent"] == 1) {
                    $_W["shopshare"]["link"] = $this->createPluginMobileUrl("article", array(
                        "directopenid" => 1,
                        "aid" => $article["id"],
                        "shareid" => $myid,
                        "mid" => $member["id"]
                    ));
                } else if (!empty($_GPC["mid"])) {
                    $_W["shopshare"]["link"] = $this->createPluginMobileUrl("article", array(
                        "directopenid" => 1,
                        "aid" => $article["id"],
                        "shareid" => $myid,
                        "mid" => $_GPC["mid"]
                    ));
                }
            }
        }
    } else {
        die("没有查询到文章信息！请检查URL后重试！");
    }
} else {
    die("url参数错误！");
}
include $this->template("index"); ?>
