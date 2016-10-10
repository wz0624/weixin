<?php
defined('IN_IA') or exit('Access Denied');

if(!pdo_fieldexists('weisrc_friend_reply', 'sharecount')) {
    pdo_query("ALTER TABLE ".tablename('weisrc_friend_reply')." ADD `sharecount` int(11) DEFAULT '0';");
}