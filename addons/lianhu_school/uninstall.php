<?php
defined('IN_IA') or exit('Access Denied');

$sql="
DROP TABLE IF EXISTS ims_lianhu_teacher ;
DROP TABLE IF EXISTS ims_lianhu_grade ;
DROP TABLE IF EXISTS ims_lianhu_class ;
DROP TABLE IF EXISTS ims_lianhu_student ;
DROP TABLE IF EXISTS ims_lianhu_work ;
DROP TABLE IF EXISTS ims_lianhu_test ;
DROP TABLE IF EXISTS ims_lianhu_weak ;
";
pdo_run($sql);