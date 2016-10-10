<?php
defined('IN_IA') or exit('Access Denied');
global $_W,$_GPC;
$module_name = 'hx_o2o';
checktable();
if(empty($_W['isfounder'])) {
	message('您没有相应操作权限', '', 'error');
}
load()->func('tpl');
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'upgate';
$ip =gethostbyname($_SERVER['SERVER_ADDR']);
$domain =$_SERVER['HTTP_HOST'];
$setting =setting_load('site');
$id =isset($setting['site']['key'])? $setting['site']['key'] : '1';
load()->func('communication');
load()->func('file');
$tmpdir =IA_ROOT."/addons/".$module_name."/".date('ymd');

$versionfile = IA_ROOT."/addons/".$module_name."/version.php";
if(file_exists($versionfile)){
	require_once $versionfile;
	$version = VERSION;
}else{
	$version = '1.0.0';
}

if(!is_dir($tmpdir)){
	mkdirs($tmpdir);
}
if ($operation == 'upgate') {
	$auth = getAuthSet();
	if(checksubmit('submit')){
		if (empty($_GPC['domain'])){
			message('域名不能为空!', '', 'error');
		}
		if (empty($_GPC['code'])){
			message('请填写授权码!', '', 'error');
		}
		if (empty($_GPC['id'])){
			message('您还没未注册站点!', '', 'error');
		}
		//发送请求，验证授权
		$resp =ihttp_post('http://wechat.imaumm.com/app/index.php?i=109&c=entry&do=oauth&m=hx_modules',array('ip'=>$ip,'id'=>$id,'key'=>$_GPC['code'],'domain'=>$domain,'module'=>$module_name));
		$content = json_decode($resp['content']);
		$status = intval($content->status);
		$message = trim($content->message);
		if ($status == 1){
			$set =pdo_fetch('SELECT * FROM ' . tablename('hx_module'). ' WHERE module = :module limit 1', array(':module' => $module_name));
			$sets =iunserializer($set['set']);
			if (!is_array($sets)){
				$sets =array();
			}
			$sets['auth'] =array('ip' => $ip, 'id' => $id, 'code' => $_GPC['code'], 'domain'=>$_GPC['domain'] );
			if (empty($set)){
				pdo_insert('hx_module', array('set' => iserializer($sets), 'module' => $module_name,'time'=>time()));
			}else{
				pdo_update('hx_module', array('set' => iserializer($sets),'time'=>time()),array('module'=>$module_name));
			}
			message('系统授权成功！', referer(), 'success');
		}
		
		message('授权失败，请联系客服! 错误信息:'.$message);
	}

	$status =0;
	if (!empty($ip)&& !empty($id) && !empty($auth['code'])){
		load()->func('communication');
		//发送请求，验证授权
		$resp =ihttp_post('http://wechat.imaumm.com/app/index.php?i=109&c=entry&do=oauth&m=hx_modules',array('ip'=>$ip,'id'=>$id,'key'=>$auth['code'],'domain'=>$domain,'module'=>$module_name));
		$content = json_decode($resp['content']);
		$status = intval($content->status);
		$message = trim($content->message);
		if ($status == 1){
			$status =1;
		}
	}
	include $this->template('upgate');
}elseif ($operation == 'copyright') {
	load()->model('setting');
	load()->func('tpl');
	$settings = setting_load('copyright');
	$settings = $settings['copyright'];
	if(empty($settings) || !is_array($settings)) {
		$settings = array();
	}

	$do = $_GPC['op']?trim($_GPC['op']):'copyright';

	if ($do == 'copyright') {
		$_W['page']['title'] = '站点信息设置';
		if (checksubmit('submit')) {
			$data = array(
					'status' => $_GPC['status'],
					'reason' => $_GPC['reason'],
					'sitename' => $_GPC['sitename'],
					'url' => strexists($_GPC['url'], 'http://') ? $_GPC['url'] : "http://{$_GPC['url']}",
					'statcode' => htmlspecialchars_decode($_GPC['statcode']),
					'footerleft' => htmlspecialchars_decode($_GPC['footerleft']),
					'footerright' => htmlspecialchars_decode($_GPC['footerright']),
					'icon' => $_GPC['icon'],
					'flogo' => $_GPC['flogo'],
					'blogo' => $_GPC['blogo'],
					'baidumap' => $_GPC['baidumap'],
					'company' => $_GPC['company'],
					'address' => $_GPC['address'],
					'person' => $_GPC['person'],
					'phone' => $_GPC['phone'],
					'qq' => $_GPC['qq'],
					'email' => $_GPC['email'],
					'keywords' => $_GPC['keywords'],
					'description' => $_GPC['description'],
					'showhomepage' => intval($_GPC['showhomepage']),
			);
			setting_save($data, 'copyright');
			message('更新设置成功！', $this->createWebUrl('sysset'));
		}
	}
	include $this->template('copyright');
}elseif ($operation == 'update') {
	$auth = getAuthSet();
	$versionfile =IA_ROOT . '/addons/'.$module_name.'/version.php';
	if (is_file($versionfile)){
		$updatedate =date('Y-m-d H:i', filemtime($versionfile));
	}else{
		$updatedate =date('Y-m-d H:i', filemtime($versionfile));
	}
	include $this->template('download');
}elseif ($operation == 'check'){
	set_time_limit(0);
	$auth = getAuthSet();
	global $my_scenfiles;
	my_scandir(IA_ROOT.'/addons/'.$module_name.'/');
	$files =array();
	foreach($my_scenfiles as $sf){
		$files[] =array('path' => str_replace(IA_ROOT."/addons/".$module_name."/","",$sf), 'md5'=> md5_file($sf));
	}
	$files =base64_encode(json_encode($files));
	$resp =ihttp_post('http://wechat.imaumm.com/app/index.php?i=109&c=entry&op=check&do=oauth&m=hx_modules',array('ip'=>$auth['ip'], 'id'=>$auth['id'], 'key'=>$auth['code'], 'domain'=>$auth['domain'], 'version'=>$version, 'files'=>$files ,'module'=>$module_name));
	//print_r($resp['content']);
	$content = object_to_array(@json_decode($resp['content']));
	
	if($content['status'] ==1){
		$files =array();
		if (!empty($content['files'])){
			foreach ($content['files'] as $file){
				$entry =IA_ROOT . "/addons/".$module_name."/".$file['path'];
				if (!is_file($entry)|| md5_file($entry)!= $file['md5']){
					
					if($file['path'] == '/install.php' || $file['path'] == '/upgrade.php' || $file['path'] == '/manifest.xml' || $file['path'] == '/version.php'){
						
					}else{
						$files[] =array('path'=>$file['path'],'download'=>0);
					}
				}
			}
		}
		$content['files'] = $files;
		file_put_contents($tmpdir."/file.txt",json_encode($content));
		if (version_compare($content['version'],$version,"=") && count($files) == 0) {
			@rmdirs($tmpdir);
			die(json_encode(array('result'=>0, 'message' => '<p class="label label-success" >无需更新</p> <a class="btn btn-default" href="">刷新!</a>')));
		}
		die(json_encode(array('result'=>1, 'version'=>$content['version'], 'filecount'=>count($files), 'upgrade'=>!empty($content['upgrade']))));
	}
	
	die(json_encode(array('result' => 0, 'message' => '<p class="label label-success" >'.$content['message']."</p>. <a class='btn btn-default' href=''>刷新!</a>")));
}elseif ($operation == 'download'){
	$f =file_get_contents($tmpdir."/file.txt");
	$upgrade =json_decode($f,true);
	$files =$upgrade['files'];
	$auth = getAuthSet();
	$path ="";
	foreach($files as $f){
		if(empty($f['download'])){
			$path =$f['path'];
			break;
		}
	}
	
	if(!empty($path)){
		$resp =ihttp_post('http://wechat.imaumm.com/app/index.php?i=109&c=entry&op=download&do=oauth&m=hx_modules',array('ip'=>$auth['ip'], 'id'=>$auth['id'], 'key'=>$auth['code'], 'domain'=>$auth['domain'], 'path'=>$path ,'module'=>$module_name));
		$ret =object_to_array(@json_decode($resp['content'], true));
		if($ret['status'] == 0){
			die(json_encode(array('result'=>1, 'total'=>1,'success'=>$ret['message'])));
		}
		if ($ret['status'] == 1){
			$path =$ret['path'];
			if(!file_exists(IA_ROOT.'/addons/'.$this->modulename.'/'.$path)){
				mkdirs(dirname(IA_ROOT.'/addons/'.$this->modulename.'/'.$path),"0777");
			}
			$content =base64_decode($ret['content']);
			file_put_contents(IA_ROOT.'/addons/'.$this->modulename.''.$path, $content);
			$success =0;
			foreach($files as &$f){
				if($f['path']==$path){
					$f['download'] =1;
					break;
				}
				if($f['download']){
					$success++;
				}
			}
			unset($f);
			$upgrade['files'] =$files;
			file_put_contents($tmpdir."/file.txt",json_encode($upgrade));
			die(json_encode(array('result'=>1, 'total'=>count($files),'success'=>$success."(".$path.")")));
		}
	}else{
		if(!empty($upgrade['upgrade'])){
			$updatefile =IA_ROOT."/addons/".$module_name."/update.php";
			file_put_contents($updatefile, base64_decode($upgrade['upgrade']));
			require $updatefile;
			if(file_exists($updatefile)){
				@unlink($updatefile);
			}
			$installfile =IA_ROOT."/addons/".$module_name."/install.php";
			if(file_exists($installfile)){
				@unlink($installfile);
			}
			$xmlfile =IA_ROOT."/addons/".$module_name."/manifest.xml";
			if(file_exists($xmlfile)){
				@unlink($xmlfile);
			}
			
			file_put_contents(IA_ROOT.'/addons/'.$module_name.'/version.php',"<?php if(!defined('VERSION')) {define('VERSION','".$upgrade['version']."');}");
		}
		@rmdirs($tmpdir);
		die(json_encode(array('result'=>2)));
	}
}
function getAuthSet(){
	global $_W;
	$module = 'hx_o2o';
	$set =pdo_fetch("SELECT * FROM " . tablename('hx_module'). " WHERE `module` = '{$module}' limit 1");
	$sets =iunserializer($set['set']);
	if (is_array($sets)){
		return is_array($sets['auth'])? $sets['auth'] : array();
	}
	return array();
}
function checktable(){
	global $_W;
	if(!pdo_tableexists('hx_module')){
		$sql = "
CREATE TABLE `ims_hx_module` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`module` varchar(32) NOT NULL DEFAULT '',
	`set` text NOT NULL,
	`time` int(11) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM
CHECKSUM=0
DELAY_KEY_WRITE=0;";
		pdo_query($sql);
	}
}
function my_scandir($dir) {
	global $my_scenfiles;
	if ($handle = opendir($dir)) {
		while (($file = readdir($handle)) !== false) {
			if ($file != ".." && $file != ".") {
				if (is_dir($dir . "/" . $file)) {
					my_scandir($dir . "/" . $file);
				} else {
					$my_scenfiles[] = $dir . "/" . $file;
				}
			}
		}
		closedir($handle);
	}
}
function object_to_array($obj)
{
	$_arr= is_object($obj) ? get_object_vars($obj) : $obj;
	foreach((array)$_arr as $key=> $val)
	{
		$val= (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
		$arr[$key] = $val;
	}
	return$arr;
}
?>