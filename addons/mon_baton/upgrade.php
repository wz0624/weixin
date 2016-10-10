<?php


if (!pdo_fieldexists('mon_baton_user', 'baton_num')) {
    pdo_query("ALTER TABLE " . tablename('mon_baton_user') . " ADD  `baton_num` int(10) default 0 ;");

}

if (pdo_fieldexists('mon_baton_user', 'baton_num')) {
   // pdo_query("ALTER TABLE " . tablename('mon_baton_user') . " drop index `baton_num`  ;");

}




if (!pdo_fieldexists('mon_baton', 'speak')) {
    pdo_query("ALTER TABLE " . tablename('mon_baton') . " ADD `speak` varchar(1000);");

}


if (!pdo_fieldexists('mon_baton', 'follow_dialog_tip')) {
    pdo_query("ALTER TABLE " . tablename('mon_baton') . " ADD `follow_dialog_tip` varchar(500);");

}
if (!pdo_fieldexists('mon_baton', 'follow_btn')) {
    pdo_query("ALTER TABLE " . tablename('mon_baton') . " ADD `follow_btn` varchar(50);");

}
