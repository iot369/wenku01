<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$menutype = 'mydede';
$menutype_son = 'mf';
if($cfg_mb_lit=='Y')
{
	ShowMsg("由于系统开启了精简版会员空间，你访问的功能不可用！","-1");
	exit();
}
require_once(DEDEINC."/datalistcp.class.php");

if(!isset($ftype))//0-所有任务；1-我发布的任务
{
	$ftype = 0;
}
if(!isset($dopost))
{
	$dopost = '';
}

if ($dopost == 'add')
{//发布悬赏
    $validate=GetMkTime($validate);
	$query = "insert into `#@__member_request`(mid, doctitle, score, remark, validate) values ('$cfg_ml->M_ID', '$doctitle', '$score', '$remark', '$validate') ";
	//echo GetMkTime($validate);
	if($dsql->ExecuteNoneQuery($query))
	{
		ShowMsg('增加悬赏任务成功', 'book_request.php');
	}
	else
	{
		ShowMsg('增加悬赏任务失败', '-1');
	}
	exit();
}
else if ($dopost == 'respreq')
{//应答悬赏
	
	ShowMsg('应标悬赏任务成功！','book_request_add.php');
	exit();
}
else if ($dopost == 'selectresp')
{//选择应答
	
	ShowMsg('选择悬赏中标会员成功','book_request_add.php');
	exit();
}
//浏览
else{
	
	$query = "Select * From `#@__member_request` a, `#@__member` b where a.mid = b.mid order by id desc";
	
	if($ftype==1)
	{
		$query = "Select * From `#@__member_request` a, `#@__member` b where a.mid = b.mid and a.mid='$cfg_ml->M_ID' order by id desc";
	}
	
	$dlist = new DataListCP();
	$dlist->pageSize = 20;
	$dlist->SetParameter("ftype",$ftype);
	$dlist->SetTemplate(dirname(__FILE__).'/templets/book_request_list.htm');
	$dlist->SetSource($query);
	$dlist->Display();
}

?>