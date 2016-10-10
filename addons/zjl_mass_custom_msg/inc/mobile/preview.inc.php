<?php

global $_W,$_GPC;
checkauth();
$optionId = $_GPC['oid'];
$openid = $_W['openid'];
if ($_W['openid'] == '') {
    message("未获取到粉丝信息", "", "error");
}
$result = $this->sendCustomMsg($openid, $optionId);
if ($result['errcode'] == 0) {
    message("发送成功", "", "success");
} else {
    message("发送失败", "", "error");
}



