<?php


 if(!pdo_fieldexists('fm_photosvote_voteer', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_voteer')." ADD `jifen` int(10) unsigned NOT NULL;");

}

 if(!pdo_fieldexists('fm_photosvote_voteer', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_voteer')." ADD `lasttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间';");

}

 if(!pdo_fieldexists('fm_photosvote_reply_vote', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_vote')." ADD `codekeykey` varchar(255) NOT NULL COMMENT '验证码key';");

}


 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `photosvote` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启diy';");

}


 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `tuser` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启diy';");

}

 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `paihang` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启diy';");

}

 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `reg` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启diy';");

}

 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `des` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启diy';");

}

 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `tags` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启diy';");

}
  
 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `other` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启diy';");

}
  
 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `rbody_photosvote` text NOT NULL COMMENT '内容';");

}
  
 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `rbody_tuser` text NOT NULL COMMENT '内容';");

}
  
 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `rbody_paihang` text NOT NULL COMMENT '内容';");

}

 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `rbody_reg` text NOT NULL COMMENT '内容';");

}  
  
 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `rbody_des` text NOT NULL COMMENT '内容';");

}
  
 if(!pdo_fieldexists('fm_photosvote_reply_body', 'comment')) {
pdo_query("ALTER TABLE ".tablename('fm_photosvote_reply_body')." ADD `rbody_tags` text NOT NULL COMMENT '内容';");

}
  
