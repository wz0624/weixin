<?php
function search_companyall(){
	global $_W, $_GPC;
	$weid=$_W['uniacid'];
	$companyall = pdo_fetchall("SELECT id,short,title FROM".tablename('amouse_weijob_company')." WHERE weid = '$weid' ORDER BY companyorder DESC");
	return $companyall;
}

function search_company($id=''){
	global $_W, $_GPC;
	$weid=$_W['uniacid'];
	$from_user = $_W['fans']['from_user'];
	if(!empty($id)){
		$company = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_company')." WHERE id = '$id' AND weid = $weid");
	}elseif(empty($id) && !empty($from_user)){
		$company = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_company')." WHERE weid = :weid AND from_user = :from_user", array(':from_user' => $from_user, ':weid' => $weid));
	}
	return $company;
}
function search_joball(){
	global $_W, $_GPC;
	$weid = $_W['uniacid'];
	$jobs = pdo_fetchall("SELECT * FROM ".tablename('amouse_weijob_employ')." WHERE weid = $weid ORDER BY employorder DESC limit 0,10");
	return $jobs;
}
function search_job($id){
	global $_W, $_GPC;
	$weid = $_W['uniacid'];
	$job = pdo_fetch("SELECT * FROM ".tablename('amouse_weijob_employ')." WHERE id = $id");
	return $job;
}
function search_company_jobs($id){
	global $_W, $_GPC;
	$weid = $_W['uniacid'];
	$jobs = pdo_fetchall("SELECT * FROM".tablename('amouse_weijob_employ')." WHERE companyid = $id ORDER BY employorder ASC");
	return $jobs;
}

function get_tjgl($id,$from_user){
	global $_W, $_GPC;
	$result = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_resume_recod')."WHERE jobid =$id AND from_user = '$from_user'");
	return $result;
}

function get_tjglall(){
	global $_W, $_GPC;
	$weid = $_W['uniacid'];
	$result = pdo_fetchall("SELECT * FROM".tablename('amouse_weijob_resume_recod')."WHERE weid = $weid");
	return $result;
}

function search_cv($id,$from_user){
	global $_W, $_GPC;
	if(!empty($id) && empty($from_user)){
		$cv = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_resume')."WHERE id = $id");
	}elseif(!empty($from_user) && empty($id)){
		$cv = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_resume')."WHERE from_user = '$from_user'");
	}
	return $cv;
}

function search_cover(){
	global $_W, $_GPC;
	$weid = $_W['uniacid'];
	$module = 'amouse_weijob';
	$do = 'index';
	$cover = pdo_fetch("SELECT * FROM ".tablename('cover_reply')."WHERE uniacid= :weid AND do = :do AND module = :module", array(':weid' => $weid, ':do' => $do, ':module' => $module));
	return $cover;
}
?>