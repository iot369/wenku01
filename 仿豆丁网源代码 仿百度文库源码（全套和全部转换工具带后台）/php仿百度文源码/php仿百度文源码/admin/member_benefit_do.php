<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/oxwindow.class.php");
if(empty($dopost))
{
	$dopost = '';
}
if(empty($fmdo))
{
	$fmdo = '';
}
$ENV_GOBACK_URL = isset($_COOKIE['ENV_GOBACK_URL']) ? 'member_main.php' : '';

$adminid = $cuserLogin->getUserID();

//设置提现记录为已审核状态
if($dopost == "checkedbenefit")
{	
	$nid = ereg_replace('[^0-9,]','',ereg_replace('`',',',$nid));
	$nid = explode(',',$nid);
	if(is_array($nid))
	{
		foreach ($nid as $var)
		{
			$query = "update `#@__member_benefit` set status = '2', operator='$adminid', optime=now() where id = '$var'";
			//echo $query;
			$dsql->ExecuteNoneQuery($query);
		}
		ShowMsg("更新提现状态为【已审核】成功！","member_benefit.php");
		exit();
	}
}
//设置提现记录为已拒绝
else if($dopost == "refusebenefit")
{	
	$nid = ereg_replace('[^0-9,]','',ereg_replace('`',',',$nid));
	$nid = explode(',',$nid);
	if(is_array($nid))
	{
		foreach ($nid as $var)
		{
			$query = "update `#@__member_benefit` set status = '1', operator='$adminid', optime=now() where id = '$var'";
			$dsql->ExecuteNoneQuery($query);
		}
		ShowMsg("更新提现状态为【已拒绝】成功！","member_benefit.php");
		exit();
	}
}
//设置提现记录为已付款状态
else if($dopost == "upbenefit")
{	
	$nid = ereg_replace('[^0-9,]','',ereg_replace('`',',',$nid));
	$nid = explode(',',$nid);
	if(is_array($nid))
	{
		foreach ($nid as $var)
		{
			$query = "update `#@__member_benefit` set status = '3', operator='$adminid', optime=now() where id = '$var'";
			$dsql->ExecuteNoneQuery($query);
		}
		ShowMsg("更新提现状态为【已付款】成功！","member_benefit.php");
		exit();
	}
}
?>