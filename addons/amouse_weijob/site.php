<?php
/**
 * 微招聘模块微站定义
 * @author zombieszy QQ:214983937
 * @url  w.mamani.cn
 */
defined('IN_IA') or exit('Access Denied');
class Amouse_weijobModuleSite extends WeModuleSite {

    //Index
	public function doMobileIndex(){
		global $_GPC, $_W;
        require_once "jssdk.php";
		$weid = $_W['uniacid'];
        $followed = !empty($_W['openid']);
        if ($followed) {
            $mf = pdo_fetch("select follow from " . tablename('mc_mapping_fans') . " where openid=:openid limit 1", array(":openid" => $_W['openid']));
            $followed = $mf['follow'] == 1;
        }
        if(!$followed){
            $setting = $this->module['config'];
            if(empty($setting['followurl'])){
                message('您还没有关注，请先关注该公众号：'.$_W['account'][name]);
            }else{
                $followurl = $setting['followurl'];
                header("location:$followurl");
            }
        }
        $openid = empty($_W['openid'])?$_GPC['wid']:$_W['openid'];
        $jobs = pdo_fetchall("SELECT * FROM ".tablename('amouse_weijob_employ')."WHERE weid = $weid and status='1' ORDER BY createtime DESC,hits DESC limit 0,10");
        foreach ($jobs as $k => $v){
            $nowtime = TIMESTAMP;
            $nowtime = $this->get_new_date($nowtime);
            $endtime = $this->get_new_date($v['endtime']);
            $createtime = $this->get_new_date($v['createtime']);
            $company = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_company')."WHERE id = :id",array(':id' => $v['companyid']));
            if($v['type'] == 0){
                $type = '全职';
            }elseif($v['type'] == 1){
                $type = '兼职';
            }elseif($v['type'] == 2){
                $type = '实习';
            }
            $item[] = array(
                'id' => $v['id'],
                'hits' => $v['hits'],
                'jobname' => $v['jobname'],
                'type' => $type,
                'vtype' => $v['type'],
                'offer' => $v['offer'],
                'from_user'=>$v['from_user'],
                'workplace' => $v['workplace'],
                'title' => $company['title'],
                'endtime' => $v['endtime'],
                'createtime' => date("m-d",$v['createtime']),
            );
        }

        $linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('index', array('openid'=>$openid),true);
        $shareimg = toimage($member['headimg']);
		include $this->template('index');
	}

    //求职
    public function  doMobileMyjob(){
        global $_GPC, $_W;
        require_once "jssdk.php";
        $weid = $_W['uniacid'];
        $followed = !empty($_W['openid']);
        if ($followed) {
            $mf = pdo_fetch("select follow from " . tablename('mc_mapping_fans') . " where openid=:openid limit 1", array(":openid" => $_W['openid']));
            $followed = $mf['follow'] == 1;
        }
        if(!$followed){
            $setting = $this->module['config'];
            if(empty($setting['followurl'])){
                message('您还没有关注，请先关注该公众号：'.$_W['account'][name]);
            }else{
                $followurl = $setting['followurl'];
                header("location:$followurl");
            }
        }
        $openid = empty($_W['openid'])?$_GPC['wid']:$_W['openid'];
        $jobs = pdo_fetchall("SELECT * FROM ".tablename('amouse_weijob_job')."WHERE weid = $weid and status='1' ORDER BY createtime DESC,hits DESC limit 0,10");
        foreach ($jobs as $k => $v){
            $nowtime = TIMESTAMP;
            $nowtime = $this->get_new_date($nowtime);
            $endtime = $this->get_new_date($v['endtime']);
            $createtime = $this->get_new_date($v['createtime']);
            $type = '求职';
            $item[] = array(
                'id' => $v['id'],
                'hits' => $v['hits'],
                'name' => $v['name'],
                'type' => $type,
                'from_user'=>$v['from_user'],
                'vtype' =>3,
                'work' => $v['work'],
                'edu' => $v['edu'],
                'endtime' => $v['endtime'],
                'createtime' => date("m-d",$v['createtime']),
            );
        }

        $linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('index', array('openid'=>$openid),true);
        $shareimg = toimage($member['headimg']);
        include $this->template('index');
    }

