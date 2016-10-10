<?php
global $_W, $_GPC;
$action = $_GPC['action'];
$optionId = $_GPC['oid'];
$tableName = $this->modulename . "_options";
$optionData = pdo_fetch("SELECT * FROM " . tablename($tableName) . " WHERE id= :oid", array(":oid" => $optionId));
if ($action == "send") {
    $threadId = $_GPC['tid'];
    $params = array(
        ":weid" => $optionData['weid'],
        ":tid" => $threadId,
        ":add_time" => $optionData['cache_name'],
        ":option_id" => $optionId,
    );
    $cacheData = pdo_fetch("SELECT * FROM " . tablename("zjl_mass_custom_msg_thread_cache") . " WHERE weid= :weid and tid = :tid and add_time = :add_time and option_id = :option_id", $params);
    $threadIndex = $cacheData['thread_index'] + 1;
    if (empty($cacheData)) {
        exit("未找到该线程缓存,请重新生成");
    }
    $fansList = json_decode($cacheData['options'], true);
    $status = $this->sendCustomMsg($fansList['list'][$cacheData['thread_index']],$optionId); //测试
    $setSql = "thread_index = thread_index + 1 ";
    if ($status['errcode'] == 0) {
        //pdo_update("zjl_mass_custom_msg_thread_cache", array('success_count' => $cacheData['success_count'] + 1), $cacheData);
        $setSql.= ",success_count = success_count + 1 ";
        //pdo_update($tableName, array('success_count' => $optionData['success_count'] + 1), $optionData);
    } else {
        //var_dump($status);
    }

    $updateSql = "update " . tablename('zjl_mass_custom_msg_thread_cache') . " set " . $setSql . " where id=" . $cacheData['id'];
    pdo_run($updateSql);

    if ($threadIndex < $fansList['count']) {
        $targetUrl = $this->createWebUrl("sendProcess", array("action" => "send", "oid" => $optionId, "tid" => $threadId), true);
        $noticeStr = "线程{$threadId}，{$threadIndex}/{$fansList['count']}";
        $hasNext = true;
    } else {
        $noticeStr = "线程{$threadId}，已经发送完毕";
        $hasNext = false;
    }
    ?>
    <html>   
        <head>
            <title>正在发送...（预计发送：<?php echo $optionData['total'] ?>）</title>
            <?php if ($hasNext) {  //测试 ?>
                <meta http-equiv="refresh" content="1;  
                      url=<?php echo $targetUrl; ?>" /> 
                  <?php } ?>
        </head>   
        <body>   
            <?php
            echo $noticeStr;
            //echo time();
            ?>
        </body> 
    </html>  
    <?php
    exit();
}
$threadUrlArray = array();
for ($threadId = 1; $threadId <= intval($optionData['thread_count']); $threadId++) {
    $threadUrlArray[] = $this->createWebUrl("sendProcess", array("action" => "send", "oid" => $optionId, "tid" => $threadId), true);
}
include $this->template('sendProcess');
