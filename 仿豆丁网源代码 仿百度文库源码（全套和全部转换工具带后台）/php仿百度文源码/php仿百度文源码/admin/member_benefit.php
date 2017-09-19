<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('member_Operations');
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
require_once(DEDEINC.'/datalistcp.class.php');

$addsql = '';//附加查询条件

if(!empty($userid))
{
	$addsql = " and b.userid like '%$userid%' ";
}

if(isset($sta))
{
	$addsql .= " And status='$sta' ";
}
$sql = "Select a.id, a.mid, a.money, a.rmb, a.addtime, a.account, a.status From `#@__member_benefit` a, `#@__member` b where a.mid = b.mid $addsql order by a.id desc";
$dlist = new DataListCP();

//设定每页显示记录数（默认25条）
$dlist->pageSize = 25;
$dlist->SetParameter("buyid",$buyid);
if(isset($sta))
{
	$dlist->SetParameter("sta",$sta);
}
$dlist->dsql->SetQuery("Select * From #@__moneycard_type ");
$dlist->dsql->Execute('ts');
while($rw = $dlist->dsql->GetArray('ts'))
{
	$TypeNames[$rw['tid']] = $rw['pname'];
}
$tplfile = DEDEADMIN."/templets/member_benefit.htm";
//echo $sql;
//这两句的顺序不能更换
$dlist->SetTemplate($tplfile);      //载入模板
$dlist->SetSource($sql);            //设定查询SQL
$dlist->Display();                  //显示

function GetMemberID($mid)
{
	global $dsql;
	if($mid==0)
	{
		return '0';
	}
	$row = $dsql->GetOne("Select userid From #@__member where mid='$mid' ");
	if(is_array($row))
	{
		return "<a href='member_view.php?id={$mid}'>".$row['userid']."</a>";
	}
	else
	{
		return '0';
	}
}

function GetPType($tname)
{
	if($tname=='card') return '点数卡';
	else if($tname=='archive') return '购买文章';
	else if($tname=='stc') return '兑换金币';
	else return '会员升级';
}

function GetSta($sta)
{
	if($sta==0)
	{
		return '未审核';
	}
	else if($sta==1)
	{
		return '已拒绝';
	}
	else if($sta==2)
	{
		return '已同意/未付款';
	}else if($sta ==3){
		return '已付款';
	}
}

?>