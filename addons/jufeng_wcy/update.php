<?php

if(!pdo_fieldexists('jufeng_wcy_sms', 'is_sms')) {
	pdo_query("ALTER TABLE ".tablename('jufeng_wcy_sms')." ADD `is_sms` int(10) NOT NULL DEFAULT '0' COMMENT '是否开启全局短信';");
}
if(!pdo_fieldexists('jufeng_wcy_sms', 'sms_id')) {
	pdo_query("ALTER TABLE ".tablename('jufeng_wcy_sms')." ADD `sms_id` varchar(20) NOT NULL COMMENT '短信模板ID';");
}
