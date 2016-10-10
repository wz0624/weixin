<?php

$installSql = <<<sql
CREATE TABLE IF NOT EXISTS `ims_zjl_mass_custom_msg_options` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uniacid` INT UNSIGNED NOT NULL,
  `weid` INT UNSIGNED NOT NULL,
  `add_time` INT NOT NULL,
  `type` INT UNSIGNED NOT NULL,
  `options` TEXT NOT NULL,
  `success_count` INT NOT NULL,
  `total` INT NOT NULL,
  `cache_name` VARCHAR(45) NOT NULL,
  `thread_count` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
		
CREATE TABLE IF NOT EXISTS `ims_zjl_mass_custom_msg_thread_cache` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `weid` INT UNSIGNED NOT NULL,
  `tid` INT UNSIGNED NOT NULL,
  `add_time` INT NOT NULL,
  `option_id` INT UNSIGNED NOT NULL,
  `options` LONGTEXT  NOT NULL,
  `success_count` INT NOT NULL DEFAULT 0,
  `total` INT NOT NULL,
  `thread_index` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
sql;
$row = pdo_run($installSql);