    /**
     *
     **/
	public function doMobileSearch(){
		global $_GPC, $_W;
        $weid = $_W['uniacid'];
		$keyword = $_GPC['title'];
		$type = $_GPC['type'];
		$page = intval($_GPC['page']);
		$start = ($page-1)*10; 
		$psize = 20;
		$condition = '';
        if($type!=3){
            if (!empty($keyword)) {
                $condition .= " AND jobname LIKE '%{$keyword}%'";
            }
            if($type!=""){
                $condition .= " AND type = $type";
            }
            $list = pdo_fetchall("SELECT * FROM ".tablename('amouse_weijob_employ')."WHERE weid = $weid  and status='1' $condition ORDER BY employorder DESC,createtime DESC,hits DESC limit $start,10");
            if(!empty($list)){
                $i =0;
                foreach($list as $key => $a){
                    $nowtime = TIMESTAMP;
                    $nowtime = $this->get_new_date($nowtime);
                    $endtime = $this->get_new_date($a['endtime']);
                    $createtime = $this->get_new_date($a['createtime']);
                    if($nowtime < $endtime || $endtime==$createtime || $a['endtime'] ==0){
                        $companyid = $a['companyid'];
                        $company = pdo_fetch("SELECT title FROM".tablename('amouse_weijob_company')."WHERE id = $companyid");
                        $data['status'] = 1;
                        $data['data'][$i] = array(
                            'id' => $a['id'],
                            'hits' => $a['hits'],
                            'jobname' => $a['jobname'],
                            'type' => $a['type'],
                            'from_user'=>$a['from_user'],
                            'offer' => $a['offer'],
                            'workplace' => $a['workplace'],
                            'createtime' => date("m-d",$a['createtime']),
                            'company_name' => $company['title']
                        );
                        $i++;
                    }
                }
                echo json_encode($data);
            }else{
                $data['status'] = 0;
                echo json_encode($data);
            }
        }else{
            if (!empty($keyword)) {
                $condition .= " AND name LIKE '%{$keyword}%'";
            }
            $list = pdo_fetchall("SELECT * FROM ".tablename('amouse_weijob_job')."WHERE weid = $weid  and status='1' $condition ORDER BY createtime DESC,hits DESC limit $start,10");
            if(!empty($list)){
                $i =0;
                foreach($list as $key => $a){
                    $nowtime = TIMESTAMP;
                    $nowtime = $this->get_new_date($nowtime);
                    $createtime = $this->get_new_date($a['createtime']);
                    $data['status'] = 1;
                    $data['data'][$i] = array(
                        'id' => $a['id'],
                        'hits' => $a['hits'],
                        'name' => $a['name'],
                        'type' => '求职',
                        'from_user'=>$a['from_user'],
                        'work' => $a['work'],
                        'salary' => $a['salary'],
                        'createtime' => date("m-d",$a['createtime']),
                        'edu' =>$a['edu']
                    );
                    $i++;
                }
                echo json_encode($data);
            }else{
                $data['status'] = 0;
                echo json_encode($data);
            }
        }
	}
	
	public function doMobileCompany(){
		global $_GPC, $_W;
        $companyid = intval($_GPC['id']);
		$weid = $_W['uniacid'];
        require_once "jssdk.php";
        $followed = !empty($_W['openid']);
        if ($followed) {
            $mf = pdo_fetch("select follow from " . tablename('mc_mapping_fans') . " where openid=:openid limit 1", array(":openid" => $_W['openid']));
            $followed = $mf['follow'] == 1;
        }
        if(!$followed){
            $setting = $this->module['config'];
            if(empty($setting['followurl'])){
                message('您还没有关注，请先关注该公众号：'.$_W['account'][name]);
            }else{
                $followurl = $setting['followurl'];
                header("location:$followurl");
            }
        }
        $openid = $_W['openid'];
        if(empty($openid)){
            $openid=$_GPC['wid'];
        }
        $company = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_company')."WHERE id = $companyid" );
        $linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('company', array('openid'=>$openid),true);
        $shareimg = !empty($company['thumb']) ? toimage($company['thumb']):'http://'.$_SERVER['HTTP_HOST'].'../addons/amouse_weijob/icon.jpg' ;
		include $this->template('company');
	}

    //
	public function doMobileJob(){
		global $_GPC, $_W;
        require_once "jssdk.php";
		$id = intval($_GPC['id']);
		$weid = $_W['uniacid'];
        $from_user=$_GPC['from_user'];
		$job = pdo_fetch("SELECT hits,endtime,createtime FROM".tablename('amouse_weijob_employ')."WHERE weid=$weid AND id=$id");
		$nowtime = TIMESTAMP;
		$nowtime = $this->get_new_date($nowtime);
		$endtime = $this->get_new_date($job['endtime']);
		$createtime = $this->get_new_date($job['createtime']);
		$hits = intval($job['hits']) + 1;
		pdo_update('amouse_weijob_employ',array('hits'=>$hits),array('id'=>$id));
        $linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('job', array('from_user'=>$from_user,'id'=>$id),true);
		include $this->template('job');
	}

    //求职详情
    public function doMobileApply(){
        global $_GPC, $_W;
        require_once "jssdk.php";
        $id = intval($_GPC['id']);
        $weid = $_W['uniacid'];
        $from_user=$_GPC['from_user'];
        $job = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_job')."WHERE weid=$weid AND id=$id");
        $nowtime = TIMESTAMP;
        $nowtime = $this->get_new_date($nowtime);
        $createtime = $this->get_new_date($job['createtime']);
        $hits = intval($job['hits']) + 1;
        pdo_update('amouse_weijob_job',array('hits'=>$hits),array('id'=>$id));
        $linkUrl = $_W['siteroot'].'app/'.$this->createMobileUrl('apply', array('id'=>$id,'from_user'=>$from_user),true);
        include $this->template('apply');
    }


