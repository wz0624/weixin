<?php


 if(!pdo_fieldexists('han_sheka_zhufu', 'font_size')) {
	pdo_query("ALTER TABLE ".tablename('han_sheka_zhufu')." ADD   `font_size` int(3) NOT NULL DEFAULT '12';");
}
 if(!pdo_fieldexists('han_sheka_reply', 'f_logo')) {
	pdo_query("ALTER TABLE ".tablename('han_sheka_reply')." ADD   `f_logo` varchar(255) NOT NULL;");
}

