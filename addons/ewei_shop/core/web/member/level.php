<?php
if (!defined("IN_IA")) {
    print ("Access Denied");
}
global $_W, $_GPC;
$operation = !empty($_GPC["op"]) ? $_GPC["op"] : "display";
$setdata = pdo_fetch("select * from " . tablename("ewei_shop_sysset") . " where uniacid=:uniacid limit 1", array(
    ":uniacid" => $_W["uniacid"]
));
$set = iunserializer($setdata["sets"]);
$shopset = $set["shop"];
if ($operation == "display") {
    ca("member.level.view");
    $default = array(
        "id" => "default",
        "levelname" => empty($set["shop"]["levelname"]) ? "普通等级" : $set["shop"]["levelname"],
        "discount" => $set["shop"]["leveldiscount"],
        "ordermoney" => 0,
        "ordercount" => 0
    );
    $others = pdo_fetchall("SELECT * FROM " . tablename("ewei_shop_member_level") . " WHERE uniacid = '{$_W["uniacid"]}' ORDER BY level asc");
    $list = array_merge(array(
        $default
    ) , $others);
} elseif ($operation == "post") {
    $id = trim($_GPC["id"]);
    if (empty($id)) {
        ca("member.level.add");
    } else {
        ca("member.level.edit|member.level.view");
    }
    if ($id == "default") {
        $level = array(
            "id" => "default",
            "levelname" => empty($set["shop"]["levelname"]) ? "普通等级" : $set["shop"]["levelname"],
            "discount" => $set["shop"]["leveldiscount"],
            "ordermoney" => 0,
            "ordercount" => 0
        );
    } else {
        $level = pdo_fetch("SELECT * FROM " . tablename("ewei_shop_member_level") . " WHERE id=:id and uniacid=:uniacid limit 1", array(
            ":uniacid" => $_W["uniacid"],
            ":id" => intval($id)
        ));
    }
    $level_array = array();
    for ($i = 0; $i < 101; $i++) {
        $level_array[$i] = $i;
    }
    if (checksubmit("submit")) {
        if (empty($_GPC["levelname"])) {
            message("抱歉，请输入分类名称！");
        }
        $data = array(
            "uniacid" => $_W["uniacid"],
            "level" => intval($_GPC["level"]) ,
            "levelname" => trim($_GPC["levelname"]) ,
            "ordercount" => intval($_GPC["ordercount"]) ,
            "ordermoney" => $_GPC["ordermoney"],
            "discount" => $_GPC["discount"]
        );
        if (!empty($id)) {
            if ($id == "default") {
                $updatecontent = "<br/>等级名称: {$set["shop"]["levelname"]}->{$data["levelname"]}" . "<br/>折扣: {$set["shop"]["leveldiscount"]}->{$data["discount"]}";
                $set["shop"]["levelname"] = $data["levelname"];
                $set["shop"]["leveldiscount"] = $data["discount"];
                $data = array(
                    "uniacid" => $_W["uniacid"],
                    "sets" => iserializer($set)
                );
                if (empty($setdata)) {
                    pdo_insert("ewei_shop_sysset", $data);
                } else {
                    pdo_update("ewei_shop_sysset", $data, array(
                        "uniacid" => $_W["uniacid"]
                    ));
                }
                $setdata = pdo_fetch("select * from " . tablename("ewei_shop_sysset") . " where uniacid=:uniacid limit 1", array(
                    ":uniacid" => $_W["uniacid"]
                ));
                m("cache")->set("sysset", $setdata);
                plog("commission.level.edit", "修改会员默认等级" . $updatecontent);
            } else {
                $updatecontent = "<br/>等级名称: {$level["levelname"]}->{$data["levelname"]}" . "<br/>折扣: {$level["leveldiscount"]}->{$data["discount"]}";
                pdo_update("ewei_shop_member_level", $data, array(
                    "id" => $id,
                    "uniacid" => $_W["uniacid"]
                ));
                plog("member.level.edit", "修改会员等级 ID: {$id}" . $updatecontent);
            }
        } else {
            pdo_insert("ewei_shop_member_level", $data);
            $id = pdo_insertid();
            plog("member.level.add", "添加会员等级 ID: {$id}");
        }
        message("更新等级成功！", $this->createWebUrl("member/level", array(
            "op" => "display"
        )) , "success");
    }
} elseif ($operation == "delete") {
    ca("member.level.delete");
    $id = intval($_GPC["id"]);
    $level = pdo_fetch("SELECT id,levelname FROM " . tablename("ewei_shop_member_level") . " WHERE id = '$id'");
    if (empty($level)) {
        message("抱歉，等级不存在或是已经被删除！", $this->createWebUrl("member/level", array(
            "op" => "display"
        )) , "error");
    }
    pdo_delete("ewei_shop_member_level", array(
        "id" => $id,
        "uniacid" => $_W["uniacid"]
    ));
    plog("member.level.delete", "删除会员等级 ID: {$id} 等级名称: {$level["levelname"]}");
    message("等级删除成功！", $this->createWebUrl("member/level", array(
        "op" => "display"
    )) , "success");
}
load()->func("tpl");
include $this->template("web/member/level"); 
?>
