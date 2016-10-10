<?php
$upgradeSql = <<<sql
        ALTER TABLE `ims_zjl_mass_custom_msg_thread_cache` CHANGE `options` `options` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
sql;
$row = pdo_run($upgradeSql);
if (!pdo_fieldexists('zjl_mass_custom_msg_thread_cache', 'thread_index')) {
    $upgradeSql = <<<sql
        ALTER TABLE `ims_zjl_mass_custom_msg_thread_cache` ADD `thread_index` INT NOT NULL DEFAULT '0';
sql;
    $row = pdo_run($upgradeSql);
}

if (!pdo_fieldexists('zjl_mass_custom_msg_options', 'uniacid')) {
    $upgradeSql = <<<sql
        ALTER TABLE `ims_zjl_mass_custom_msg_options` ADD `uniacid` INT NOT NULL DEFAULT '0' AFTER `id`;
sql;
    $row = pdo_run($upgradeSql);
}


