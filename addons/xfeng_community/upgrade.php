<?php

if(!pdo_fieldexists('xcommunity_goods', 'businesstime')) {
  pdo_query("ALTER TABLE ".tablename('xcommunity_goods')." add `businesstime` varchar(50) DEFAULT NULL;");
}

if(!pdo_fieldexists('xcommunity_goods', 'parent')) {
  pdo_query("ALTER TABLE ".tablename('xcommunity_goods')." add `parent` varchar(20) DEFAULT NULL;");
}

if(!pdo_fieldexists('xcommunity_goods', 'child')) {
  pdo_query("ALTER TABLE ".tablename('xcommunity_goods')." add `child` varchar(20) DEFAULT NULL;");
}

