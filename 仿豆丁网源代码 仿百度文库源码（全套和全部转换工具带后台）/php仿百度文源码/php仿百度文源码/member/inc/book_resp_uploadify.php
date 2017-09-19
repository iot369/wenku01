<?php
$_COOKIE=$_POST;
require_once(dirname(__FILE__).'/../config.php');
CheckRank(0,0);
require_once(DEDEINC."/dedetag.class.php");
require_once(DEDEINC."/userlogin.class.php");
require_once(DEDEINC."/customfields.func.php");
require_once(DEDEMEMBER."/inc/inc_catalog_options.php");
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");
//首先判断当前会员是否已经对该任务投过稿了
$query = "Select * from `#@__member_response` where mid='$cfg_ml->M_ID' and reqid='$reqid' ";
$arr = $dsql->GetOne($query);
if(is_array($arr))
{
	echo "{result: '-1', msg:'对不起，您已经对该悬赏任务投过稿，请勿重复提交！'}";//已经投过稿，返回失败
	exit();
}

//上传文件
$fileurl = MemberUploadBook('Filedata','',$cfg_ml->M_ID, $utype, '',-1,-1,true);
$fileParts  = pathinfo($_FILES['Filedata']['name']);

$doctitle = $_FILES["Filedata"]["name"];
$filetype = $fileParts['extension'];

$filesize = @filesize($cfg_basedir.$fileurl);
if(empty($filesize))
{
	$filesize = "0 MB";
}
else
{
	$filesize = trim(sprintf("%0.2f", $filesize / 1024 / 1024));
	$filesize = $filesize." MB";
}

//增加文档投标记录
$dsql->ExecuteNoneQuery("insert into `#@__member_response` (mid, doctitle, docdesc, filetype, filesize, fileurl, reqid) values ('$cfg_ml->M_ID', '$doctitle', '$doctitle', '$filetype', '$filesize', '$fileurl', '$reqid') ");

//更新求书信息的投标数+1
$dsql->ExecuteNoneQuery("update `#@__member_request` set respnum = respnum+1 where id='$reqid'");


$result = "0";
$msg = "投稿成功！";

$json .= "{result: '".$result."',";
$json .= "msg: '".$msg."',";
$json .= "title: '".$_FILES["Filedata"]["name"]."',";
$json .= "aid:'".$aid."',";
$json .= "arcid:'".$arcID."',";
$json .= "filetype:'".$fileParts['extension']."'}";
echo $json;

?>