<?php
//系统版本检测文件
require_once '../include/global.php';
require_once 'userdata.php';
$ADMIN_COOKIE = isset($_COOKIE['ADMIN_COOKIE']) ? purge($_COOKIE['ADMIN_COOKIE']) : '';
if($ADMIN_COOKIE == $cookie){
	$islogin = true;
}else{
	$islogin = false;
}

if (!$islogin) {
echo "<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL /edition.php was not found on this server.</p>
</body></html>";
exit;
}

$data = http_gets('http://auth.lvdoui.net/api/authlook/update?host=' . $_SERVER['HTTP_HOST'] . '&authkey=EAEFBAFE732CED83C872A42EDCFA622F'. '&version=' . EDITION);
$json_data = json_decode($data, true);

if(EDITION >= $json_data['msg']['extend']){ //不需要更新，检测文件是否被修改
	$file_new = [];
	$file_md5 = [];
	$file_lose = [];
	if(is_array($json_data['msg']['extend']) && !empty($json_data['msg']['extend'])){
		foreach($json_data['msg']['extend'] as $extend) {
			if($extend['home'] == 'api'){
				$file_arr = apiData($extend['file'],$extend['mulu']);
			}elseif('adm'){
				$file_arr = admData($extend['file'],$extend['mulu']);
			}
			if($file_arr){
				if($file_arr['md5'] != $extend['md5']  && $file_arr['version'] == $extend['version']){
					$file_md5[] = $extend;
				}
				if($file_arr['version'] < $extend['version']){
					$file_new[] = $extend;
				}	
			}else{
				$file_lose[] = $extend;
			}
		}
		$data = ['file_new'=>$file_new,'file_md5'=>$file_md5,'file_lose'=>$file_lose];
		json(200, $data);
	}	
}else{
	json(201,['edition'=>$json_data['msg'], 'new_url'=>$json_data['url']]);
}

function admData($FilePath,$mulu='') {
	if(file_exists(FCPATH.ADM_EXTEND_MULU.$mulu.$FilePath)){
		$Data = implode('', file(FCPATH.ADM_EXTEND_MULU.$mulu.$FilePath));
		$filemd5 = md5_file(FCPATH.ADM_EXTEND_MULU.$mulu.$FilePath);
		preg_match("/Version:(.*)/i", $Data, $version);
		$version = isset($version[1]) ? strip_tags(trim($version[1])) : '';
		$file_arr = ['version'=>$version,'md5' => $filemd5];
		return $file_arr;
	}else{
		return false;
	}
}

function apiData($FilePath,$mulu='') {
	if(file_exists(FCPATH.API_EXTEND_MULU.$mulu.$FilePath)){
		$Data = implode('', file(FCPATH.API_EXTEND_MULU.$mulu.$FilePath));
		$filemd5 = md5_file(FCPATH.API_EXTEND_MULU.$mulu.$FilePath);
		preg_match("/Version:(.*)/i", $Data, $version);
		$version = isset($version[1]) ? strip_tags(trim($version[1])) : '';
		$file_arr = ['version'=>$version,'md5' => $filemd5];
		return $file_arr;
	}else{
		return false;
	}	
}