    //发布求职
    public function doMobilePublishJob(){
        global $_GPC, $_W;
        $from_user = $_GPC['from_user'];
        $id = $_GPC['id'];
        $weid = $_W['uniacid'];
        $item = pdo_fetch("SELECT * FROM ".tablename('amouse_weijob_job')."WHERE from_user = '$from_user'");
        if(!empty($item)){
            $item['age'] && $item['age'] = date('Y-m-d', $item['age']);
        }
        if(checksubmit('submit')){
            $data = array(
                'weid' => $weid,
                'from_user' => $from_user,
                'name' => $_GPC['name'],
                'age' => strtotime($_GPC['age']),
                'sex' => $_GPC['sex'],
                'mobile' => $_GPC['mobile'],
                'work' => $_GPC['work'],
                'edu' => $_GPC['selectEdu'],
                'salary' => $_GPC['salary'],
                'addr' => $_GPC['addr'],
                'description' => $_GPC['description'],
                'createtime' => TIMESTAMP,
            );
            if (empty($id)) {
                $data['status']=0;
                pdo_insert('amouse_weijob_job', $data);
                message('求职信息保存成功！',$this->createMobileUrl('index',array('from_user'=>$from_user)),'success');
            } else {
                unset($data['createtime']);
                pdo_update('amouse_weijob_job', $data, array('id' => $id));
                message('求职信息更新成功！',$this->createMobileUrl('index',array('from_user'=>$from_user)),'success');
            }
        }

        include $this->template('pub_job');

    }

	//地理位置LBS
	public function doMobileLbs(){
		global $_W, $_GPC;
		$weid =  intval($_W['uniacid']);
		$id = $_GPC['id'];
		$sits = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_company')."WHERE id = $id");
		include $this->template('lbs');
	}

	//我的简历
	public function doMobileMyResume(){
		global $_W, $_GPC;
		$weid =  intval($_W['uniacid']);
		$from_user = $_GPC['from_user'];
		$id = pdo_fetch("SELECT id FROM ".tablename('amouse_weijob_resume')."WHERE from_user = '$from_user'");
		$id = $id['id'];
		if(!empty($id)){
			$item = pdo_fetch("SELECT * FROM ".tablename('amouse_weijob_resume')."WHERE id =:id",array(':id' => $id));
            $item['age'] && $item['age'] = date('Y-m-d', $item['age']);
		}
		if(checksubmit('submit')){
			$data = array(
				'weid' => $weid,
				'from_user' => $from_user,
				'name' => $_GPC['person_name'],
				'age' => strtotime($_GPC['person_age']),
				'sex' => $_GPC['person_sex'],
				'major' => $_GPC['person_major'],
				'phone' => $_GPC['person_mobile'],
				'email' => $_GPC['person_email'],
				'home' => $_GPC['person_home'],
				'college' => $_GPC['college'],
				'skill' => $_GPC['person_skill'],
				'status' =>0,
				'self' => $_GPC['person_self'],
				'experience' => $_GPC['person_work'],
				'education' => $_GPC['person_edu'], 
				'job_direction' => $_GPC['person_direction'],
				'createtime' => TIMESTAMP,
            );
            if (empty($id)) {
                pdo_insert('amouse_weijob_resume', $data);
                message('简历保存成功！',$this->createMobileUrl('index',array('wid'=>$from_user)),'success');
            } else {
                unset($data['createtime']);
                pdo_update('amouse_weijob_resume', $data, array('id' => $id));
                message('简历更新成功！',$this->createMobileUrl('index',array('wid'=>$from_user)),'success');
            }
		}
		include $this->template('my_resume');
	}
	
	/*投简记录开始*/
	public function doMobileResumeRecod(){
		global $_W, $_GPC;
		$weid = $_W['uniacid'];
		$from_user = $_GPC['from_user'];
		$tjjl_list = pdo_fetchall("SELECT * FROM".tablename('amouse_weijob_resume_recod')."WHERE weid = $weid AND from_user = '$from_user' ORDER BY createtime DESC limit 0,10"); 
		$i = 0;
		foreach($tjjl_list as $key => $v){
			$jobid = $v['jobid'];
			$job = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_employ')."WHERE id = $jobid  and status='1'");
			$companyid = $job['companyid'];
			$company = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_company')."WHERE id = $companyid" );
			$result[$i] = array(
				'tjjlid' => $v[id],
				'jobid' => $job['id'],
				'jobname' => $job['jobname'],
				'companyid' => $job['companyid'],
				'thumb' => $company['thumb'],
				'company_name' => $company['title'],
				'workplace' => $job['workplace'],
				'offer' => $job['offer'],
				'createtime' => $v['createtime'],
			);
			$i++;
		}
		include $this->template('resume_recod');
	}

	//ajax加载数据
	public function doMobileTjglsearch(){
		global $_GPC, $_W;
		$weid = $_W['uniacid'];
        $from_user = $_GPC['from_user'];
		$page = intval($_GPC['page']); //获取请求的页数  
		$start = ($page-1)*10; 
		$tj = pdo_fetchall("SELECT * FROM ".tablename('amouse_weijob_resume_recod')."WHERE weid = :weid AND from_user = :from_user ORDER BY createtime DESC limit $start,10", array(':weid' => $weid, ':from_user' => $from_user));
		if(!empty($tj)){
			$i = 0;
			foreach ($tj as $k => $v){
				$jobid = $v['jobid'];
				$cvid = $v['cvid'];
				$job = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_employ')."WHERE id = $jobid  and status='1'");
				$companyid = $job['companyid'];
				$company = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_company')."WHERE id = $companyid" );
				$data['status'] = 1;
				$data['data'][$i] = array(
					'id' => $v['id'],
					'jobid' => $jobid,
					'jobname' => $job['jobname'],
					'companyid' => $job['companyid'],
					'thumb' => $company['thumb'],
					'company_name' => $company['title'],
					'workplace' => $job['workplace'],
					'createtime' => $v['createtime'],
				);
				$i++;
			}
		    echo json_encode($data);
		}else{
			$data['status'] = 0;
			echo $data;
		}
	}

