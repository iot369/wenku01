<?php
$_COOKIE=$_POST;
require_once(dirname(__FILE__)."/../config.php");
//增加权限检查
if (!empty($_FILES)) {
	
	//require_once(DEDEINC."/image.func.php");
	//$sparr_image = Array("image/pjpeg","image/jpeg","image/gif","image/png","image/x-png","image/wbmp");
	//$sparr_flash = Array("application/x-shockwave-flash");
	//$okdd = 0;
	$uptime = time();
	$adminid = $cuserLogin->getUserID();
	
	//上传文件
	$filename = $_FILES["Filedata"]["name"];
	$fileurl = adminUploadBook('Filedata','',$adminid, $utype, '',-1,-1,true);
	$fileParts  = pathinfo($_FILES['Filedata']['name']);
	$filetype = $fileParts['extension'];
	$filesize = @filesize($cfg_basedir.$fileurl);
	
	//保存附件信息到数据库中 t_uploads表。
	//$aid = SaveBookUploadInfo($_FILES["Filedata"]["name"], $filename, $mediatype, $fileParts['extension']);
	$uptime = time();
	//插入数据
	$inquery = "INSERT INTO `#@__uploads`(title,url,mediatype,filetype,width,height,playtime,filesize,uptime,mid)
	   VALUES ('$filename','$fileurl','$medaitype', '$filetype', '0', '0', '0','$filesize','$uptime','$adminid'); ";
	$dsql->ExecuteNoneQuery($inquery);

	$aid = $dsql->GetLastID();
	
	$result = "-1";
	if($filename != ''){
		$result = 0;
	}
	
	//返回结果	
	$json .= "{result: '".$result."',";
	$json .= "title: '".$_FILES["Filedata"]["name"]."',";
	$json .= "aid:'".$aid."',";
	$json .= "filetype:'".$fileParts['extension']."'}";
	echo $json;
	exit();
}
?>