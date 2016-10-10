<?php
$sql="
insert into ims_lianhu_school (uniacid,school_name,addtime)values({$_W['uniacid']},'默认学校',1448273256);
alter table ims_lianhu_line add school_id int(11) default 1;	
	alter table ims_lianhu_line add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_appointment add school_id int(11) default 1;
	alter table ims_lianhu_appointment add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_applist add school_id int(11) default 1;
	alter table ims_lianhu_applist add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_log add school_id int(11) default 1;
	alter table ims_lianhu_log add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_scorejilv add school_id int(11) default 1;
	alter table ims_lianhu_scorejilv add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_scorelist add school_id int(11) default 1;
	alter table ims_lianhu_scorelist add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_syllabus add school_id int(11) default 1;
	alter table ims_lianhu_syllabus add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_msg add school_id int(11) default 1;
	alter table ims_lianhu_msg add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_course add school_id int(11) default 1;
	alter table ims_lianhu_course add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_teacher add school_id int(11) default 1;
	alter table ims_lianhu_teacher add uniacid int(11) default {$_W['uniacid']};
	alter table ims_lianhu_teacher add msg_id_str text ;


	alter table ims_lianhu_grade add school_id int(11) default 1;
	alter table ims_lianhu_grade add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_class add school_id int(11) default 1;
	alter table ims_lianhu_class add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_student add school_id int(11) default 1;
	alter table ims_lianhu_student add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_work add school_id int(11) default 1;
	alter table ims_lianhu_work add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_test add school_id int(11) default 1;
	alter table ims_lianhu_test add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_weak add school_id int(11) default 1;
	alter table ims_lianhu_weak add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_jinbu add school_id int(11) default 1;
	alter table ims_lianhu_jinbu add uniacid int(11) default {$_W['uniacid']};

	alter table ims_lianhu_msg_record add school_id int(11) default 1;
	alter table ims_lianhu_msg_record add uniacid int(11) default {$_W['uniacid']};	";
pdo_run($sql);