    /*信息发布开始*/
    public function doMobilePublish(){
        global $_W, $_GPC;
        $from_user = $_GPC['from_user'];
        $weid = $_W['uniacid'];
        $id = intval($_GPC['id']);
        $companyid = intval($_GPC['companyid']);
        $from = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_company')."WHERE from_user=:from_user AND weid=:weid", array(':from_user' => $from_user, ':weid' => $weid));
        if(!empty($id)){
            $item = pdo_fetch("SELECT * FROM ".tablename('amouse_weijob_employ')."WHERE id = $id  and status='1'");
            $item['endtime'] && $item['endtime'] = date('Y-m-d', $item['endtime']);
        }else{
            $item['endtime'] = date('Y-m-d',time() + 6 * 86400);
        }
        if(checksubmit('submit')){
            if(empty($from)){
                message('请先设置基本信息！',$this->createMobileUrl('companyset',array('from_user'=>$from_user)), 'error');
            }else{
                $data = array(
                'weid' => $weid,
                'companyid' => $companyid,
                'jobname' => $_GPC['jobname'],
                'type' => $_GPC['jobtype'],
                'edu' => $_GPC['edu'],
                'offer' => $_GPC['offer'],
                'number' => $_GPC['number'],
                'workplace' =>$_GPC['workplace'],
                'workyear' => $_GPC['workyear'],
                'description' => $_GPC['description'],
                'status'=>0,
                'endtime' => strtotime($_GPC['endtime']),
                'createtime' => TIMESTAMP,
                );

                if(empty($id)){
                    pdo_insert('amouse_weijob_employ', $data);
                    message('岗位添加成功,请等待审核！',$this->createMobileUrl('mypublish',array('from_user'=>$from_user)),'success');
                }else{
                    pdo_update('amouse_weijob_employ', $data, array('id' => $id));
                    message('岗位更新成功,请等待审核！',$this->createMobileUrl('mypublish',array('from_user'=>$from_user)),'success');
                }
            }
        }
        include $this->template('publish');
    }

    //信息发布记录
    public function doMobileMyPublish(){
        global $_W, $_GPC;
        $weid=$_W['uniacid'];
        $from_user=$_GPC['from_user'];
        $company=pdo_fetch("SELECT id,title FROM".tablename('amouse_weijob_company')."WHERE weid = :weid AND from_user = :from_user",array(':weid'=>$weid,':from_user'=>$from_user));
        $jobs = pdo_fetchall("SELECT * FROM".tablename('amouse_weijob_employ')."WHERE weid = :weid and status='1' AND companyid= :companyid ORDER BY createtime DESC limit 0,10", array(':weid' => $weid, ':companyid' => $company['id']));
        $i = 0;
        foreach($jobs as $k => $v){
            $result[$i] = $v;
            $result[$i]['company_name'] = $company['title'];
            $i++;
        }
        include $this->template('my_publish');
    }

    //信息发布搜索
    public function doMobilePublishsearch(){
        global $_W, $_GPC;
        $weid = $_W['uniacid'];
        $from_user=$_GPC['from_user'];
        $page = intval($_GPC['page']); //获取请求的页数
        $start = ($page-1)*10;
        $company = pdo_fetch("SELECT id,title FROM".tablename('amouse_weijob_company')."WHERE weid = :weid AND from_user = :from_user",array(':weid'=>$weid,':from_user'=>$from_user));
        $jobs = pdo_fetchall("SELECT * FROM".tablename('amouse_weijob_employ')."WHERE weid = :weid and status='1' AND companyid = :companyid ORDER BY createtime DESC limit $start,10",array(':weid'=>$weid,':companyid'=>$company['id']));
        if(!empty($jobs)){
            $i = 0;
            foreach($jobs as $k => $v){
                $data['status'] = 1;
                $data['data'][$i] = array(
                    'company_name' => $company['title'],
                    'thumb' => $company['thumb'],
                    'id' => $v['id'],
                    'offer' => $v['offer'],
                    'jobname' => $v['jobname'],
                );
                $i++;
            }
            echo json_encode($data);
        }else{
            $data['status'] = 0;
            echo $data;
        }
    }

    //删除发布岗位
    public function doMobilePublishdel(){
        global $_W, $_GPC;
        $jobid = $_GPC['jobid'];
        $from_user = $_GPC['from_user'];
        $jobids = explode(",",$jobid );
        $i=0;
        foreach($jobids as $key => $v){
            $res[$i] = pdo_delete('amouse_weijob_employ', array('id' => $v));
            pdo_delete('amouse_weijob_resume_recod',array('jobid' => $v));
            $i++;
        }
    }

