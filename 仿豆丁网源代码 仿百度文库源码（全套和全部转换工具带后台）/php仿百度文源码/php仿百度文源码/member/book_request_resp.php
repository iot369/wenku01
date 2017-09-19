<?php
require_once(dirname(__FILE__).'/config.php');
CheckRank(0,0);
$menutype = 'mydede';
$dopost = isset($dopost) ? trim($dopost) : '';
if($dopost == '')
{
	//首先判断是否有这个悬赏任务
	$query = "Select a.*, b.uname from `#@__member_request` a, `#@__member` b where a.mid=b.mid and a.id='$reqid' ";
	$bookReq = $dsql->GetOne($query);
	if(!is_array($bookReq))
	{
		ShowMsg('对不起，您选择的悬赏任务不存在！', '-1');
		exit();
	}
	
	//首先用户是否投过稿
	$query = "Select * from `#@__member_response` where reqid='$reqid' and mid='$cfg_ml->M_ID'";
	$arr = $dsql->GetOne($query);
	if(is_array($arr))
	{
		ShowMsg('对不起，该任务您已经投过稿了，请勿重复投稿！', '-1');
		exit();
	}
	
	$tpl = new DedeTemplate();
	$tpl->LoadTemplate(DEDEMEMBER.'/templets/book_request_resp.htm');
	$tpl->Display();
	exit();
}

?>