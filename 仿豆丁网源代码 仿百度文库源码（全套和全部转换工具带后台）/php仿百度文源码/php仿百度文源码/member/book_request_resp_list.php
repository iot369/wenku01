<?php
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
require_once(DEDEINC."/datalistcp.class.php");
require_once(DEDEMEMBER."/inc/inc_catalog_options.php");
$channelid = isset($channelid) && is_numeric($channelid) ? $channelid : 17;//书籍频道模型ID

//判断该任务是否已经选标
//首先判断是否有这个悬赏任务
$query = "Select status from `#@__member_request` where id='$reqid' ";
$arr = $dsql->GetOne($query);
$isSelected = ($arr["status"] == "1");//表示已选标
	
$query = "Select a.*, b.uname From `#@__member_response` a, `#@__member` b where a.mid = b.mid and a.reqid='$reqid' order by id desc";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("ftype",$ftype);
$dlist->SetTemplate(dirname(__FILE__).'/templets/book_request_resp_list.htm');
$dlist->SetSource($query);
$dlist->Display();

?>