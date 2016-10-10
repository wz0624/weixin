<?php 

class Pdb_cloudbakModuleCore extends WeModuleSite {
	//备份进度：
	public function doWebProcess() {
		set_time_limit(0);
		error_reporting(0);
		
		global $_GPC, $_W;
		$id = $_GPC['id'];
		$op = $_GPC['op'];
		$step = $_GPC['step'] ? (int)$_GPC['step']:1;
		
		$setting = $this->module['config'];
		$documentRoot = IA_ROOT;
		//引入快盘的库：
		define('CONSUMER_KEY',$setting['consumer_key']); //换成你申请的应用对应的CONSUMER_KEY
		define('CONSUMER_SECRET',$setting['consumer_secret']);//同上
		require_once (__DIR__ . '/kp.class.php');
		require_once (__DIR__ . '/folder.php');
		// echo CONSUMER_SECRET;exit;
		// echo $documentRoot;exit;
		$documentRoot1 = str_replace('\\','/',$documentRoot);//格式化过的根目录地址；
		
		echo '<meta charset="utf-8">';
		echo '<script src="./resource/js/lib/jquery-1.11.1.min.js"></script>';
		echo str_repeat(" ",1024);
		
		//第一步，先把所有的文件遍历出来，存放到数据库：
		if ($step == 1){
			//导出数据库：
			echo '<script>$("#ajax_result",window.parent.document).html("正在导出数据库文件...");</script>';
			ob_flush();
			flush();
			
			if ($setting['host']){
				$host = $setting['host'];
				$hostArr = explode(':',$host);
				// print_r($hostArr);exit;
				$host = $hostArr[0];
				$port = $hostArr[1];
			}else{
				$host = $_W['config']['db']['host'];
			}
			
			if ($setting['username']){
				$username = $setting['username'];
			}else{
				$username = $_W['config']['db']['username'];
			}
			
			if ($setting['password']){
				$password = $setting['password'];
			}else{
				$password = $_W['config']['db']['password'];
			}
			
			if ($setting['username']){
				$username = $setting['user'];
			}else{
				$username = $_W['config']['db']['username'];
			}
			
			if ($setting['dbname']){
				$dbname = $setting['dbname'];
			}else{
				$dbname = $_W['config']['db']['database'];
			}
			
			if (!$port){
				$port = $_W['config']['db']['port'];
				if (!$port){
					$port = '3306';
				}
			}
			// echo $port;exit;
			
			$DB = array();
			$DB['chset']='utf8';
			$DB['host'] = $host;
			$DB['port'] = $port;
			$DB['user'] = $username;
			$DB['pwd'] = $password;
			$DB['db'] = $dbname;
			// print_r($DB);exit;
			
			require_once (__DIR__ . '/db_export.php');
			do_export($DB);
			
			//压缩完数据库，标识进度为50%，并进入上传文件的页面：
			echo '<script>$("#ajax_result",window.parent.document).html("数据库文件已经成功导出！");</script>';
			ob_flush();
			flush();
			//数据库导出完成。
			
			echo '<script>$("#ajax_result",window.parent.document).html("正在整理文件清单！");</script>';
			ob_flush();
			flush();
			
			//组合排除的文件夹配置：
			$ext_dirs = $setting['ext_dirs'];
			$ext_dirs = str_replace(array('，','；',';'),',',$ext_dirs);
			$ext_dirs = explode(',',$ext_dirs);
			if (!is_array($ext_dirs)){
				$ext_dirs = array();
			}else{
				foreach ($ext_dirs as $k=>$v){
					if (!$v){
						unset($ext_dirs[$k]);
					}
				}
			}
			// print_r($ext_dirs);exit;
			
			//组合排除的文件后缀名配置：
			$ext_files = $setting['ext_files'];
			$ext_files = str_replace(array('，','；',';'),',',$ext_files);
			$ext_files = str_replace('.','',$ext_files);
			$ext_files = explode(',',$ext_files);
			if (!is_array($ext_files)){
				$ext_files = array();
			}else{
				foreach ($ext_files as $k=>$v){
					if (!$v){
						unset($ext_files[$k]);
					}
				}
			}
			// print_r($ext_files);exit;
			
			//获取根目录下的文件夹，排除mybak和myfile的文件夹：
			$list = JFolder::folders($documentRoot);
			// print_r($list);exit;
			$folder = array();
			$folder[] = '';
			if (is_array($list)){
				foreach ($list as $k=>$v){
					// echo $v;exit;
					if (in_array($v,$ext_dirs)){
						continue;
					}
					$folder[] = $v;
				
				}
			
			}
			// print_r($folder);exit;
			//读取每个文件夹下的文件
			defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
			if (is_array($folder)){
				$size = 0;
				$i = 1;
				foreach ($folder as $k=>$v){
					//压缩网站的文件，并移动到指定的文件夹去：
					// echo $documentRoot.DS.$v;exit;
					if ($v){
						$dirs = JFolder::folders($documentRoot.DS.$v, '.', true, true);
						array_push($dirs, $documentRoot.DS.$v);//加入根目录
					}else{
						$dirs[] = $documentRoot;
					}
					// print_r($dirs);continue;
					
					// exit;
					if (is_array($dirs)){
						foreach ($dirs as $dir) {
							$files = JFolder::files($dir, '.', false, true);
							// print_r($files);exit;
							foreach ($files as $file) {
								//在这里排除指定的文件后缀名：
								$filename = str_replace($dir,'',$file);
								$filename = str_replace('\\','',$filename);
								
								$dirname = str_replace($documentRoot1,'',$file);
								$dirname = str_replace($filename,'',$dirname);
								$dirname = str_replace('\\','/',$dirname);
								// echo $dirname.'=>'.$filename."\r\n";continue;
								// echo $dirname.'=>'."\r\n";exit;
								$ext = JFolder::getExt($file);
								// echo $ext;exit;
								if (in_array($ext,$ext_files)){
									continue;
								}
								$sign = md5($file);
								//把每个文件的路径写入到数据表中：
								$sql = "select id from ".tablename('pdb_cloudbak_files').
										" where sign like '{$sign}' and record_id ='{$id}' limit 1 ";
								// echo $sql;exit;
								$check = pdo_fetchcolumn($sql);
								// echo $check;exit;
								if (!$check){
									$data = array();
									$data['record_id'] = $id;
									$data['file_path'] = $file;
									$data['dir'] = $dirname;
									$data['filename'] = $filename;
									$data['sign'] = $sign;
									pdo_insert('pdb_cloudbak_files',$data);
									// print_r($data);exit;
								}
		
								
							}
						}
						
					}
				
				}
			}
			
			
			
			//显示进度：
			echo '<script>$("#nowstep",window.parent.document).val(2);</script>';
			$barPer = 1;
			echo '<script>$("#progressbar",window.parent.document).width("'.$barPer.'%");</script>';
			ob_flush();
			flush();
			//进入下一页：
			$next = $this->createWebUrl('process',array('id'=>$id,'step'=>'2'));
			echo '<script>location.href="'.$next.'";</script>';
			ob_flush();
			flush();
			exit;
		}
		
		//第二步：逐个文件上传到金山快盘：
		if ($step == 2){
			$kp = new kp(CONSUMER_KEY,CONSUMER_SECRET);
			// print_r($kp);exit;
			//检查是否有快盘的授权：
			$is_oath = 0;
			$kp_oauth_file = __DIR__ . '/kp_oauth';
			if (!file_exists($kp_oauth_file)){
				$is_oath = 1;
			}else{
				$oauth = json_decode(file_get_contents($kp_oauth_file));//从文件中读取
				// echo $oauth->expires_time;exit;
				if ($oauth->expires_time <= time()){
					$is_oath = 1;
				}
			}
			
			if ($op == 'get_oauth'){
				//跳转到快盘授权：
				//print_r($kp);exit;
				$backurl = $_GPC['backurl'];
				// print_r($_W);exit;
				$call_back = $_W['siteroot'].'web/'.str_replace('./','',$this->createWebUrl('process', array('id' => $id,'op'=>'oauth_authorize','step'=>'2','backurl'=>$backurl)));
				$oauth = $kp->OAuth($call_back);
				//print_r($oauth);exit;
				exit;
			}
			
			if ($op == 'oauth_authorize'){
				//保存快盘授权：
				$oauth = $kp->OAuth();
				if ($kp->errstr){
					echo '<script>alert("'."OAuth授权失败:".$kp->errstr.'")</script>';
					ob_flush();
					flush();
					exit;
					// die("OAuth授权失败:".$kp->errstr);
				}else{
					file_put_contents(__DIR__ . '/kp_oauth',json_encode($oauth));
					// printf("OAuth授权成功：%s<br>",json_encode($oauth));exit;
					// echo '';
					$backurl = $_GPC['backurl'];
					$backurl = urldecode($backurl);
					//跳转到压缩文件的页面：
					header("location:".$backurl);
					exit;
					
				}
				
			}
			
			if (!empty($oauth))
			{
				$kp->oauth_token = $oauth->oauth_token;
				$kp->oauth_token_secret = $oauth->oauth_token_secret;
			}
			
			//需要快盘授权：
			if ($is_oath == 1){
				//准备输出进度条：
				echo '<meta charset="utf-8">';
				echo '<script src="./resource/js/lib/jquery-1.11.1.min.js"></script>';
				echo str_repeat(" ",1024);
				
				$backurl = urlencode($this->createWebUrl('start', array('id' => $id,'in_bak'=>'1')));
				echo '<script>alert("未获取快盘授权或授权过期，跳转到快盘授权")</script>';
				echo '<script>window.parent.location.href="'.$this->createWebUrl('process', array('id' => $id,'op'=>'get_oauth','step'=>'2','backurl'=>$backurl)).'";</script>';
				ob_flush();
				flush();
				exit;
			}
			// exit('step2');
			echo '<script>$("#ajax_result",window.parent.document).html("正在上传文件...");</script>';
			ob_flush();
			flush();
			
			$sql = "select * from ".tablename('pdb_cloudbak_files').
					" where is_bak = 0 and record_id='{$id}' order by id asc limit 1";
			$fileArr = pdo_fetch($sql);
			// print_r($fileArr);exit;
			if (!$fileArr['id']){
				
				//修改本次任务的状态：
				$data = array('status'=>1);
				pdo_update('pdb_cloudbak_record',$data,array('id' => $id));
				
				//备份完成，删除数据库的备份文件：
				@unlink(IA_ROOT .'/mysql_bak.sql');
				
				echo '<script>$("#is_finished",window.parent.document).val(1);</script>';
				echo '<script>$("#ajax_result",window.parent.document).html("<span style=\'color:red;\'>本次备份已经完成！</span>");</script>';
				echo '<script>$("#button_abort",window.parent.document).hide();</script>';
				echo '<script>$("#progressbar",window.parent.document).width("100%");</script>';
				ob_flush();
				flush();
				exit;

			}
			
			//总文件数：
			$sql = "select count(*) as total from ".tablename('pdb_cloudbak_files').
					" where record_id='{$id}' order by id asc limit 1";
			$allFileTotal = pdo_fetchcolumn($sql);
			//未完成的文件数：
			$sql = "select count(*) as total from ".tablename('pdb_cloudbak_files').
					" where record_id='{$id}' and is_bak =0 order by id asc limit 1";
			$noBakTotal = pdo_fetchcolumn($sql);
			
			$sql = "select * from ".tablename('pdb_cloudbak_record')." where id='{$id}' limit 1";
			$record = pdo_fetch($sql);
			
			//创建快盘的根目录：
			$dir = 'PHPDB微赞云备份/'.$_SERVER['HTTP_HOST'].'/'.date("Y-m-d",strtotime($record['create_time'])).'-ID-'.$id;
			// echo $dir;exit;
			$dir = $kp->gbk_to_utf8($dir);
			$ret = $kp->md($dir);
			
			// echo $dir;exit;
			$file2 = $fileArr['file_path'];
			if (file_exists($file2)){
				$targetDir = $dir . $fileArr['dir'];
				$file1 = $dir . $fileArr['dir'].$fileArr['filename'];
				
				$targetDir = $kp->gbk_to_utf8($targetDir);
				$ret = $kp->md($targetDir);
				if ($ret == false){
					//错误，进入到下一页重试：
					//进入下一页：
					$next = $this->createWebUrl('process',array('id'=>$id,'step'=>'2'));
					echo '<script>location.href="'.$next.'";</script>';
					ob_flush();
					flush();
					exit;
				}
				$ret = $kp->upload($file1,$file2);
				
			}
			//echo 'hello';
			// print_r($ret);exit;
			$data = array();
			if ($ret){
				$data['is_bak'] = 1;
			}else{
				$data['is_bak'] = 2;
				
			}
			$data['bak_time'] = date("Y-m-d H:i:s");
			pdo_update('pdb_cloudbak_files',$data,array('id'=>$fileArr['id']));
			// print_r($data);exit;
			
			//显示进度：
			$finished = $allFileTotal - $noBakTotal;
			$per = $finished / $allFileTotal;
			$barPer = $per * 100;
			// $barPer = 10;
			echo '<script>$("#progressbar",window.parent.document).width("'.$barPer.'%");</script>';
			ob_flush();
			flush();
			//进入下一页：
			$next = $this->createWebUrl('process',array('id'=>$id,'step'=>'2'));
			echo '<script>location.href="'.$next.'";</script>';
			ob_flush();
			flush();
			exit;	
			
		}

		echo '<script>alert("备份已经完成！")</script>';
		ob_flush();
		flush();
		exit;
		

		
	}
	
}