    //ajax加载收到的简历
    public function doMobileCvsearch(){
        global $_W, $_GPC;
        $weid = $_W['uniacid'];
        $jobid = $_GPC['jobid'];
        $from_user = $_GPC['from_user'];
        $list = pdo_fetchall("SELECT * FROM".tablename('amouse_weijob_resume_recod')."WHERE weid = $weid AND jobid = $jobid");
        if(!empty($list)){
            $i = 0;
            foreach($list as $key => $a){
                $cvid=$a['cvid'];
                $cvs=pdo_fetch("SELECT * FROM".tablename('amouse_weijob_resume')."WHERE id=$cvid ORDER BY id");
                $resumeRecod=pdo_fetch("SELECT createtime FROM".tablename('amouse_weijob_resume_recod')."WHERE jobid = $jobid AND cvid = $cvid");
                $ct =  date('Y-m-d H:i:s',$resumeRecod['createtime']);
                $data['status'] = 1;
                $data ['data'][$i] = array(
                    'id' => $a['id'],
                    'cvid' => $a['cvid'],
                    'name' => $cvs['name'],
                    'sex' => $cvs['sex'],
                    'age' => date('Y-m-d',$cvs['age']),
                    'phone' => $cvs['phone'],
                    'self' => $cvs['self'],
                    'job_direction' => $cvs['job_direction'],
                    'createtime' => $ct,
                );
                $i++;
            }
            echo json_encode($data);
        }else{
            $status = 0;
            echo $status;
        }
    }

    public function doMobileCompanysearch(){
        global $_W, $_GPC;
        $from_user = $_GPC['from_user'];
        message('请先设置基本信息',$this->createMobileUrl('companyset',array('from_user'=>$from_user)), 'error');
    }

    /*信息发布结束*/
    public function doMobileCompanyset(){
        global $_W, $_GPC;
        $from_user = $_GPC['from_user'];
        load()->func('file');
        load()->func('tpl');
        $weid =  intval($_W['uniacid']);
        $id = intval($_GPC['id']);
        if(!empty($from_user)){
            $item = pdo_fetch("SELECT * FROM ".tablename('amouse_weijob_company')."WHERE from_user = :from_user ",array(':from_user' => $from_user));
        }
        $sign = explode(',',$_GPC['sign']);
        if(checksubmit('submit')){
            $data = array(
                'weid' => $weid,
                'from_user' => $from_user,
                'title' => trim($_GPC['companyname']),
                'short' => trim($_GPC['shortname']),
                'linkman' => trim($_GPC['link_man']),
                'email' => trim($_GPC['company_email']),
                'phone' => trim($_GPC['company_mobile']),
                'tel' => trim($_GPC['company_phone']),
                'qq' => trim($_GPC['company_qq']),
                'address' => trim($_GPC['address']),
                'content' => trim($_GPC['content']),
                'thumb' => trim($_GPC['thumb']),
                'thumb1' => trim($_GPC['thumb1']),
                'lng' => $sign[0],
                'status'=>1,
                'lat' => $sign[1],
                'createtime' => TIMESTAMP,
            );
            if (empty($id)) {
                pdo_insert('amouse_weijob_company', $data);
                message('公司信息保存成功！',$this->createMobileUrl('publish',array('from_user'=>$from_user)),'success');
            } else {
                unset($data['createtime']);
                pdo_update('amouse_weijob_company', $data, array('id' => $id));
                message('公司信息更新成功！',$this->createMobileUrl('publish',array('from_user'=>$from_user)),'success');
            }
        }
        include $this->template('companyset');
    }

    //删除投递记录
	public function doMobileResumeRecodDel(){
		global $_W, $_GPC;
		$tjjlid = $_GPC['tjjlid'];
		$from_user = $_GPC['from_user'];
		$tjjlids = explode(",",$tjjlid );
		$i=0;
		foreach($tjjlids as $key => $v){
			$res[$i] = pdo_delete('amouse_weijob_resume_recod', array('id' => $v));
			$i++;
		}
	}
	/*投简记录结束*/
	
