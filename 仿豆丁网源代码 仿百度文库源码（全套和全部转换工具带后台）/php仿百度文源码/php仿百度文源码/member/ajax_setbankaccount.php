<?php
require_once(dirname(__FILE__)."/config.php");
function showJSON($msg, $code){
	echo "{code:'".$code."', msg:'".$msg."'}";
}


if(!isset($vdcode))
{
	$vdcode = '';
}
$svali = GetCkVdValue();
if(preg_match("/2/",$safe_gdopen)){
	if(strtolower($vdcode)!=$svali || $svali=='')
	{
		ResetVdValue();
		showJSON('验证码错误！', '-1');
		exit();
	}
	
}
if($account=='')
{
	showJSON("账号不能为空！","-1");
	exit();
}

$rs = $dsql->GetOne("SELECT count(1) count FROM `#@__member_bank_account` where mid = '$cfg_ml->M_ID'");

//如果存在，就更新
if($rs["count"] == "1") {
	$dsql->ExecuteNoneQuery("Update `#@__member_bank_account` set account='$account', modifytime=now() where mid='".$cfg_ml->M_ID."' ");
}else{
	$dsql->ExecuteNoneQuery("insert into `#@__member_bank_account`(mid, account) values('$cfg_ml->M_ID', '$account')");
}

showJSON("设置提现账号成功！","0");
exit();
?>