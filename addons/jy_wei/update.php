<?php

if(!pdo_fieldexists('jy_wei_company', 'tel')) {
	pdo_query("ALTER TABLE ".tablename('jy_wei_company')." ADD `tel` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('jy_wei_company', 'address')) {
	pdo_query("ALTER TABLE ".tablename('jy_wei_company')." ADD `address` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('jy_wei_company', 'mail')) {
	pdo_query("ALTER TABLE ".tablename('jy_wei_company')." ADD `mail` varchar(200) NOT NULL;");
}

if(!pdo_fieldexists('jy_wei_company', 'qrcode')) {
	pdo_query("ALTER TABLE ".tablename('jy_wei_company')." ADD `qrcode` varchar(255) NOT NULL;");
}
if(!pdo_fieldexists('jy_wei_company', 'lng')) {
	pdo_query("ALTER TABLE ".tablename('jy_wei_company')." ADD `lng` varchar(10) NOT NULL;");
}
if(!pdo_fieldexists('jy_wei_company', 'lat')) {
	pdo_query("ALTER TABLE ".tablename('jy_wei_company')." ADD `lat` varchar(10) NOT NULL;");
}