	//投简邮件发送
	public function doMobileMail(){
		global $_W, $_GPC;
		$weid =  intval($_W['uniacid']);
        $from_user = $_GPC['from_user'];
		$myresume = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_resume')."WHERE from_user='$from_user' ");
		if($myresume['sex'] == 0){
			$sex = '男';
		}elseif($myresume['sex'] == 1){
			$sex = '女';
		}
		$jobid = $_GPC['id'];
		$job = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_employ')."WHERE id = $jobid");
		$job_name = $job['jobname'];
		$companyid = $job['companyid'];
		load()->func('communication');
		
		$company = pdo_fetch("SELECT title,email FROM".tablename('amouse_weijob_company')."WHERE id = $companyid");
		 
		$body = "<h3>应聘岗位</h3> <br />";
		$body.='应聘企业：'.$company['title'].'<br>应聘岗位：'.$job_name.'<br>应聘人姓名：'.$myresume['name'].'<br>性别：'.$sex.'<br>年龄：'.$myresume['age'].'<br>手机号码：'.$myresume['phone'].'<br>邮箱：'.$myresume['email'].'<br>自我评价：'.$myresume['self'].'<br>工作经历：'.$myresume['experience'].'<br>教育经历：'.$myresume['education'] ;
		//$send = ihttp_email($company['email'],'招牌简历提示----您有一封应聘邮件，请查收',);
		$send =ihttp_email($company['email'], '招牌简历提示----您有一封应聘邮件，请查收', $body);
		if (is_error($send)) {
			$data = array(
				'weid' => $_W['uniacid'],
				'jobid' => $jobid,
				'cvid' => $myresume['id'],
				'from_user' => $from_user,
				'createtime' => TIMESTAMP,
			);
			pdo_insert('amouse_weijob_resume_recod', $data);
			message('投简成功',$this->createMobileUrl('job',array('id' => $jobid,'from_user' => $from_user)),'success');
		}else{
			$data = array(
				'weid' => $_W['uniacid'],
				'jobid' => $jobid,
				'cvid' => $myresume['id'],
				'from_user' => $from_user,
				'createtime' => TIMESTAMP,
			);
			pdo_insert('amouse_weijob_resume_recod', $data);
			message('投简成功',$this->createMobileUrl('job',array('id'=>$jobid,'from_user'=>$from_user)),'success');
		}
	}

    //============= 后台管理 ==================
	public function doWebQuery() {
		global $_W, $_GPC;
		$kwd = $_GPC['keyword'];
		$sql ='SELECT * FROM '.tablename('amouse_weijob_company').' WHERE `weid`=:weid AND `title` LIKE :title';
		$params = array();
		$params[':weid'] = $_W['uniacid'];
		$params[':title'] = "%{$kwd}%";
		$ds = pdo_fetchall($sql, $params);
		foreach($ds as &$row) {
			$r = array();
			$r['mid'] = $row['id'];
			$r['title'] = $row['title'];
			$r['description'] = $row['content'];
			$r['thumb'] = $row['thumb'];
			$row['entry'] = $r;
		}
		include $this->template('query');
	}
	

	public function get_new_date($date){
		$new_date = date('Y-m-d',$date);
		$new_date = strtotime($new_date);
		return $new_date;
	}


    private function fileUpload($file, $type) {
        global $_W;
        set_time_limit(0);
        $_W['uploadsetting'] = array();
        $_W['uploadsetting']['images']['folder'] = 'image';
        $_W['uploadsetting']['images']['extentions'] = array('jpg', 'png', 'gif');
        $_W['uploadsetting']['images']['limit'] = 50000;
        $result = array();
        $upload = file_upload($file, 'image');
        if (is_error($upload)) {
            message($upload['message'], '', 'ajax');
        }
        $result['url'] = $upload['url'];
        $result['error'] = 0;
        $result['filename'] = $upload['path'];
        return $result;
    }

    public function doMobileUploadImage() {
        global $_W;
        load()->func('file');
        if (empty($_FILES['file']['name'])) {
            $result['message'] = '请选择要上传的文件！';
            exit(json_encode($result));
        }

        if ($file = $this->fileUpload($_FILES['file'], 'image')) {
            if ($file['error']) {
                exit('0');
                //exit(json_encode($file));
            }
            $result['url'] = $_W['config']['upload']['attachdir'] . $file['filename'];
            $result['error'] = 0;
            $result['filename'] = $file['filename'];
            exit(json_encode($result));
        }
    }

