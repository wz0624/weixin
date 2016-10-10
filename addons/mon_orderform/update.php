<?php
if(!pdo_fieldexists('mon_orderform_item', 'ipreview_pg')) {pdo_query("ALTER TABLE ".tablename('mon_orderform_item')."  ADD `ipreview_pg` varchar(500) DEFAULT NULL;");}
