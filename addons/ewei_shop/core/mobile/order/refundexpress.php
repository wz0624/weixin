<?php
if (!defined("IN_IA")) {
    print("Access Denied");
}
global $_W, $_GPC;
function sortByTime($zym_var_7, $zym_var_8)
{
    if ($zym_var_7["ts"] == $zym_var_8["ts"]) {
        return 0;
    } else {
        return $zym_var_7["ts"] > $zym_var_8["ts"] ? 1 : -1;
    }
}
function getList($zym_var_6, $zym_var_4)
{
    $zym_var_1 = "http://wap.kuaidi100.com/wap_result.jsp?rand=" . time() . "&id={$zym_var_6}&fromWeb=null&postid={$zym_var_4}";
    load()->func("communication");
    $zym_var_2 = ihttp_request($zym_var_1);
    $zym_var_5 = $zym_var_2["content"];
    if (empty($zym_var_5)) {
        return array();
    }
    preg_match_all("/\<p\>&middot;(.*)\<\/p\>/U", $zym_var_5, $zym_var_3);
    if (!isset($zym_var_3[1])) {
        return false;
    }
    return $zym_var_3[1];
}
$operation = !empty($_GPC["op"]) ? $_GPC["op"] : "display";
$openid    = m("user")->getOpenid();
$uniacid   = $_W["uniacid"];
$orderid   = intval($_GPC["id"]);
if ($_W["isajax"]) {
    if ($operation == "display") {
        $order = pdo_fetch("select refundid from " . tablename("ewei_shop_order") . " where id=:id and uniacid=:uniacid and openid=:openid limit 1", array(
            ":id" => $orderid,
            ":uniacid" => $uniacid,
            ":openid" => $openid
        ));
        if (empty($order)) {
            show_json(0);
        }
        $refundid = $order["refundid"];
        $refund   = pdo_fetch("select * from " . tablename("ewei_shop_order_refund") . " where id=:id and uniacid=:uniacid  limit 1", array(
            ":id" => $refundid,
            ":uniacid" => $uniacid
        ));
        $set      = set_medias(m("common")->getSysset("shop"), "logo");
        show_json(1, array(
            "order" => $order,
            "refund" => $refund,
            "set" => $set
        ));
    } else if ($operation == "step") {
        $express   = trim($_GPC["express"]);
        $expresssn = trim($_GPC["expresssn"]);
        $arr       = getList($express, $expresssn);
        if (!$arr) {
            $arr = getList($express, $expresssn);
            if (!$arr) {
                show_json(1, array(
                    "list" => array()
                ));
            }
        }
        $len   = count($arr);
        $step1 = explode("<br />", str_replace("&middot;", "", $arr[0]));
        $step2 = explode("<br />", str_replace("&middot;", "", $arr[$len - 1]));
        for ($i = 0; $i < $len; $i++) {
            if (strtotime(trim($step1[0])) > strtotime(trim($step2[0]))) {
                $row = $arr[$i];
            } else {
                $row = $arr[$len - $i - 1];
            }
            $step   = explode("<br />", str_replace("&middot;", "", $row));
            $list[] = array(
                "time" => trim($step[0]),
                "step" => trim($step[1]),
                "ts" => strtotime(trim($step[0]))
            );
        }
        show_json(1, array(
            "list" => $list
        ));
    }
}
include $this->template("order/refundexpress");
?>