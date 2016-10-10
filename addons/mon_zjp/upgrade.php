<?php




if(!pdo_fieldexists('mon_zjp', 'prize_sharebtn_name')) {
	pdo_query("ALTER TABLE ".tablename('mon_zjp')." ADD `prize_sharebtn_name` varchar(50) ;");
}





if(!pdo_fieldexists('mon_zjp', 'prize_sharebtn_name')) {
    pdo_query("ALTER TABLE ".tablename('mon_zjp')." ADD `luck_sharebtn_name` varchar(50);");
}


if(!pdo_fieldexists('mon_zjp', 'day_play_count')) {
	pdo_query("ALTER TABLE ".tablename('mon_zjp')." ADD `day_play_count` int(3);");
}







