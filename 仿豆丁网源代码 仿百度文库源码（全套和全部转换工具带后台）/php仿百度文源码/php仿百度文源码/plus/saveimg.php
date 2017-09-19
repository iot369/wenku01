<?php
require_once(dirname(__FILE__)."/../include/common.inc.php");
//文档ID
$id = intval($aid);

//图片二进制数据
$data = $GLOBALS['HTTP_RAW_POST_DATA'];

//echo $data; 
//生成图片
$fileurl = createImage($data);

//生成图片成功，更新数据库记录
if($fileurl != ""){
	//更新文档记录
	$dsql->ExecuteNoneQuery("update `#@__archives` set litpic = '$fileurl', flag = CONCAT(flag, ',p') where id = '$id'");
}
echo $fileurl;

function createImage($data){	
	global $cfg_basedir, $cfg_image_dir, $cfg_addon_savetype, $cfg_dir_purview;
	$filename = '1'.'-'.dd2char(MyDate('ymdHis', time())).$rnddd.'-L';
	$filedir = $cfg_image_dir.'/'.MyDate($cfg_addon_savetype, time());
	
	//如果目录不存在，则先创建
	if(!file_exists($cfg_basedir.$filedir)){
		mkdir($cfg_basedir.$filedir, $cfg_dir_purview);
	}
	
	$fileurl = $filedir.'/'.$filename.'.png';
	
	$filepath = $cfg_basedir.$fileurl;
	
	if(!file_exists($filepath)){
		$fo = fopen($filepath, "w");
		if(!fwrite($fo, $data)){
				return "";
		}else{
				return $fileurl;
		}
	}
}
?>