    public function doWebCompany() {
        global $_GPC, $_W;
        load()->func('tpl');
        $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($op == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM ".tablename('amouse_weijob_company')."WHERE id = :id",array(':id' => $id));
                if(empty($item)){
                    message('抱歉，该公司不存在或已删除', '', 'error');
                }
            }
            if(checksubmit('submit')){
                if(empty($_GPC['title'])){
                    message('请输入公司名称！');
                }
                if(empty($_GPC['tel'])){
                    message('请输入电话号码！');
                }
                if(empty($_GPC['content'])){
                    message('请填写公司简介，让求职者更清晰的了解公司！');
                }
                if(empty($_GPC['address'])){
                    message('请通过设置坐标来确定公司地址！');
                }
                if(empty($_GPC['email'])){
                    message('请输入公司邮箱，否则应聘信息无法发送！');
                }
                if(empty($_GPC['thumb1'])){
                    message('请上传营业执照！');
                }
                $data = array(
                    'weid' => intval($_W['uniacid']),
                    'short' => $_GPC['short'],
                    'title' => $_GPC['title'],
                    'thumb' => $_GPC['thumb'],
                    'thumb1' => $_GPC['thumb1'],
                    'linkman' => $_GPC['linkman'],
                    'phone' => $_GPC['phone'],
                    'tel' => $_GPC['tel'],
                    'qq' => $_GPC['qq'],
                    'email' => $_GPC['email'],
                    'content' => $_GPC['content'],
                    'companyorder' => $_GPC['companyorder'] ,
                    'province' => $_GPC['district']['province'],
                    'city' => $_GPC['district']['city'],
                    'dist' => $_GPC['district']['district'],
                    'address' => $_GPC['address'],
                    'lng' => $_GPC['baidumap']['lng'],
                    'lat' => $_GPC['baidumap']['lat'],
                    'status'=>1,
                    'createtime' => TIMESTAMP,
                );
                if (empty($id)) {
                    pdo_insert('amouse_weijob_company', $data);
                    message('公司添加成功！', $this->createWebUrl('company', array('op' => 'display')), 'success');
                } else {
                    unset($data['createtime']);
                    pdo_update('amouse_weijob_company', $data, array('id' => $id));
                    message('公司更新成功！', $this->createWebUrl('company', array('op' => 'display')), 'success');
                }
            }
        }elseif($op == 'display'){
            if (checksubmit('submit')) { //排序
                if (!empty($_GPC['displayorder'])) {
                    foreach ($_GPC['displayorder'] as $id => $displayorder) {
                        pdo_update('amouse_weijob_company', array('companyorder' => $displayorder), array('id' => $id));
                    }
                    message('排序更新成功！', 'refresh', 'success');
                }
            }

            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }
            $list = pdo_fetchall("SELECT * FROM ".tablename('amouse_weijob_company')." WHERE weid = '{$_W['uniacid']}' $condition ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('amouse_weijob_company') . " WHERE weid = '{$_W['uniacid']}' $condition ");
            $pager = pagination($total, $pindex, $psize);
        }elseif($op == 'delete'){
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id FROM".tablename('amouse_weijob_company')."WHERE id = :id", array(':id' => $id));
            if(empty($row)){
                message('公司不存在或已删除！');
            }
            pdo_delete('amouse_weijob_company', array('id' => $id));
            pdo_delete('amouse_weijob_employ', array('companyid' => $id));
            message('删除成功！', referer(), 'success');
        }elseif($op == 'recommed'){//推荐
            $id= intval($_GPC['id']);
            $recommed= intval($_GPC['status']);
            if($recommed==1){
                $msg='审核';
            }elseif($recommed==0){
                $msg='取消审核';
            }
            if($id > 0) {
                pdo_update('amouse_weijob_company',array('status' =>$recommed), array('id' => $id)) ;
                message($msg.'成功！', $this->createWebUrl('company', array('op' => 'display')), 'success');
            }
        }
        include $this->template('company');
    }


    public function doWebEmploy() {
        global $_GPC, $_W;
        load()->func('tpl');
        $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $companyid = intval($_GPC['companyid']);
        if ($op == 'post') {
            $id = intval($_GPC['id']);
            $weid = $_W['uniacid'];
            if (!empty($id)) {
                $item = pdo_fetch("SELECT * FROM ".tablename('amouse_weijob_employ')."WHERE id = :id",array(':id' => $id));
                $item['endtime'] && $item['endtime'] = date('Y-m-d H:i:s', $item['endtime']);
                if(empty($item)){
                    message('抱歉，岗位不存在或已删除', '', 'error');
                }
            } else{
                $item['endtime'] = date('Y-m-d',time() + 6 * 86400);
            }
            $company = pdo_fetch("SELECT * FROM ".tablename('amouse_weijob_company')."WHERE id = :companyid",array(':companyid' => $companyid));
            if(checksubmit('submit')){
                if(empty($_GPC['jobname'])){
                    message('请输入招聘岗位名称！');
                }
                if(empty($_GPC['companyid'])){
                    message('请选择所属公司！');
                }
                if($_GPC['offer'] == ""){
                    message('请填写月薪！');
                }
                if(empty($_GPC['description'])){
                    message('请填写岗位描述！');
                }
                $company = pdo_fetchall("SELECT id,title FROM".tablename('amouse_weijob_company')."WHERE weid = '$weid'");
                $data = array(
                    'weid' => intval($_W['uniacid']),
                    'jobname' => $_GPC['jobname'],
                    'companyid' => $companyid,
                    'number' => $_GPC['number'],
                    'type' => $_GPC['type'],
                    'edu' => $_GPC['edu'],
                    'offer' => $_GPC['offer'],
                    'status' =>1,
					'hits'=> $_GPC['hits'],
                    'workplace' => $_GPC['workplace'],
                    'workyear' => $_GPC['workyear'],
                    'endtime' => strtotime($_GPC['endtime']),
                    'description' => $_GPC['description'],
                    'employorder' => $_GPC['employorder'],
                    'createtime' => TIMESTAMP,
                );
                if (empty($id)) {
                    pdo_insert('amouse_weijob_employ', $data);
                    message('岗位添加成功！', $this->createWebUrl('employ', array('op' => 'display')), 'success');
                } else {
                    unset($data['createtime']);
                    pdo_update('amouse_weijob_employ', $data, array('id' => $id));
                    message('岗位更新成功！', $this->createWebUrl('employ', array('op' => 'display')), 'success');
                }
            }
        }elseif($op == 'display'){
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND jobname LIKE '%{$_GPC['keyword']}%'";
            }
            $list = pdo_fetchall("SELECT * FROM ".tablename('amouse_weijob_employ')." WHERE weid = '{$_W['uniacid']}' $condition ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
      
		   $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('amouse_weijob_employ') . " WHERE weid = '{$_W['uniacid']}'");
            $pager = pagination($total, $pindex, $psize);
        } elseif ($op == 'saves') {
            $employorder = $_GPC['employorder'];
            $filter = array();
            $filter['weid'] = $_W['uniacid'];
            foreach($employorder as $id => $o) {
                $filter['id'] = intval($id);
                $rec = array(
                    'employorder' => intval($o)
                );
                pdo_update('amouse_weijob_employ', $rec, $filter);
            }
            message('批量编辑成功.', referer(), 'success');
        }elseif($op == 'delete'){
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id FROM".tablename('amouse_weijob_employ')."WHERE id = :id", array(':id' => $id));
            if(empty($row)){
                message('岗位不存在或已删除！');
            }
            pdo_delete('amouse_weijob_employ', array('id' => $id));
            pdo_delete('amouse_weijob_resume_recod', array('jobid' => $id));
            message('删除成功！', referer(), 'success');
        }elseif($op == 'recommed'){//推荐
            $id= intval($_GPC['id']);
            $recommed= intval($_GPC['status']);
            if($recommed==1){
            $msg='审核';
            }elseif($recommed==0){
             $msg='取消审核';
            }
            if($id > 0) {
                pdo_update('amouse_weijob_employ',array('status' =>$recommed), array('id' => $id)) ;
                message($msg.'成功！', $this->createWebUrl('employ', array('op' => 'display')), 'success');
            }
        }
        include $this->template('employ');
    }

    public function doWebJob() {
        global $_GPC, $_W;
        $weid = $_W['uniacid'];
        $jobid = $_GPC['jobid'];
        $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if($op == 'display'){
            $list = pdo_fetchall("SELECT * FROM".tablename('amouse_weijob_resume_recod')."WHERE jobid =:jobid",array(':jobid'=>$jobid));
            if(!empty($list)){
                $orders=array();
                foreach ($list as &$item) {
                    $cvs = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_resume')."WHERE id = {$item['cvid']} ORDER BY id");
                    $item['name'] =$cvs['name'];
                    $item['rid'] =$cvs['id'];
                    $item['sex'] = $cvs['sex'];
                    $item['age'] = date('Y-m-d', $cvs['age']);
                    $item['phone'] = $cvs['phone'];
                    $item['self'] = $cvs['self'];
                    $item['job_direction'] = $cvs['job_direction'];
                    $orders[] = $item;
                }
                unset($item);
            }
        }elseif($op == 'recommed'){//推荐
            $rid= intval($_GPC['rid']);
            $recommed= intval($_GPC['status']);
            if($recommed==1){
                $msg='审核';
            }
            if($id > 0) {
                pdo_update('amouse_weijob_resume',array('status' =>$recommed), array('id' => $rid)) ;
                message($msg.'成功！', $this->createWebUrl('employ', array('op' => 'display')), 'success');
            }
        }elseif($op == 'detail'){//推荐
            $rid= intval($_GPC['rid']);
            $jobid = $_GPC['jobid'];
            if($rid > 0) {
                $resume = pdo_fetch("SELECT * FROM".tablename('amouse_weijob_resume')."WHERE id = {$rid} ORDER BY id");
            }
        }elseif($op == 'delete'){//推荐
            $jobid = $_GPC['jobid'];
            $rid = $_GPC['rid'];
            $id = $_GPC['id'];
            $row = pdo_fetch("SELECT id FROM".tablename('amouse_weijob_resume_recod')."WHERE id = :id", array(':id' => $id));
            if(empty($row)){
                message('岗位不存在或已删除！');
            }
            pdo_delete('amouse_weijob_resume', array('id' => $rid));
            pdo_delete('amouse_weijob_resume_recod', array('id' => $id));
            message('删除成功！', referer(), 'success');
        }
        include $this->template('job');
    }


    //求职
    public function doWebApplyJob() {
        global $_GPC, $_W;
        $weid = $_W['uniacid'];
        load()->func('tpl');
        $op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if($op == 'display'){
            $pindex = max(1, intval($_GPC['page']));
            $psize = 20;
            $condition = '';
            if (!empty($_GPC['keyword'])) {
                $condition .= " AND title LIKE '%{$_GPC['keyword']}%'";
            }
            $list = pdo_fetchall("SELECT * FROM ".tablename('amouse_weijob_job')." WHERE weid = $weid $condition ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
            $total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('amouse_weijob_job') . " WHERE weid = $weid $condition ");
            $pager = pagination($total, $pindex, $psize);
        }elseif($op == 'delete'){
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id FROM".tablename('amouse_weijob_job')."WHERE id = :id", array(':id' => $id));
            if(empty($row)){
                message('求职不存在或已删除！');
            }
            pdo_delete('amouse_weijob_job', array('id' => $id));
            message('删除成功！', referer(), 'success');
        }elseif($op == 'recommed'){//推荐
            $id= intval($_GPC['id']);
            $recommed= intval($_GPC['status']);
            if($recommed==1){
                $msg='审核';
            }elseif($recommed==0){
                $msg='取消审核';
            }
            if($id > 0) {
                pdo_update('amouse_weijob_job',array('status' =>$recommed), array('id' => $id)) ;
                message($msg.'成功！', $this->createWebUrl('applyjob', array('op' => 'display')), 'success');
            }
        }
        include $this->template('apply');
    }

}
