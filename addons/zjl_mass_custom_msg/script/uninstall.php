<?php

$uninstallSql = <<<uninstallSql
    DROP TABLE IF EXISTS `ims_zjl_mass_custom_msg_options`;
    DROP TABLE IF EXISTS `ims_zjl_mass_custom_msg_thread_cache`;
uninstallSql;
$row = pdo_run($uninstallSql);
