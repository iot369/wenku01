<?php
require_once(dirname(__FILE__).'/config.php');
CheckRank(0,0);
$menutype = 'mydede';
$dopost = isset($dopost) ? trim($dopost) : '';
if($dopost == '')
{
	$tpl = new DedeTemplate();
	$tpl->LoadTemplate(DEDEMEMBER.'/templets/book_request_add.htm');
	$tpl->Display();
	exit();
}
elseif ($dopost == 'add')
{//发布悬赏
	$query = "insert into `#@__member_request`(mid, doctitle, score, remark, validate) values ('$cfg_ml->M_ID', '$doctitle', '$score', '$remark', $validate) ";
	//echo $query;
	if($dsql->ExecuteNoneQuery($query))
	{
		ShowMsg('增加悬赏任务成功', 'book_request_add.php');
	}
	else
	{
		ShowMsg('增加悬赏任务失败', '-1');
	}
	exit();
}elseif ($dopost == 'save')
{//应答悬赏
	
	ShowMsg('应标悬赏任务成功！','book_request_add.php');
	exit();
}elseif ($dopost == 'select')
{//选择应答
	
	ShowMsg('选择悬赏中标会员成功','book_request_add.php');
	exit();
}

?>