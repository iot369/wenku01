<?php
require_once(dirname(__FILE__)."/config.php");
//考虑安全原因不管是否开启游客投稿功能，都不允许用户投稿
CheckRank(0,0);
if($cfg_mb_lit=='Y')
{
	ShowMsg("由于系统开启了精简版会员空间，你访问的功能不可用！","-1");
	exit();
}
require_once(DEDEINC."/dedetag.class.php");
require_once(DEDEINC."/userlogin.class.php");
require_once(DEDEINC."/customfields.func.php");
require_once(DEDEMEMBER."/inc/inc_catalog_options.php");
require_once(DEDEMEMBER."/inc/inc_archives_functions.php");
$channelid = isset($channelid) && is_numeric($channelid) ? $channelid : 17;//书籍频道模型ID

/*-------------
function _ShowForm(){  }
--------------*/
if(empty($dopost))
{
	$cInfos = $dsql->GetOne("Select * From `#@__channeltype`  where id='$channelid'; ");
	if(!is_array($cInfos))
	{
		ShowMsg('模型不正确', '-1');
		exit();
	}

	//如果限制了会员级别或类型，则允许游客投稿选项无效
	if($cInfos['sendrank']>0 || $cInfos['usertype']!='')
	{
		CheckRank(0,0);
	}

	//检查会员等级和类型限制
	if($cInfos['sendrank'] > $cfg_ml->M_Rank)
	{
		$row = $dsql->GetOne("Select membername From `#@__arcrank` where rank='".$cInfos['sendrank']."' ");
		ShowMsg("对不起，需要[".$row['membername']."]才能在这个频道发布文档！","-1","0",5000);
		exit();
	}
	if($cInfos['usertype']!='' && $cInfos['usertype'] != $cfg_ml->M_MbType)
	{
		ShowMsg("对不起，需要[".$cInfos['usertype']."帐号]才能在这个频道发布文档！","-1","0",5000);
		exit();
	}
	include(DEDEMEMBER."/templets/book_add.htm");
	exit();
}
?>